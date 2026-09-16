<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'VideoComment')]
class VideoCommentSchema
{
    #[OA\Property(property: 'id', type: 'integer', example: 1)]
    public int $id;

    #[OA\Property(property: 'youtube_comment_id', type: 'string')]
    public string $youtube_comment_id;

    #[OA\Property(property: 'author_name', type: 'string', example: 'Người xem')]
    public string $author_name;

    #[OA\Property(property: 'author_avatar', type: 'string', format: 'uri', nullable: true)]
    public ?string $author_avatar;

    #[OA\Property(property: 'text', type: 'string', nullable: true)]
    public ?string $text;

    #[OA\Property(property: 'like_count', type: 'integer', example: 12)]
    public int $like_count;

    #[OA\Property(property: 'published_at', type: 'string', format: 'date-time', nullable: true)]
    public ?string $published_at;

    #[OA\Property(property: 'replies', type: 'array', items: new OA\Items(ref: '#/components/schemas/VideoComment'))]
    public array $replies;
}
