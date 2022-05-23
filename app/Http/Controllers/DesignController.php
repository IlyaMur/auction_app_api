<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\DesignResource;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Eloquent\Criteria\IsLive;
use App\Repositories\Contracts\DesignInterface;
use App\Repositories\Eloquent\Criteria\LatestFirst;

class DesignController extends Controller
{
    public function __construct(protected DesignInterface $designs)
    {
    }

    public function index()
    {
        $designs = $this
            ->designs
            ->withCriteria(new LatestFirst(), new IsLive())
            ->all();

        return DesignResource::collection($designs);
    }

    public function update(Request $request, $id)
    {
        $design = $this->designs->find($id);

        $this->authorize($design);

        $this->validate($request, [
            'title' => ['required', "unique:designs,title,$design->id"],
            'description' => ['required', 'string', 'min:20', 'max:140'],
            'tags' => ['required',]
        ]);

        $design = $this->designs->update($id, [
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title),
            'is_live' => $design->upload_successful ? $request->is_live : false
        ]);

        // apply the tags
        $this->designs->applyTags($id, $request->tags);

        return new DesignResource($design);
    }

    public function destroy(Request $request, $id)
    {
        $design = $this->designs->find($id);

        $this->authorize($design);

        foreach (['thumbnail', 'large', 'original'] as $size) {
            $storage = Storage::disk($design->disk);
            $path = "uploads/designs/{$size}/{$design->image}";

            if ($storage->exists($path)) {
                $storage->delete($path);
            }
        }

        $this->designs->delete($id);

        return response()->json([
            'message' => 'Record deleted'
        ]);
    }

    public function findDesign($id)
    {
        return new DesignResource($this->designs->find($id));
    }
}