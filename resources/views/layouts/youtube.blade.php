<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0f0f0f">
    <title>@yield('title', 'YouTube')</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --bg:#0f0f0f; --bg2:#272727; --bg3:#1f1f1f; --txt:#f1f1f1; --sub:#aaaaaa; --red:#ff0000; --red2:#cc0000; }
        html, body { background: var(--bg); color: var(--txt); font-family: Roboto, Arial, "Helvetica Neue", Helvetica, sans-serif; -webkit-font-smoothing: antialiased; }
        body { max-width: 560px; margin: 0 auto; min-height: 100vh; position: relative; padding-bottom: 56px; }
        a { color: inherit; text-decoration: none; }
        img { display: block; max-width: 100%; }
        ::-webkit-scrollbar { display: none; }

        .topbar { position: sticky; top: 0; z-index: 50; display: flex; align-items: center; justify-content: space-between; height: 56px; padding: 0 12px; background: var(--bg); }
        .topbar .left { display: flex; align-items: center; gap: 12px; }
        .topbar .right { display: flex; align-items: center; gap: 16px; }
        .logo { display: flex; align-items: center; gap: 4px; font-size: 18px; font-weight: 700; letter-spacing: -1px; }
        .logo .play { background: var(--red); border-radius: 6px; width: 26px; height: 18px; display: flex; align-items: center; justify-content: center; }
        .logo .play svg { width: 14px; height: 14px; fill: #fff; }
        .icon-btn { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; }
        .icon-btn svg { width: 24px; height: 24px; fill: var(--txt); }
        .avatar-dot { width: 32px; height: 32px; border-radius: 50%; background: #3ea6ff; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; }

        .chips { position: sticky; top: 56px; z-index: 40; display: flex; gap: 8px; padding: 10px 12px; overflow-x: auto; background: var(--bg); }
        .chip { white-space: nowrap; background: var(--bg2); color: var(--txt); padding: 7px 12px; border-radius: 8px; font-size: 14px; }
        .chip.active { background: var(--txt); color: #000; }

        .feed { padding: 4px 0 24px; }
        .vcard { display: flex; flex-direction: column; margin-bottom: 4px; }
        .thumb { position: relative; aspect-ratio: 16/9; background: #000; overflow: hidden; }
        .thumb img { width: 100%; height: 100%; object-fit: cover; }
        .dur { position: absolute; right: 8px; bottom: 8px; background: rgba(0,0,0,.8); color: #fff; font-size: 12px; font-weight: 600; padding: 2px 5px; border-radius: 4px; }
        .vmeta { display: flex; gap: 12px; padding: 12px; }
        .vmeta .ch-av { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; background: var(--bg2); flex: none; }
        .vmeta .ch-av.placeholder { display: flex; align-items: center; justify-content: center; font-size: 15px; color: var(--sub); }
        .vinfo { flex: 1; min-width: 0; }
        .vinfo .t { font-size: 15px; line-height: 1.4; font-weight: 600; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 4px; }
        .vinfo .s { font-size: 13px; color: var(--sub); line-height: 1.5; }
        .vinfo .s span.dot { margin: 0 4px; }
        .more-btn { width: 36px; height: 36px; flex: none; display: flex; align-items: center; justify-content: center; }
        .more-btn svg { width: 20px; height: 20px; fill: var(--txt); }

        .empty { text-align: center; color: var(--sub); padding: 60px 24px; }
        .empty .big { font-size: 40px; margin-bottom: 12px; }

        .bottomnav { position: fixed; bottom: 0; left: 0; right: 0; max-width: 560px; margin: 0 auto; height: 56px; background: var(--bg); border-top: 1px solid #272727; display: flex; z-index: 60; }
        .bn { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 3px; font-size: 11px; color: var(--sub); }
        .bn svg { width: 24px; height: 24px; fill: var(--sub); }
        .bn.active { color: var(--txt); }
        .bn.active svg { fill: var(--txt); }
        .bn.plus { position: relative; }
        .bn.plus .fab { width: 34px; height: 26px; border-radius: 10px; background: var(--bg2); display: flex; align-items: center; justify-content: center; }
        .bn.plus .fab svg { width: 18px; height: 18px; fill: var(--txt); }

        .pager { display: flex; justify-content: center; gap: 12px; padding: 16px; }
        .pager a, .pager span { padding: 8px 14px; background: var(--bg2); border-radius: 18px; font-size: 14px; }

        /* watch page */
        .backbar { position: sticky; top: 0; z-index: 50; display: flex; align-items: center; gap: 16px; height: 56px; padding: 0 12px; background: var(--bg); }
        .backbar .logo { font-size: 17px; }
        .player-container { position: relative; aspect-ratio: 16/9; background: #000; }
        .player { position: absolute; inset: 0; }
        .player iframe { width: 100%; height: 100%; border: 0; position: relative; z-index: 1; }
        .player-topshield { position: absolute; top: 0; left: 0; right: 0; height: 56px; z-index: 5; }
        .player-logoshield { position: absolute; right: 0; bottom: 40px; width: 130px; height: 54px; z-index: 5; }
        .player.mini { position: fixed; right: 8px; top: 64px; width: 168px; max-width: 48vw; aspect-ratio: 16/9; z-index: 90; border-radius: 10px; overflow: hidden; box-shadow: 0 6px 20px rgba(0,0,0,.7); }
        .watch-body { padding: 12px; }
        .watch-title { font-size: 18px; font-weight: 700; line-height: 1.4; margin-bottom: 8px; }
        .watch-stats { font-size: 13px; color: var(--sub); margin-bottom: 12px; }
        .watch-actions { display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px; margin: 0 -12px 12px; padding-left: 12px; padding-right: 12px; }
        .act { white-space: nowrap; display: flex; align-items: center; gap: 6px; background: var(--bg2); padding: 8px 14px; border-radius: 18px; font-size: 14px; font-weight: 600; }
        .act svg { width: 20px; height: 20px; fill: var(--txt); }
        .act .like-split { display: flex; align-items: center; }
        .act .like-split .v { padding-left: 8px; margin-left: 8px; border-left: 1px solid #4a4a4a; }
        .ch-row { display: flex; align-items: center; gap: 12px; padding: 12px 0; border-top: 1px solid #272727; border-bottom: 1px solid #272727; }
        .ch-row .ch-av { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; background: var(--bg2); flex: none; }
        .ch-row .ch-av.placeholder { display: flex; align-items: center; justify-content: center; font-size: 17px; color: var(--sub); }
        .ch-info { flex: 1; min-width: 0; }
        .ch-info .n { font-size: 15px; font-weight: 600; }
        .ch-info .sub { font-size: 12px; color: var(--sub); }
        .btn-sub { background: var(--txt); color: #000; padding: 9px 16px; border-radius: 18px; font-size: 14px; font-weight: 600; white-space: nowrap; }
        .desc-card { background: var(--bg2); border-radius: 12px; padding: 12px; margin: 12px 0; }
        .desc-card .top { font-size: 13px; font-weight: 600; margin-bottom: 8px; }
        .desc-card .top .dot { margin: 0 4px; }
        .desc-body { font-size: 14px; line-height: 1.5; white-space: pre-wrap; }
        .desc-body.clamp { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .desc-toggle { margin-top: 8px; font-size: 14px; font-weight: 600; color: var(--sub); }
        .tags { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px; }
        .tags .tag { background: var(--bg3); color: #3ea6ff; font-size: 12px; padding: 3px 8px; border-radius: 6px; }

        .cmts-head { display: flex; align-items: center; gap: 8px; padding: 12px 0 8px; font-size: 16px; font-weight: 600; }
        .cmts-head svg { width: 22px; height: 22px; fill: var(--txt); }
        .cmt-add { display: flex; align-items: center; gap: 12px; padding: 8px 0 16px; }
        .cmt-add .me { width: 32px; height: 32px; border-radius: 50%; background: #3ea6ff; flex: none; }
        .cmt-add input { flex: 1; background: transparent; border: 0; border-bottom: 1px solid #4a4a4a; color: var(--txt); font-size: 14px; padding: 6px 0; outline: none; }
        .cmt { display: flex; gap: 12px; padding: 10px 0; }
        .cmt .av { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; background: var(--bg2); flex: none; }
        .cmt .av.placeholder { display: flex; align-items: center; justify-content: center; font-size: 14px; color: var(--sub); }
        .cmt .body { flex: 1; min-width: 0; }
        .cmt .who { font-size: 13px; color: var(--sub); margin-bottom: 2px; }
        .cmt .who b { color: var(--txt); font-size: 13px; font-weight: 600; margin-right: 6px; }
        .cmt .txt { font-size: 14px; line-height: 1.4; }
        .cmt .acts { display: flex; align-items: center; gap: 16px; margin-top: 6px; }
        .cmt .acts .lk { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--sub); }
        .cmt .acts .lk svg { width: 16px; height: 16px; fill: var(--sub); }
        .cmt .replies { margin-top: 8px; padding-left: 4px; border-left: 2px solid #272727; }
        .cmt .replies .more-rep { font-size: 13px; font-weight: 600; color: #3ea6ff; margin-top: 8px; display: flex; align-items: center; gap: 6px; }

        .section-head { display: flex; align-items: center; gap: 8px; padding: 20px 12px 8px; font-size: 16px; font-weight: 700; }
        .section-head::before { content: ''; flex: none; width: 4px; height: 20px; background: var(--red); border-radius: 2px; }
        .rec { border-top: 1px solid #272727; margin-top: 12px; }

        .cmt-bar { display: flex; align-items: center; gap: 12px; margin: 0 -12px; padding: 12px; border-top: 1px solid #272727; border-bottom: 1px solid #272727; cursor: pointer; }
        .cmt-bar .ic { flex: none; display: flex; }
        .cmt-bar .ic svg { width: 24px; height: 24px; fill: var(--txt); }
        .cmt-bar .cnt { font-size: 16px; font-weight: 700; white-space: nowrap; }
        .cmt-bar .pv { flex: 1; min-width: 0; display: flex; align-items: center; gap: 10px; }
        .cmt-bar .pv .av { width: 28px; height: 28px; border-radius: 50%; object-fit: cover; flex: none; background: var(--bg2); }
        .cmt-bar .pv .av.placeholder { display: flex; align-items: center; justify-content: center; font-size: 12px; color: var(--sub); }
        .cmt-bar .pv .txt { font-size: 13px; color: var(--sub); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1; min-width: 0; }
        .cmt-bar .pv .txt b { color: var(--txt); margin-right: 6px; font-weight: 600; }
        .cmt-bar .chev { margin-left: auto; flex: none; }
        .cmt-bar .chev svg { width: 20px; height: 20px; fill: var(--sub); }

        .cmt-sheet { position: fixed; inset: 0; background: rgba(0,0,0,.6); z-index: 200; display: none; }
        .cmt-sheet.open { display: block; }
        .cmt-sheet .panel { position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 100%; max-width: 560px; max-height: 88vh; background: var(--bg); border-radius: 16px 16px 0 0; display: flex; flex-direction: column; }
        .cmt-sheet .grab { width: 40px; height: 4px; background: #4a4a4a; border-radius: 2px; margin: 8px auto 4px; flex: none; }
        .cmt-sheet .handle { display: flex; align-items: center; padding: 4px 16px 12px; border-bottom: 1px solid #272727; flex: none; }
        .cmt-sheet .handle .t { font-size: 16px; font-weight: 700; flex: 1; }
        .cmt-sheet .handle .x { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; cursor: pointer; }
        .cmt-sheet .handle .x svg { width: 22px; height: 22px; fill: var(--txt); }
        .cmt-sheet .list { overflow-y: auto; padding: 8px 12px 24px; }
    </style>
</head>
<body>
    @if (empty($hideTopbar))
    <header class="topbar">
        <div class="left">
            <span class="icon-btn" aria-label="menu">
                <svg viewBox="0 0 24 24"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>
            </span>
            <a class="logo" href="{{ route('videos.web.index') }}">
                <span class="play"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></span>
                YouTube
            </a>
        </div>
        <div class="right">
            <span class="icon-btn" aria-label="search">
                <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 1 0-.7.7l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0A4.5 4.5 0 1 1 14 9.5 4.5 4.5 0 0 1 9.5 14z"/></svg>
            </span>
            <span class="avatar-dot">A</span>
        </div>
    </header>
    @endif

    @yield('content')

    <nav class="bottomnav">
        <a href="{{ route('videos.web.index') }}" class="bn @if(request()->routeIs('videos.web.index')) active @endif">
            <svg viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
            <span>Trang chủ</span>
        </a>
        <a class="bn">
            <svg viewBox="0 0 24 24"><path d="M10 9.5l-1 .586V14.914l1 .586 6-3v-0L10 9.5zM4 4h16v16H4z" fill="none"/><path d="M17 3H7a5 5 0 0 0-5 5v8a5 5 0 0 0 5 5h10a5 5 0 0 0 5-5V8a5 5 0 0 0-5-5zm-1 12.5l-6-3.5v-0l6-3.5v7z"/></svg>
            <span>Shorts</span>
        </a>
        <a class="bn plus">
            <span class="fab"><svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg></span>
        </a>
        <a class="bn">
            <svg viewBox="0 0 24 24"><path d="M20 8H4V6h16v2zm-2-6H6v2h12V2zm4 10v8H2v-8h20zm-9 2v2h5v-2h-5zm-4 0H6v2h3v-2H9z"/></svg>
            <span>Đăng ký</span>
        </a>
        <a class="bn">
            <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            <span>Cá nhân</span>
        </a>
    </nav>

    @stack('scripts')
</body>
</html>
