<?php


namespace App\Traits;


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait UploadImageTrait
{
    public function uploadOneImage(UploadedFile $uploadedFile, $folder = null, $disk = 'public', $imageName = null)
    {
        $nameImage = !is_null($imageName) ? $imageName : Str::random(25);

        $image = $uploadedFile->storeAs($folder, $nameImage . '.' . $uploadedFile->getClientOriginalExtension(), $disk);

        return $image;
    }
}
