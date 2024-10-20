<?php

namespace WordSphere\Core\Application\Factories\ContentManagement;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use WordSphere\Core\Infrastructure\ContentManagement\Persistence\Models\EloquentMedia;

class MediaFactory extends Factory
{
    protected $model = EloquentMedia::class;

    /**
     * {@inheritDoc}
     */
    public function definition(): array
    {
        $width = $this->faker->numberBetween(800, 1920);
        $height = $this->faker->numberBetween(600, 1000);
        $fileName = $this->faker->uuid().'.jpg';

        // Generate a random image
        $image = $this->generateImage($width, $height);

        // Save the image to storage
        Storage::disk('public')->put('media/'.$fileName, $image->encode('jpg'));

        return [
            'name' => $fileName,
            'path' => 'media/'.$fileName,
            'ext' => 'jpg',
            'type' => 'image/jpeg',
            'size' => $image->filesize(),
            'width' => $width,
            'height' => $height,
            'alt' => $this->faker->sentence,
            'caption' => $this->faker->paragraph,
        ];

    }

    private function generateImage(int $width, int $height): \Intervention\Image\Image
    {

        $image = Image::canvas($width, $height, $this->faker->hexColor());

        for ($i = 0; $i < 5; $i++) {
            $image->circle(
                $this->faker->numberBetween(50, 200),
                $this->faker->numberBetween(0, $width),
                $this->faker->numberBetween(0, $height),
                function ($draw): void {
                    $draw->background($this->faker->hexColor());
                }
            );
        }

        $image->text('Curator Media', $width / 2, $height / 2, function ($font): void {
            $font->size(36);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('middle');
        });

        return $image;

    }
}
