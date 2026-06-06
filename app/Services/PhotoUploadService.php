<?php

namespace App\Services;

use App\Models\Photo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoUploadService
{
    public function upload(UploadedFile $file, Model $imageable, int $order = 0): Photo
    {
        $fileType = strtolower($file->getClientOriginalExtension());
        $filename = (string) Str::uuid();

        $path = $this->storagePath($filename, $fileType);

        Storage::disk($this->disk())->put($path, $file->get());

        return $imageable->photos()->create([
            'filename' => $filename,
            'file_type' => $fileType,
            'order' => $order,
        ]);
    }

    public function delete(Photo $photo): void
    {
        if (Storage::disk($this->disk())->exists($photo->storagePath())) {
            Storage::disk($this->disk())->delete($photo->storagePath());
        }

        $photo->delete();
    }

    public function storagePath(string $filename, string $fileType): string
    {
        return $this->directory().'/'.$filename.'.'.$fileType;
    }

    public function disk(): string
    {
        return (string) config('photos.disk', 'public');
    }

    public function directory(): string
    {
        return trim((string) config('photos.directory', 'photos'), '/');
    }
}
