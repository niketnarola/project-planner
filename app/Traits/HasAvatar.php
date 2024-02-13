<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HasAvatar
{
    public function getAvatarUrlAttribute(): string
    {
        return $this->profile_photo_path
            ? Storage::disk($this->avatarDisk())->url($this->profile_photo_path)
            : $this->defaultAvatarUrl();
    }
 
    public function updateAvatar(null|string $avatar): void
    {
        tap($this->avatar, function ($previous) use ($avatar) {
            $this->forceFill([
                'avatar' => $avatar,
            ])->save();
 
            if ($previous && ! $avatar) {
                Storage::disk($this->avatarDisk())->delete($previous);
            }
        });
    }
 
    protected function defaultAvatarUrl(): string
    {
        $name = trim(collect(explode(' ', $this->name))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));
 
        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';
    }
 
    public function avatarDisk(): string
    {
        return isset($_ENV['VAPOR_ARTIFACT_NAME']) ? 's3' : config('your-config.profile_photo_disk', 'public');
    }
 
    public function avatarDirectory(): string
    {
        return config('your-config.profile_photo_directory', 'avatars');
    }
}