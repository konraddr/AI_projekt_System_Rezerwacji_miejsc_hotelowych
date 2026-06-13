<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PhotoFileController extends Controller
{
    public function show(Photo $photo): StreamedResponse
    {
        $disk = Storage::disk(config('photos.disk', 'public'));
        $path = $photo->storagePath();

        abort_unless($disk->exists($path), 404);

        return $disk->response($path);
    }
}
