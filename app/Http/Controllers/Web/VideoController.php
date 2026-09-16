<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\VideoComment;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $videos = Video::query()
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $recommend = collect();

        if (! $videos->hasMorePages() && $videos->isNotEmpty()) {
            $exclude = $videos->pluck('id')->all();

            $recommend = Video::query()
                ->whereNotIn('id', $exclude)
                ->inRandomOrder()
                ->limit(6)
                ->get();

            if ($recommend->isEmpty()) {
                $recommend = Video::query()
                    ->inRandomOrder()
                    ->limit(6)
                    ->get();
            }
        }

        return view('videos.index', compact('videos', 'recommend'));
    }

    public function show(int $id)
    {
        $video = Video::findOrFail($id);

        $comments = VideoComment::query()
            ->where('video_id', $video->id)
            ->orderBy('published_at')
            ->get();

        $recommend = Video::query()
            ->where('id', '!=', $video->id)
            ->inRandomOrder()
            ->limit(8)
            ->get();

        return view('videos.show', compact('video', 'comments', 'recommend'));
    }
}
