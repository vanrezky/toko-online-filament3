<?php

namespace App\Traits;

use App\Constants\UploadPath;
use Illuminate\Support\Facades\Storage;

trait HasProfilePictureTrait
{
    public function getProfilePhotoUrlAttribute(): string
    {
        return $this->image
            ? Storage::disk($this->profilePhotoDisk())->url($this->image)
            : $this->defaultProfilePhotoUrl();
    }

    public function updateProfilePhoto(null|string $photo): void
    {
        tap($this->image, function ($previous) use ($photo) {
            $this->forceFill([
                'image' => $photo,
            ])->save();

            if ($previous && !$photo) {
                Storage::disk($this->profilePhotoDisk())->delete($previous);
            }
        });
    }

    protected function defaultProfilePhotoUrl(): string
    {
        $name = trim(collect(explode(' ', $this->name))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=7F9CF5&background=EBF4FF';
    }

    public function profilePhotoDisk(): string
    {
        return isset($_ENV['VAPOR_ARTIFACT_NAME']) ? 's3' : config('your-config.profile_photo_disk', 'public');
    }

    public function profilePhotoDirectory(): string
    {
        return config(UploadPath::PROFILE_UPLOAD_PATH, 'profile-photos');
    }
}
