<?php

namespace App\Filament\Pages;

use App\Models\UserSocialLink;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use JeffGreco13\FilamentBreezy\FilamentBreezy;

class MyProfile extends Page
{

    protected static string $view = 'filament.pages.my-profile';

    public $user;
    public $userData;
    public $socialLinkData;

    // Password
    public $new_password;
    public $new_password_confirmation;

    protected $loginColumn;

    public function boot()
    {
        // user column
        $this->loginColumn = config('filament-breezy.fallback_login_field');
    }

    protected function getBreadcrumbs(): array
    {
        return [
            url()->current() => __('filament-breezy::default.profile.profile'),
        ];
    }

    public function mount()
    {
        $this->user = Filament::auth()->user();
        // dd($this->user);
        $this->updateProfileForm->fill($this->user->toArray());

        $this->socialLink = UserSocialLink::where('user_id', $this->user->id)->first();
        if ( $this->socialLink ) {
            $this->updateUserSocialForm->fill($this->socialLink->toArray());
        }
    }

    protected static function getNavigationIcon(): string
    {
        return config('filament-breezy.profile_page_icon', 'heroicon-o-document-text');
    }
 
    protected static function getNavigationGroup(): ?string
    {
        return __('filament-breezy::default.profile.account');
    }
 
    public static function getNavigationLabel(): string
    {
        return __('filament-breezy::default.profile.profile');
    }
 
    protected function getTitle(): string
    {
        return __('filament-breezy::default.profile.my_profile');
    }
 
    protected static function shouldRegisterNavigation(): bool
    {
        return config('filament-breezy.show_profile_page_in_navbar');
    }

    protected function getForms(): array
    {
        return array_merge(parent::getForms(), [
            'updateProfileForm' => $this->makeForm()
                ->model(config('filament-breezy.user_model'))
                ->schema($this->getUpdateProfileFormSchema())
                ->statePath('userData'),
 
            'updateUserSocialForm' => $this->makeForm()
                ->model(config('filament-breezy.user_model'))
                ->schema($this->getUserSocialLinkFormSchema())
                ->statePath('socialLinkData'),
 
            'updatePasswordForm' => $this->makeForm()->schema(
                $this->getUpdatePasswordFormSchema()
            ),
        ]);
    }

    /**
     * User profile form schema
     */
    protected function getUpdateProfileFormSchema(): array
    {
        return [
            Forms\Components\FileUpload::make('avatar')
                ->label('Avatar')
                ->image()
                ->avatar()
                ->disk($this->user->avatarDisk())
                ->directory($this->user->avatarDirectory())
                ->preserveFilenames(),

            Forms\Components\TextInput::make('name')
                ->required()
                ->label(__('filament-breezy::default.fields.name')),

            Forms\Components\TextInput::make($this->loginColumn)
                ->required()
                ->email(fn () => $this->loginColumn === 'email')
                ->unique(config('filament-breezy.user_model'), ignorable: $this->user)
                ->label(__('filament-breezy::default.fields.email')),

            Forms\Components\MarkdownEditor::make('bio')
                ->maxLength(100)
                ->disableToolbarButtons([
                    'attachFiles',
                    'codeBlock',
                    'strike'
                ]),
        ];
    }

    /**
     * Update user profile
     */
    public function updateProfile()
    {
        $this->user->update($this->updateProfileForm->getState());

        $this->notify('success', __('Profile has been updated.'));
    }

    /**
     * User social link form schema
     */
    protected function getUserSocialLinkFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('github')
                // ->prefix('https://github.com/')
                ->maxLength(100),

            Forms\Components\TextInput::make('stackoverflow')
                // ->prefix('https://stackoverflow.com/')
                ->maxLength(100),

            Forms\Components\TextInput::make('twitter')
                // ->prefix('https://twitter.com/')
                ->maxLength(100),
        ];
    }

    /**
     * Update user social links
     */
    public function updateUserSocialDetails()
    {
        UserSocialLink::updateOrCreate(
            ['user_id' => $this->user->id],
            $this->updateUserSocialForm->getState()
        );
        $this->notify('success', __('Profile has been updated.'));
    }

    /**
     * Change password form schema
     */
    protected function getUpdatePasswordFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('new_password')
                ->label(__('filament-breezy::default.fields.new_password'))
                ->password()
                ->rules(app(FilamentBreezy::class)->getPasswordRules())
                ->required(),
            Forms\Components\TextInput::make('new_password_confirmation')
                ->label(__('filament-breezy::default.fields.new_password_confirmation'))
                ->password()
                ->same('new_password')
                ->required(),
        ];
    }

    /**
     * Update password
     */
    public function updatePassword()
    {
        $state = $this->updatePasswordForm->getState();
        $this->user->update([
            'password' => Hash::make($state['new_password']),
        ]);
        
        session()->forget('password_hash_web');

        Filament::auth()->login($this->user);

        $this->notify('success', __('filament-breezy::default.profile.password.notify'));
        $this->reset(['new_password', 'new_password_confirmation']);
    }
}
