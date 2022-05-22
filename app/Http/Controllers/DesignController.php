<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Design;

class DesignController extends Controller
{
    public function update(Request $request, Design $design)
    {
        $this->authorize($design);

        $this->validate($request, [
            'title' => ['required', "unique:designs,title,$design->id"],
            'description' => ['required', 'string', 'min:20', 'max:140'],
        ]);

        $design->update([
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title),
            'is_live' => !$design->upload_successful ? false : $request->is_live
        ]);

        return response()->json($design);
    }
}
