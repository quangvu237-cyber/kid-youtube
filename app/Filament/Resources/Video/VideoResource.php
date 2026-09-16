<?php

namespace App\Filament\Resources\Video;

use App\Filament\Resources\Video\Pages\CreateVideo;
use App\Filament\Resources\Video\Pages\EditVideo;
use App\Filament\Resources\Video\Pages\ListVideo;
use App\Filament\Resources\Video\Schemas\VideoForm;
use App\Filament\Resources\Video\Tables\VideoTable;
use App\Models\Video;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VideoResource extends Resource
{
    protected static ?string $model = Video::class;

    protected static ?string $navigationIcon = 'heroicon-o-video-camera';

    protected static ?string $navigationGroup = 'Quản lý nội dung';

    protected static ?string $navigationLabel = 'Video YouTube';

    protected static ?string $modelLabel = 'Video';

    protected static ?string $pluralModelLabel = 'Video YouTube';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return VideoForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return VideoTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVideo::route('/'),
            'create' => CreateVideo::route('/create'),
            'edit' => EditVideo::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderByDesc('id');
    }
}
