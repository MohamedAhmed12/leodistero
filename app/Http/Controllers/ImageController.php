<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadImageFormRequest;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function upload(UploadImageFormRequest $request)
    {
        $data = $request->validated();
        $imagePath = $data['image']->store("public/images");
        return [
            'imagePath' => asset(Storage::url($imagePath))
        ];
    }
}
