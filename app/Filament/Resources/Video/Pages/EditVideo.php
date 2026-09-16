<?php

namespace App\Filament\Resources\Video\Pages;

use App\Filament\Resources\Video\VideoResource;
use App\Services\VideoService;
use Filament\Resources\Pages\EditRecord;

class EditVideo extends EditRecord
{
    protected static string $resource = VideoResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $service = app(VideoService::class);

        if (! empty($data['youtube_url'])) {
            $data['youtube_id'] = $service->extractYoutubeId($data['youtube_url']);
        }

        return $data;
    }
}
