<?php

namespace App\Filament\Pages\Settings;

use Closure;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;

class Settings extends BaseSettings
{
    public function schema(): array|Closure
    {
        return [
            Tabs::make('Settings')
                ->schema([
                    Tabs\Tab::make('General')
                        ->schema([
                            TextInput::make('general.brand_name')
                                ->label('Brand Name')
                                ->required(),
                            TextInput::make('general.email')
                                ->label('Contact Email')
                                ->email()
                                ->required(),
                            TextInput::make('general.phone')
                                ->label('Phone Number')
                                ->tel(),
                        ]),
                    Tabs\Tab::make('SEO')
                        ->schema([
                            TextInput::make('seo.title')
                                ->label('Site Title')
                                ->required(),
                            Textarea::make('seo.description')
                                ->label('Site Description')
                                ->required()
                                ->rows(3),
                            TextInput::make('seo.keywords')
                                ->label('Meta Keywords')
                                ->helperText('Separate keywords with commas'),
                        ]),
                    Tabs\Tab::make('Social Media')
                        ->schema([
                            TextInput::make('social.facebook')
                                ->label('Facebook URL')
                                ->url(),
                            TextInput::make('social.twitter')
                                ->label('Twitter URL')
                                ->url(),
                            TextInput::make('social.instagram')
                                ->label('Instagram URL')
                                ->url(),
                            TextInput::make('social.linkedin')
                                ->label('LinkedIn URL')
                                ->url(),
                        ]),
                ]),
        ];
    }
}
