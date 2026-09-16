<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Video extends Model
{
    use HasFactory;

    protected $table = 'youtube_videos';

    protected $fillable = [
        'youtube_id',
        'youtube_url',
        'title',
        'description',
        'channel',
        'channel_id',
        'channel_avatar',
        'channel_subscriber_count',
        'thumbnail',
        'view_count',
        'like_count',
        'comment_count',
        'duration',
        'tags',
        'category_id',
        'category_name',
        'published_at',
        'status',
    ];

    protected $casts = [
        'tags' => 'array',
        'published_at' => 'datetime',
        'view_count' => 'integer',
        'like_count' => 'integer',
        'comment_count' => 'integer',
        'channel_subscriber_count' => 'integer',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Video $video) {
            $video->comments()->delete();
        });
    }

    public function comments(): HasMany
    {
        return $this->hasMany(VideoComment::class, 'video_id');
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        $thumb = $this->thumbnail;

        if (empty($thumb)) {
            return $this->youtube_id
                ? 'https://i.ytimg.com/vi/'.$this->youtube_id.'/hqdefault.jpg'
                : null;
        }

        if (filter_var($thumb, FILTER_VALIDATE_URL) !== false) {
            return $thumb;
        }

        return Storage::disk('public')->url($thumb);
    }

    public function getEmbedUrlAttribute(): ?string
    {
        return $this->youtube_id
            ? 'https://www.youtube.com/embed/'.$this->youtube_id.'?modestbranding=1&rel=0&iv_load_policy=3&playsinline=1&enablejsapi=1'
            : null;
    }
}
