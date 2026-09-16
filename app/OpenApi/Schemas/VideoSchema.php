<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'Video')]
class VideoSchema
{
    #[OA\Property(property: 'id', type: 'integer', example: 1)]
    public int $id;

    #[OA\Property(property: 'youtube_id', type: 'string', example: 'dQw4w9WgXcQ')]
    public string $youtube_id;

    #[OA\Property(property: 'youtube_url', type: 'string', format: 'uri', example: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ')]
    public string $youtube_url;

    #[OA\Property(property: 'title', type: 'string', nullable: true, example: 'Rick Astley - Never Gonna Give You Up')]
    public ?string $title;

    #[OA\Property(property: 'description', type: 'string', nullable: true)]
    public ?string $description;

    #[OA\Property(property: 'thumbnail', type: 'string', format: 'uri', nullable: true)]
    public ?string $thumbnail;

    #[OA\Property(property: 'published_at', type: 'string', format: 'date-time', nullable: true)]
    public ?string $published_at;

    #[OA\Property(property: 'channel', type: 'string', nullable: true, example: 'Rick Astley')]
    public ?string $channel;

    #[OA\Property(property: 'channel_id', type: 'string', nullable: true)]
    public ?string $channel_id;

    #[OA\Property(property: 'channel_avatar', type: 'string', format: 'uri', nullable: true)]
    public ?string $channel_avatar;

    #[OA\Property(property: 'channel_subscriber_count', type: 'integer', nullable: true)]
    public ?int $channel_subscriber_count;

    #[OA\Property(property: 'view_count', type: 'integer', nullable: true)]
    public ?int $view_count;

    #[OA\Property(property: 'like_count', type: 'integer', nullable: true)]
    public ?int $like_count;

    #[OA\Property(property: 'comment_count', type: 'integer', nullable: true)]
    public ?int $comment_count;

    #[OA\Property(property: 'duration', type: 'string', nullable: true, example: '3:33')]
    public ?string $duration;

    #[OA\Property(property: 'tags', type: 'array', items: new OA\Items(type: 'string'), nullable: true)]
    public ?array $tags;

    #[OA\Property(property: 'category_id', type: 'string', nullable: true)]
    public ?string $category_id;

    #[OA\Property(property: 'category_name', type: 'string', nullable: true, example: 'Music')]
    public ?string $category_name;

    #[OA\Property(property: 'status', type: 'string', enum: ['pending', 'completed', 'failed'], example: 'completed')]
    public string $status;

    #[OA\Property(property: 'comments_count', type: 'integer', example: 0)]
    public int $comments_count;

    #[OA\Property(property: 'embed_url', type: 'string', format: 'uri', nullable: true, example: 'https://www.youtube.com/embed/dQw4w9WgXcQ')]
    public ?string $embed_url;

    #[OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true)]
    public ?string $created_at;

    #[OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true)]
    public ?string $updated_at;
}
