<?php

namespace App\Filament\Resources\Video\Pages;

use App\Filament\Resources\Video\VideoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVideo extends ListRecords
{
    protected static string $resource = VideoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Thêm video'),
        ];
    }
}
