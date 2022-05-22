<?php

namespace App\Http\Controllers\Designs;

use App\Jobs\UploadImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $this->validate($request, [
            'image' => ['required', 'mimes:png,jpeg,bmp,gif', 'max:12048'],
        ]);

        $image = $request->file('image');
        $imagePath = $image->getPathName();

        $filename = time() . '_'
            . str_replace(' ', '_', strtolower($image->getClientOriginalName()));

        $tmp = $image->storeAs('uploads/original', $filename, 'temp');

        $design = auth()->user()
            ->designs()
            ->create([
                'image' => $filename,
                'disk' => config('site.upload_disk'),
            ]);

        // dispatch a job to handle the image manipulation

        $this->dispatch(new UploadImage($design));

        return response()->json($design);
    }
}
