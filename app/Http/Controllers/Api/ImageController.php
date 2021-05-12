<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UploadImageFormRequest;

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
