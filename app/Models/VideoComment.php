<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VideoComment extends Model
{
    protected $table = 'youtube_video_comments';

    protected $fillable = [
        'video_id',
        'youtube_comment_id',
        'parent_youtube_comment_id',
        'author_name',
        'author_avatar',
        'text',
        'like_count',
        'published_at',
    ];

    protected $casts = [
        'like_count' => 'integer',
        'published_at' => 'datetime',
    ];

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class, 'video_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_youtube_comment_id', 'youtube_comment_id');
    }
}
