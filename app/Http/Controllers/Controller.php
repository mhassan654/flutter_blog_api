<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function saveImage($image, $path = 'public')
    {
        if(!$image)
        {
            return null;
        }

        $filename = time() .'.png';

        // $file_name = time() . '_' . str_replace(' ', '', $image->getClientOriginalName());

        // $this->upload_file($file_name, $image, 'posts');
        //save image
        Storage::disk($path)->put($filename, base64_decode($image));

        // return the path
        return URL::to('/').'/storage/'.$path.'/'.$filename;
    }

    private function upload_file($name, $file, $folder = null): void
    {
        $file->storeAs("$folder", $name, 'local');
    }
}
