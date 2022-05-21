<?php

namespace App\Jobs;

use App\Models\Models\Design;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class UploadImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $design;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Design $design)
    {
        $this->design = $design;
    }

    protected function resizeAndSave($originalFile, $pathToSave, $resolutin)
    {
        Image::make($originalFile)
            ->fit(800, 600, function ($constraint) {
                $constraint->aspectRatio();
            })
            ->save($large = storage_path(
                $pathToSave . $this->design->image
            ));
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $disk = $this->design->disk;
        $filename = $this->design->image;

        $originalFile = storage_path()
            . '/uploads/original/'
            . $this->design->image;
        try {
            // create the large image and save to tmp disk
            Image::make($originalFile)
                ->fit(800, 600, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save($large = storage_path(
                    'uploads/large' . $filename
                ));

            // create the thumbnail image and save to tmp disk
            Image::make($originalFile)
                ->fit(250, 200, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save($thumbnail = storage_path(
                    'uploads/thumbnail' . $filename
                ));

            // store images to permanent location
            if (Storage::disk($disk)->put('uploads/designs/original/' . $filename, fopen($originalFile, 'r+'))) {
                File::delete($originalFile);
            }

            // large images
            if (Storage::disk($disk)->put('uploads/designs/large/' . $filename, fopen($large, 'r+'))) {
                File::delete($large);
            }

            // thumbnail images
            if (Storage::disk($disk)->put('uploads/designs/thumbnail/' . $filename, fopen($thumbnail, 'r+'))) {
                File::delete($thumbnail);
            }

            // Update the db record
            $this->design->update(['upload_successful' => true]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }
}
