<?php

namespace App\Services;

use App\Models\Video;
use App\Models\VideoComment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class VideoService
{
    public function list(int $perPage = 15): array
    {
        return $this->paginatedPayload(
            Video::query()->orderByDesc('id')->paginate($perPage)
        );
    }

    public function find(int $id): array
    {
        return $this->formatPayload(Video::findOrFail($id));
    }

    public function create(array $data): array
    {
        return $this->formatPayload($this->createModel($data));
    }

    public function createModel(array $data): Video
    {
        $youtubeUrl = $data['youtube_url'];
        $youtubeId = $this->extractYoutubeId($youtubeUrl);

        if ($youtubeId === null) {
            throw new RuntimeException('Không thể trích xuất YouTube ID từ URL cung cấp.');
        }

        $videoData = $this->fetchVideoData($youtubeId);

        $attributes = array_merge(
            [
                'youtube_id' => $youtubeId,
                'youtube_url' => $youtubeUrl,
            ],
            $videoData ?? [],
        );

        if (! empty($data['title'])) {
            $attributes['title'] = $data['title'];
        }

        $attributes['status'] = $videoData !== null ? 'completed' : 'failed';

        $video = Video::create($attributes);

        if ($videoData !== null) {
            $this->syncComments($video);
        }

        return $video->fresh();
    }

    public function update(int $id, array $data): array
    {
        $video = Video::findOrFail($id);

        $fillable = array_intersect_key($data, array_flip([
            'youtube_url',
            'youtube_id',
            'title',
            'channel',
            'thumbnail',
            'status',
            'description',
            'tags',
        ]));

        if (isset($fillable['youtube_url']) && ! isset($fillable['youtube_id'])) {
            $youtubeId = $this->extractYoutubeId($fillable['youtube_url']);
            if ($youtubeId !== null) {
                $fillable['youtube_id'] = $youtubeId;
            }
        }

        $video->fill($fillable)->save();

        return $this->formatPayload($video->fresh());
    }

    public function delete(int $id): bool
    {
        return (bool) Video::findOrFail($id)->delete();
    }

    public function refreshVideo(int $id): array
    {
        $video = Video::findOrFail($id);

        if (empty($video->youtube_id)) {
            throw new RuntimeException('Video chưa có youtube_id để làm mới.');
        }

        $videoData = $this->fetchVideoData($video->youtube_id);

        if ($videoData !== null) {
            $video->fill($videoData)->save();
            $video->status = 'completed';
            $video->save();
            $this->syncComments($video);
        } else {
            $video->status = 'failed';
            $video->save();
        }

        return $this->formatPayload($video->fresh());
    }

    public function getCommentThreads(int $videoId, int $perPage = 20)
    {
        return VideoComment::query()
            ->where('video_id', $videoId)
            ->whereNull('parent_youtube_comment_id')
            ->orderByDesc('published_at')
            ->paginate($perPage);
    }

    public function formatPayload(Video $video): array
    {
        return [
            'id' => $video->id,
            'youtube_id' => $video->youtube_id,
            'youtube_url' => $video->youtube_url,
            'title' => $video->title,
            'description' => $video->description,
            'thumbnail' => $video->thumbnail,
            'published_at' => $video->published_at?->toIso8601String(),
            'channel' => $video->channel,
            'channel_id' => $video->channel_id,
            'channel_avatar' => $video->channel_avatar,
            'channel_subscriber_count' => $video->channel_subscriber_count,
            'view_count' => $video->view_count,
            'like_count' => $video->like_count,
            'comment_count' => $video->comment_count,
            'duration' => $video->duration,
            'tags' => $video->tags ?? [],
            'category_id' => $video->category_id,
            'category_name' => $video->category_name,
            'status' => $video->status,
            'comments_count' => $video->comments()->count(),
            'embed_url' => $video->youtube_id ? 'https://www.youtube.com/embed/'.$video->youtube_id : null,
            'created_at' => $video->created_at?->toIso8601String(),
            'updated_at' => $video->updated_at?->toIso8601String(),
        ];
    }

    public function formatComment(VideoComment $comment): array
    {
        return [
            'id' => $comment->id,
            'youtube_comment_id' => $comment->youtube_comment_id,
            'author_name' => $comment->author_name,
            'author_avatar' => $comment->author_avatar,
            'text' => $comment->text,
            'like_count' => $comment->like_count,
            'published_at' => $comment->published_at?->toIso8601String(),
        ];
    }

    public function formatCommentWithReplies(VideoComment $comment): array
    {
        $payload = $this->formatComment($comment);
        $payload['replies'] = VideoComment::query()
            ->where('parent_youtube_comment_id', $comment->youtube_comment_id)
            ->orderBy('published_at')
            ->get()
            ->map(fn (VideoComment $reply) => $this->formatComment($reply))
            ->values()
            ->all();

        return $payload;
    }

    public function extractYoutubeId(string $url): ?string
    {
        $patterns = [
            '/youtube\.com\/watch\?.*v=([a-zA-Z0-9_-]{11})/',
            '/youtu\.be\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/shorts\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/live\/([a-zA-Z0-9_-]{11})/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        if (preg_match('/[?&]v=([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function fetchVideoData(string $youtubeId): ?array
    {
        $apiKey = config('services.youtube.api_key');
        $baseUrl = rtrim((string) config('services.youtube.data_api_base_url'), '/');

        if (empty($apiKey)) {
            Log::warning('YouTube Data API key chưa được cấu hình (YOUTUBE_API_KEY).');

            return null;
        }

        try {
            $response = Http::timeout(20)->get("{$baseUrl}/videos", [
                'part' => 'snippet,statistics,contentDetails,topicDetails',
                'id' => $youtubeId,
                'key' => $apiKey,
            ]);

            if (! $response->successful()) {
                Log::error('YouTube Data API (/videos) trả về lỗi', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $item = $response->json('items.0');
            if (empty($item)) {
                return null;
            }

            $snippet = $item['snippet'] ?? [];
            $statistics = $item['statistics'] ?? [];
            $contentDetails = $item['contentDetails'] ?? [];
            $thumbnails = $snippet['thumbnails'] ?? [];

            $channelId = $snippet['channelId'] ?? null;
            $channelAvatar = null;
            $subscriberCount = null;

            if ($channelId) {
                $channel = $this->fetchChannel($channelId);
                if ($channel !== null) {
                    $channelAvatar = $channel['avatar'];
                    $subscriberCount = $channel['subscriber_count'];
                }
            }

            $categoryId = $snippet['categoryId'] ?? null;
            $categoryName = $categoryId ? $this->fetchCategoryName($categoryId) : null;

            return [
                'title' => $snippet['title'] ?? null,
                'description' => $snippet['description'] ?? null,
                'published_at' => $snippet['publishedAt'] ?? null,
                'channel' => $snippet['channelTitle'] ?? null,
                'channel_id' => $channelId,
                'channel_avatar' => $channelAvatar,
                'channel_subscriber_count' => $subscriberCount,
                'view_count' => $statistics['viewCount'] ?? null,
                'like_count' => $statistics['likeCount'] ?? null,
                'comment_count' => $statistics['commentCount'] ?? null,
                'duration' => $this->formatDuration($contentDetails['duration'] ?? null),
                'tags' => $snippet['tags'] ?? null,
                'category_id' => $categoryId,
                'category_name' => $categoryName,
                'thumbnail' => $thumbnails['high']['url']
                    ?? $thumbnails['medium']['url']
                    ?? $thumbnails['default']['url']
                    ?? null,
            ];
        } catch (Throwable $e) {
            Log::error('Lỗi khi gọi YouTube Data API (/videos)', ['message' => $e->getMessage()]);

            return null;
        }
    }

    public function syncComments(Video $video): void
    {
        if (empty($video->youtube_id)) {
            return;
        }

        $comments = $this->fetchComments($video->youtube_id);

        foreach ($comments as $comment) {
            if (empty($comment['youtube_comment_id'])) {
                continue;
            }

            VideoComment::updateOrCreate(
                ['youtube_comment_id' => $comment['youtube_comment_id']],
                [
                    'video_id' => $video->id,
                    'parent_youtube_comment_id' => $comment['parent_youtube_comment_id'],
                    'author_name' => $comment['author_name'],
                    'author_avatar' => $comment['author_avatar'],
                    'text' => $comment['text'],
                    'like_count' => $comment['like_count'] ?? 0,
                    'published_at' => $comment['published_at'] ?? null,
                ]
            );
        }
    }

    public function fetchComments(string $youtubeId): array
    {
        $apiKey = config('services.youtube.api_key');
        $baseUrl = rtrim((string) config('services.youtube.data_api_base_url'), '/');

        if (empty($apiKey)) {
            return [];
        }

        $comments = [];
        $pageToken = null;
        $maxPages = 5;

        try {
            do {
                $params = [
                    'part' => 'snippet,replies',
                    'videoId' => $youtubeId,
                    'maxResults' => 100,
                    'order' => 'relevance',
                    'key' => $apiKey,
                ];

                if ($pageToken) {
                    $params['pageToken'] = $pageToken;
                }

                $response = Http::timeout(20)->get("{$baseUrl}/commentThreads", $params);

                if (! $response->successful()) {
                    Log::warning('YouTube Data API (/commentThreads) lỗi', [
                        'status' => $response->status(),
                    ]);

                    break;
                }

                foreach ($response->json('items', []) as $thread) {
                    $topLevel = $thread['snippet']['topLevelComment'] ?? null;
                    if (empty($topLevel) || empty($topLevel['id'])) {
                        continue;
                    }

                    $topSnippet = $topLevel['snippet'] ?? [];

                    $comments[] = [
                        'youtube_comment_id' => $topLevel['id'],
                        'parent_youtube_comment_id' => null,
                        'author_name' => $topSnippet['authorDisplayName'] ?? 'Ẩn danh',
                        'author_avatar' => $topSnippet['authorProfileImageUrl'] ?? null,
                        'text' => $topSnippet['textDisplay'] ?? $topSnippet['textOriginal'] ?? null,
                        'like_count' => $topSnippet['likeCount'] ?? 0,
                        'published_at' => $topSnippet['publishedAt'] ?? null,
                    ];

                    foreach ($thread['replies']['comments'] ?? [] as $reply) {
                        $replySnippet = $reply['snippet'] ?? [];

                        $comments[] = [
                            'youtube_comment_id' => $reply['id'] ?? null,
                            'parent_youtube_comment_id' => $topLevel['id'],
                            'author_name' => $replySnippet['authorDisplayName'] ?? 'Ẩn danh',
                            'author_avatar' => $replySnippet['authorProfileImageUrl'] ?? null,
                            'text' => $replySnippet['textDisplay'] ?? $replySnippet['textOriginal'] ?? null,
                            'like_count' => $replySnippet['likeCount'] ?? 0,
                            'published_at' => $replySnippet['publishedAt'] ?? null,
                        ];
                    }
                }

                $pageToken = $response->json('nextPageToken');
                $maxPages--;
            } while ($pageToken && $maxPages > 0);
        } catch (Throwable $e) {
            Log::error('Lỗi khi lấy comments', ['message' => $e->getMessage()]);
        }

        return $comments;
    }

    protected function fetchChannel(string $channelId): ?array
    {
        $apiKey = config('services.youtube.api_key');
        $baseUrl = rtrim((string) config('services.youtube.data_api_base_url'), '/');

        if (empty($apiKey)) {
            return null;
        }

        try {
            $response = Http::timeout(15)->get("{$baseUrl}/channels", [
                'part' => 'snippet,statistics',
                'id' => $channelId,
                'key' => $apiKey,
            ]);

            if (! $response->successful()) {
                return null;
            }

            $item = $response->json('items.0');
            if (empty($item)) {
                return null;
            }

            $snippet = $item['snippet'] ?? [];
            $statistics = $item['statistics'] ?? [];
            $thumbnails = $snippet['thumbnails'] ?? [];

            return [
                'avatar' => $thumbnails['default']['url']
                    ?? $thumbnails['medium']['url']
                    ?? $thumbnails['high']['url']
                    ?? null,
                'subscriber_count' => $statistics['subscriberCount'] ?? null,
            ];
        } catch (Throwable $e) {
            return null;
        }
    }

    protected function fetchCategoryName(string $categoryId): ?string
    {
        $apiKey = config('services.youtube.api_key');
        $baseUrl = rtrim((string) config('services.youtube.data_api_base_url'), '/');

        if (empty($apiKey)) {
            return null;
        }

        try {
            $response = Http::timeout(10)->get("{$baseUrl}/videoCategories", [
                'part' => 'snippet',
                'id' => $categoryId,
                'key' => $apiKey,
            ]);

            if (! $response->successful()) {
                return null;
            }

            return $response->json('items.0.snippet.title');
        } catch (Throwable $e) {
            return null;
        }
    }

    public function formatDuration(?string $iso8601): ?string
    {
        if (empty($iso8601) || ! str_starts_with($iso8601, 'PT')) {
            return $iso8601;
        }

        $str = substr($iso8601, 2);
        $hours = preg_match('/(\d+)H/', $str, $m) ? (int) $m[1] : 0;
        $minutes = preg_match('/(\d+)M/', $str, $m) ? (int) $m[1] : 0;
        $seconds = preg_match('/(\d+)S/', $str, $m) ? (int) $m[1] : 0;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%d:%02d', $minutes, $seconds);
    }

    protected function paginatedPayload($paginator): array
    {
        return [
            'data' => collect($paginator->items())
                ->map(fn (Video $video) => $this->formatPayload($video))
                ->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ];
    }
}
