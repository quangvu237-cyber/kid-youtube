<?php

namespace App\Filament\Resources\Video\Pages;

use App\Filament\Resources\Video\VideoResource;
use App\Services\VideoService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class CreateVideo extends CreateRecord
{
    protected static string $resource = VideoResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        try {
            return app(VideoService::class)->createModel($data);
        } catch (RuntimeException $e) {
            throw ValidationException::withMessages(['youtube_url' => $e->getMessage()]);
        }
    }
}
