<?php

namespace Database\Seeders;

use App\Models\Hotel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoSeeder extends Seeder
{
    /**
     * Przykładowe miniatury 1×1 px — uruchom: sail artisan db:seed --class=PhotoSeeder
     */
    public function run(): void
    {
        $disk = Storage::disk(config('photos.disk', 'public'));
        $directory = trim((string) config('photos.directory', 'photos'), '/');
        $placeholderPng = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
        );

        Hotel::query()->with('rooms')->each(function (Hotel $hotel) use ($disk, $directory, $placeholderPng): void {
            if ($hotel->photos()->doesntExist()) {
                $this->seedPlaceholderPhoto($hotel, $disk, $directory, $placeholderPng, 0);
            }

            foreach ($hotel->rooms as $index => $room) {
                if ($room->photos()->exists()) {
                    continue;
                }

                $this->seedPlaceholderPhoto($room, $disk, $directory, $placeholderPng, $index);
            }
        });
    }

    private function seedPlaceholderPhoto($imageable, $disk, string $directory, string $contents, int $order): void
    {
        $filename = (string) Str::uuid();
        $fileType = 'png';
        $path = $directory.'/'.$filename.'.'.$fileType;

        $disk->put($path, $contents);

        $imageable->photos()->create([
            'filename' => $filename,
            'file_type' => $fileType,
            'order' => $order,
        ]);
    }
}
