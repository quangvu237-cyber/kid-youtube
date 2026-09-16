@extends('layouts.youtube')

@section('title', 'YouTube — Trang chủ')

@section('content')
    @php
        $chips = ['Tất cả', 'Âm nhạc', 'Trò chơi', 'Tin tức', 'Thể thao', 'Hài', 'Podcast', 'Hài hước', 'Công nghệ'];
    @endphp

    <div class="chips">
        @foreach ($chips as $i => $c)
            <span class="chip @if ($i === 0) active @endif">{{ $c }}</span>
        @endforeach
    </div>

    <div class="feed">
        @forelse ($videos as $v)
            @include('videos.partials.video-card', ['v' => $v])
        @empty
            <div class="empty">
                <div class="big">📺</div>
                Chưa có video nào.<br>
                Thêm video từ <a href="/admin/video/videos" style="color:#3ea6ff">admin</a>.
            </div>
        @endforelse
    </div>

    @if ($videos->hasPages())
        <div class="pager">
            @if ($videos->onFirstPage())
                <span>Trước</span>
            @else
                <a href="{{ $videos->previousPageUrl() }}">Trước</a>
            @endif
            <span>{{ $videos->currentPage() }}/{{ $videos->lastPage() }}</span>
            @if ($videos->hasMorePages())
                <a href="{{ $videos->nextPageUrl() }}">Sau</a>
            @else
                <span>Sau</span>
            @endif
        </div>
    @endif

    @if ($recommend->isNotEmpty())
        <section class="rec">
            <div class="section-head">Đề xuất cho bạn</div>
            <div class="feed" style="border-top:0;margin-top:0">
                @foreach ($recommend as $v)
                    @include('videos.partials.video-card', ['v' => $v])
                @endforeach
            </div>
        </section>
    @endif
@endsection
