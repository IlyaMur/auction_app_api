<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\DesignResource;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Eloquent\Criteria\IsLive;
use App\Repositories\Contracts\DesignInterface;
use App\Repositories\Eloquent\Criteria\ForUser;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\LatestFirst;

class DesignController extends Controller
{
    public function __construct(protected DesignInterface $designs)
    {
    }

    public function index()
    {
        $designs = $this->designs
            ->withCriteria(
                new LatestFirst(),
                new EagerLoad(['user', 'comments'])
            )
            ->all();

        return DesignResource::collection($designs);
    }

    public function update(Request $request, $id)
    {
        $design = $this->designs->find($id);

        $this->authorize($design);

        $this->validate($request, [
            'title' => ['required', "unique:designs,title,{$design->id}"],
            'description' => ['required', 'string', 'min:20', 'max:140'],
            'team' => ['required_if:assign_to_team,true']
        ]);

        $design = $this->designs->update($id, [
            'team_id' => $request->team,
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
            $file = "uploads/designs/{$size}/{$design->image}";

            if ($storage->exists($file)) {
                $storage->delete($file);
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

    public function like($id)
    {
        $totalLikes = $this->designs->like($id);

        return response()->json(['message' => "Successful", "total" => $totalLikes]);
    }

    public function checkIfUserHasLiked($id)
    {
        $isLiked = $this->designs->isLikedByUser($id);

        return response()->json(['liked' => $isLiked]);
    }

    public function search(Request $request)
    {
        $designs = $this->designs
            ->search($request)
            ->paginate(12)
            ->withQueryString();

        return DesignResource::collection($designs);
    }

    public function findBySlug($slug)
    {
        $design = $this->designs
            ->withCriteria(new IsLive(), new EagerLoad('comments', 'user'))
            ->findWhereFirst('slug', $slug)
            ->addView();

        return new DesignResource($design);
    }

    public function findByTag($tag)
    {
        $designs = $this->designs
            ->withCriteria(new IsLive(), new EagerLoad('comments', 'user'))
            ->findByTag($tag)
            ->paginate(12);

        return DesignResource::collection($designs);
    }

    public function getForTeam($teamId)
    {
        return DesignResource::collection(
            $this->designs
                ->withCriteria(new IsLive())
                ->findWhere('team_id', $teamId)
        );
    }

    public function getForUser($userId)
    {
        return DesignResource::collection(
            $this->designs
                ->withCriteria(new EagerLoad())
                ->findWhere('user_id', $userId)
        );
    }

    public function getForPreview($userId)
    {
        return DesignResource::collection(
            $this->designs
                ->withCriteria(new EagerLoad(), new IsLive())
                ->findWhere('user_id', $userId, 4)
        );
    }

    public function userOwnsDesign($id)
    {
        $design = $this->designs
            ->withCriteria(new ForUser(auth()->id()))
            ->findWhereFirst('id', $id);

        return new DesignResource($design);
    }
}
