<?php

namespace App\Filament\Resources\Video\Tables;

use App\Models\Video;
use App\Services\VideoService;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class VideoTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('Ảnh')
                    ->circular()
                    ->size(48)
                    ->getStateUsing(function (Video $record) {
                        $thumb = $record->thumbnail;

                        if (empty($thumb)) {
                            return $record->youtube_id
                                ? 'https://i.ytimg.com/vi/'.$record->youtube_id.'/hqdefault.jpg'
                                : null;
                        }

                        if (filter_var($thumb, FILTER_VALIDATE_URL) !== false) {
                            return $thumb;
                        }

                        return Storage::disk('public')->url($thumb);
                    }),
                TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable()
                    ->limit(60)
                    ->wrap(),
                TextColumn::make('youtube_id')
                    ->label('YouTube ID')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Đã sao chép ID'),
                TextColumn::make('channel')
                    ->label('Kênh')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('view_count')
                    ->label('Lượt xem')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('like_count')
                    ->label('Lượt thích')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('comment_count')
                    ->label('Bình luận')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('duration')
                    ->label('Thời lượng')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('category_name')
                    ->label('Danh mục')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('published_at')
                    ->label('Đăng lúc')
                    ->dateTime('d/m/Y H:i')
                    ->timezone('Asia/Ho_Chi_Minh')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->colors([
                        'pending' => 'warning',
                        'completed' => 'success',
                        'failed' => 'danger',
                    ]),
                TextColumn::make('created_at')
                    ->label('Tạo lúc')
                    ->dateTime('d/m/Y H:i')
                    ->timezone('Asia/Ho_Chi_Minh')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'pending' => 'Chờ',
                        'completed' => 'Hoàn thành',
                        'failed' => 'Lỗi',
                    ]),
            ])
            ->actions([
                Action::make('refresh')
                    ->label('Làm mới')
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->action(function (Video $record) {
                        app(VideoService::class)->refreshVideo($record->id);
                        Notification::make()
                            ->success()
                            ->title('Đã làm mới dữ liệu video')
                            ->send();
                    }),
                EditAction::make()->label('Sửa'),
                DeleteAction::make()->label('Xoá'),
            ]);
    }
}
