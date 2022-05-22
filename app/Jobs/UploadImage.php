<?php

namespace App\Jobs;

use App\Models\Design;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class UploadImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $design;
    protected $disk;
    protected $filename;
    protected $originalFile;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Design $design)
    {
        $this->design = $design;
        $this->disk = $design->disk;
        $this->filename = $design->image;

        $this->originalFile = storage_path()
            . '/uploads/original/' . $design->image;
    }

    protected function resizeAndStore($width, $height, $size)
    {
        $pathToSave = storage_path(
            "uploads/{$size}/" . $this->filename
        );

        Image::make($this->originalFile)
            ->fit($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            })
            ->save($pathToSave);

        $this->storeFile($size, $pathToSave);
    }

    protected function storeFile($size, $file)
    {
        if (Storage::disk($this->disk)->put(
            "uploads/designs/{$size}/" . $this->filename,
            fopen($file, 'r+')
        )) {
            File::delete($file);
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // create the large image and save to tmp disk
            $this->resizeAndStore(800, 600, 'large');
            // create the thumbnail
            $this->resizeAndStore(250, 200, 'thumbnail');
            // save the original
            $this->storeFile('original', $this->originalFile);

            // Update the db record
            $this->design->update(['upload_successful' => true]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }
}
