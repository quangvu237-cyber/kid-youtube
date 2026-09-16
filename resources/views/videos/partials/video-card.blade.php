@php
    /** @var \App\Models\Video $v */
@endphp
<a class="vcard" href="{{ route('videos.web.show', $v->id) }}">
    <div class="thumb">
        @if ($v->thumbnail_url)
            <img src="{{ $v->thumbnail_url }}" alt="" loading="lazy">
        @endif
        @if ($v->duration)
            <span class="dur">{{ $v->duration }}</span>
        @endif
    </div>
    <div class="vmeta">
        @if ($v->channel_avatar)
            <img class="ch-av" src="{{ $v->channel_avatar }}" alt="" loading="lazy">
        @else
            <div class="ch-av placeholder">{{ strtoupper(mb_substr($v->channel ?? '?', 0, 1)) }}</div>
        @endif
        <div class="vinfo">
            <div class="t">{{ $v->title ?? '(Không có tiêu đề)' }}</div>
            <div class="s">
                {{ $v->channel ?: 'Kênh' }}
                <span class="dot">•</span>
                {{ \App\Helpers\YtFormat::count($v->view_count) }} lượt xem
                <span class="dot">•</span>
                {{ \App\Helpers\YtFormat::ago($v->published_at) }}
            </div>
        </div>
        <span class="more-btn">
            <svg viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
        </span>
    </div>
</a>
