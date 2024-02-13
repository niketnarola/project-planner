<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data['password'] = Str::random(8);
        $user = static::getModel()::create($data);

        // send reset password link
        $token = Password::getRepository()->create($user);
        $user->sendPasswordResetNotification($token);

        return $user;
    }
}
