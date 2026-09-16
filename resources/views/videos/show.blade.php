@extends('layouts.youtube', ['hideTopbar' => true])

@section('title', $video->title ?? 'Video')

@section('content')
    @php
        $top = $comments->whereNull('parent_youtube_comment_id');
        $preview = $top->first();
    @endphp

    <div class="backbar">
        <a class="icon-btn" href="{{ route('videos.web.index') }}">
            <svg viewBox="0 0 24 24"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
        </a>
        <a class="logo" href="{{ route('videos.web.index') }}">
            <span class="play"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></span>
            YouTube
        </a>
    </div>

    <div class="player-container" id="playerWrap">
        <div class="player" id="playerBox">
            @if ($video->embed_url)
                <iframe src="{{ $video->embed_url }}" allowfullscreen></iframe>
            @endif
            <div class="player-topshield" aria-hidden="true"></div>
            <div class="player-logoshield" aria-hidden="true"></div>
        </div>
    </div>

    <div class="watch-body">
        <h1 class="watch-title">{{ $video->title }}</h1>
        <div class="watch-stats">
            {{ \App\Helpers\YtFormat::count($video->view_count) }} lượt xem
            <span class="dot">•</span>
            {{ \App\Helpers\YtFormat::ago($video->published_at) }} ({{ \App\Helpers\YtFormat::fullDate($video->published_at) }})
        </div>

        <div class="watch-actions">
            <span class="act">
                <span class="like-split">
                    <svg viewBox="0 0 24 24"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/></svg>
                    <span>{{ \App\Helpers\YtFormat::count($video->like_count) }}</span>
                </span>
                <span class="v"><svg viewBox="0 0 24 24" style="transform:rotate(180deg)"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/></svg></span>
            </span>
            <span class="act"><svg viewBox="0 0 24 24"><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.65 1.35 3 3 3s3-1.35 3-3-1.35-3-3.08-3z"/></svg> Chia sẻ</span>
            <span class="act"><svg viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg> Tải xuống</span>
            <span class="act"><svg viewBox="0 0 24 24"><path d="M17 3H7a2 2 0 0 0-2 2v16l7-3 7 3V5a2 2 0 0 0-2-2z"/></svg> Lưu</span>
        </div>

        <div class="ch-row">
            @if ($video->channel_avatar)
                <img class="ch-av" src="{{ $video->channel_avatar }}" alt="">
            @else
                <div class="ch-av placeholder">{{ strtoupper(mb_substr($video->channel ?? '?', 0, 1)) }}</div>
            @endif
            <div class="ch-info">
                <div class="n">{{ $video->channel ?: 'Kênh' }}</div>
                <div class="sub">{{ \App\Helpers\YtFormat::count($video->channel_subscriber_count) }} người đăng ký</div>
            </div>
            <span class="btn-sub">Đăng ký</span>
        </div>

        <div class="desc-card">
            <div class="top">
                {{ \App\Helpers\YtFormat::count($video->view_count) }} lượt xem
                <span class="dot">•</span>
                {{ \App\Helpers\YtFormat::fullDate($video->published_at) }}
                @if ($video->category_name)
                    <span class="dot">•</span> {{ $video->category_name }}
                @endif
            </div>
            <div class="desc-body clamp" id="desc">{{ $video->description }}</div>
            <div class="desc-toggle" id="descToggle" onclick="var d=document.getElementById('desc');d.classList.toggle('clamp');this.textContent=d.classList.contains('clamp')?'Xem thêm':'Ẩn bớt'">Xem thêm</div>
            @if (! empty($video->tags))
                <div class="tags">
                    @foreach ($video->tags as $t)
                        <span class="tag">{{ $t }}</span>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="cmt-bar" id="cmtBar" role="button" tabindex="0">
            <span class="ic">
                <svg viewBox="0 0 24 24"><path d="M21.99 4c0-1.1-.89-2-1.99-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4-.01-18zM18 14H6v-2h12v2zm0-3H6V9h12v2zM6 8V6h12v2H6z"/></svg>
            </span>
            <span class="cnt">{{ \App\Helpers\YtFormat::count($video->comment_count) }} bình luận</span>
            @if ($preview)
                <span class="pv">
                    @if ($preview->author_avatar)
                        <img class="av" src="{{ $preview->author_avatar }}" alt="">
                    @else
                        <span class="av placeholder">{{ strtoupper(mb_substr($preview->author_name ?? '?', 0, 1)) }}</span>
                    @endif
                    <span class="txt"><b>{{ $preview->author_name }}</b>{{ \Illuminate\Support\Str::limit(strip_tags((string) $preview->text), 70) }}</span>
                </span>
            @endif
            <span class="chev"><svg viewBox="0 0 24 24"><path d="M9.29 6.71a.996.996 0 0 0 0 1.41L13.17 12l-3.88 3.88a.996.996 0 1 0 1.41 1.41l4.59-4.59a.996.996 0 0 0 0-1.41L10.7 6.71a.996.996 0 0 0-1.41 0z"/></svg></span>
        </div>
    </div>

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

    <div class="cmt-sheet" id="cmtSheet">
        <div class="panel">
            <div class="grab"></div>
            <div class="handle">
                <span class="t">{{ \App\Helpers\YtFormat::count($video->comment_count) }} bình luận</span>
                <span class="x" id="cmtClose">
                    <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                </span>
            </div>
            <div class="list">
                <div class="cmt-add">
                    <div class="me"></div>
                    <input type="text" placeholder="Thêm bình luận...">
                </div>

                @forelse ($top as $c)
                    @php
                        $replies = $comments->where('parent_youtube_comment_id', $c->youtube_comment_id);
                    @endphp
                    <div class="cmt">
                        @if ($c->author_avatar)
                            <img class="av" src="{{ $c->author_avatar }}" alt="">
                        @else
                            <div class="av placeholder">{{ strtoupper(mb_substr($c->author_name ?? '?', 0, 1)) }}</div>
                        @endif
                        <div class="body">
                            <div class="who"><b>{{ $c->author_name }}</b> {{ \App\Helpers\YtFormat::ago($c->published_at) }}</div>
                            <div class="txt">{!! nl2br(e($c->text)) !!}</div>
                            <div class="acts">
                                <span class="lk">
                                    <svg viewBox="0 0 24 24"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/></svg>
                                    {{ \App\Helpers\YtFormat::count($c->like_count) }}
                                </span>
                                <span class="lk"><svg viewBox="0 0 24 24" style="transform:rotate(180deg)"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/></svg></span>
                            </div>
                            @if ($replies->isNotEmpty())
                                <div class="replies">
                                    @foreach ($replies as $r)
                                        <div class="cmt">
                                            @if ($r->author_avatar)
                                                <img class="av" src="{{ $r->author_avatar }}" alt="">
                                            @else
                                                <div class="av placeholder">{{ strtoupper(mb_substr($r->author_name ?? '?', 0, 1)) }}</div>
                                            @endif
                                            <div class="body">
                                                <div class="who"><b>{{ $r->author_name }}</b> {{ \App\Helpers\YtFormat::ago($r->published_at) }}</div>
                                                <div class="txt">{!! nl2br(e($r->text)) !!}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="empty">Chưa có bình luận nào cho video này.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            var bar = document.getElementById('cmtBar');
            var sheet = document.getElementById('cmtSheet');
            var closeBtn = document.getElementById('cmtClose');
            if (! bar || ! sheet) return;

            var open = function () {
                sheet.classList.add('open');
                document.body.style.overflow = 'hidden';
            };
            var close = function () {
                sheet.classList.remove('open');
                document.body.style.overflow = '';
            };

            bar.addEventListener('click', open);
            bar.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); open(); }
            });
            sheet.addEventListener('click', function (e) { if (e.target === sheet) close(); });
            if (closeBtn) closeBtn.addEventListener('click', close);
        })();

        (function () {
            var wrap = document.getElementById('playerWrap');
            var box = document.getElementById('playerBox');
            if (! wrap || ! box || ! ('IntersectionObserver' in window)) {
                return;
            }
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (e) {
                    var pastTop = e.boundingClientRect.top < 0 && ! e.isIntersecting;
                    if (pastTop) {
                        box.classList.add('mini');
                    } else if (e.isIntersecting) {
                        box.classList.remove('mini');
                    }
                });
            }, { threshold: 0 });
            io.observe(wrap);
        })();
    </script>
@endpush
