<?php

namespace App\Http\Livewire\Auth;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Auth\Events\Registered;
use JeffGreco13\FilamentBreezy\Http\Livewire\Auth\Register as BaseRegister;

class Register extends BaseRegister
{
    public function register()
    {
        $preparedData = $this->prepareModelData($this->form->getState());

        $user = config('filament-breezy.user_model')::create($preparedData);

        $user->assignRole(User::ROLE_TRAINEE_SOFTWARE_ENGINEER);

        event(new Registered($user));
        Filament::auth()->login($user, true);

        return redirect()->to(config('filament-breezy.registration_redirect_url'));
    }
}
