<?php

namespace App\Filament\Resources\Video\Schemas;

use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class VideoForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Thông tin video')
                    ->schema([
                        TextInput::make('youtube_url')
                            ->label('Link YouTube')
                            ->url()
                            ->required()
                            ->live()
                            ->helperText('Dán link YouTube (watch, youtu.be, embed, shorts, live). Khi lưu tự trích ID + lấy metadata, statistics, comments.')
                            ->columnSpanFull(),
                        TextInput::make('youtube_id')
                            ->label('YouTube ID')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Tự trích xuất từ link khi lưu.'),
                        TextInput::make('title')
                            ->label('Tiêu đề')
                            ->maxLength(255)
                            ->helperText('Để trống để tự lấy từ YouTube.')
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('Mô tả')
                            ->disabled()
                            ->rows(4)
                            ->columnSpanFull(),
                        TextInput::make('channel')
                            ->label('Kênh')
                            ->disabled(),
                        Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                'pending' => 'Chờ',
                                'completed' => 'Hoàn thành',
                                'failed' => 'Lỗi',
                            ])
                            ->default('pending')
                            ->required(),
                    ])->columns(2),
                Section::make('Thống kê & metadata')
                    ->schema([
                        TextInput::make('view_count')
                            ->label('Lượt xem')
                            ->disabled()
                            ->formatStateUsing(fn ($state) => is_numeric($state) ? number_format((float) $state) : null),
                        TextInput::make('like_count')
                            ->label('Lượt thích')
                            ->disabled()
                            ->formatStateUsing(fn ($state) => is_numeric($state) ? number_format((float) $state) : null),
                        TextInput::make('comment_count')
                            ->label('Số bình luận')
                            ->disabled()
                            ->formatStateUsing(fn ($state) => is_numeric($state) ? number_format((float) $state) : null),
                        TextInput::make('duration')
                            ->label('Thời lượng')
                            ->disabled(),
                        TextInput::make('channel_subscriber_count')
                            ->label('Lượt đăng ký kênh')
                            ->disabled()
                            ->formatStateUsing(fn ($state) => is_numeric($state) ? number_format((float) $state) : null),
                        TextInput::make('category_name')
                            ->label('Danh mục')
                            ->disabled(),
                        TextInput::make('published_at')
                            ->label('Thời gian đăng (YouTube)')
                            ->disabled()
                            ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('d/m/Y H:i') : null),
                        Placeholder::make('tags')
                            ->label('Tags')
                            ->content(fn ($record) => collect($record->tags ?? [])->join(', ') ?: '—'),
                    ])->columns(3)
                    ->collapsible(),
                Section::make('Ảnh thu nhỏ')
                    ->schema([
                        FileUpload::make('thumbnail')
                            ->label('Ảnh thu nhỏ')
                            ->image()
                            ->disk('public')
                            ->directory('videos/thumbnails')
                            ->fetchFileInformation(false)
                            ->imagePreviewHeight('150')
                            ->getUploadedFileUsing(function (BaseFileUpload $component, string $file, string|array|null $storedFileNames): ?array {
                                $storage = $component->getDisk();

                                try {
                                    if (! $storage->exists($file)) {
                                        return null;
                                    }
                                } catch (\Throwable $e) {
                                    return null;
                                }

                                $url = $storage instanceof \Illuminate\Filesystem\FilesystemAdapter
                                    ? $storage->url($file)
                                    : null;

                                return [
                                    'name' => basename($file),
                                    'size' => 0,
                                    'type' => null,
                                    'url' => $url,
                                ];
                            })
                            ->saveUploadedFileUsing(function (BaseFileUpload $component, TemporaryUploadedFile $file): ?string {
                                try {
                                    if (! $file->exists()) {
                                        return null;
                                    }
                                } catch (\Throwable $e) {
                                    return null;
                                }

                                $extension = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg';
                                $filename = Str::ulid().'.'.$extension;

                                return $file->storePubliclyAs(
                                    $component->getDirectory(),
                                    $filename,
                                    $component->getDiskName(),
                                );
                            })
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
