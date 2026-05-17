<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attachment Viewer</title>
    <style>
        :root {
            --bg: #000000;
            --panel: rgba(16, 16, 16, 0.72);
            --text: #e7e9ea;
            --muted: #9aa0a6;
            --btn: rgba(255, 255, 255, 0.15);
            --btn-hover: rgba(255, 255, 255, 0.24);
            --border: rgba(255, 255, 255, 0.22);
        }

        html, body {
            margin: 0;
            width: 100%;
            height: 100%;
            background: var(--bg);
            color: var(--text);
            font: 9pt Tahoma, Arial;
        }

        .viewer {
            position: fixed;
            inset: 0;
            display: grid;
            grid-template-rows: auto 1fr auto;
            background: radial-gradient(circle at center, #111 0%, #000 62%);
            overflow: hidden;
        }

        .topbar,
        .bottombar {
            position: relative;
            z-index: 6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 10px 14px;
            background: var(--panel);
            backdrop-filter: blur(3px);
        }

        .title {
            font: bold 9pt Tahoma, Arial;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 48vw;
        }

        .meta {
            color: var(--muted);
            white-space: nowrap;
        }

        .media-wrap {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 0;
            z-index: 2;
        }

        .media-wrap img {
            max-width: min(95vw, 1300px);
            max-height: calc(100vh - 118px);
            object-fit: contain;
            user-select: none;
            -webkit-user-drag: none;
        }

        .media-wrap iframe {
            width: min(96vw, 1300px);
            height: calc(100vh - 118px);
            border: 0;
            background: #111;
        }

        .nav-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 34px;
            padding: 0 12px;
            border: 1px solid var(--border);
            border-radius: 999px;
            background: var(--btn);
            color: var(--text);
            text-decoration: none;
            line-height: 1;
        }

        .nav-btn:hover {
            background: var(--btn-hover);
        }

        .controls {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .tap-zone {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 50%;
            z-index: 4;
            text-decoration: none;
            outline: none;
        }

        .tap-zone.left { left: 0; cursor: w-resize; }
        .tap-zone.right { right: 0; cursor: e-resize; }

        .tap-zone:focus { box-shadow: inset 0 0 0 2px rgba(29, 155, 240, 0.8); }

        .side-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 5;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: var(--btn);
            color: var(--text);
            text-decoration: none;
            font-size: 18px;
            line-height: 1;
        }

        .side-arrow:hover { background: var(--btn-hover); }
        .side-arrow.left { left: 14px; }
        .side-arrow.right { right: 14px; }

        .link {
            color: var(--text);
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .title { max-width: 38vw; }
            .side-arrow { display: none; }
            .nav-btn {
                min-width: 30px;
                height: 30px;
                padding: 0 10px;
            }
            .media-wrap img,
            .media-wrap iframe {
                max-width: 100vw;
                width: 100vw;
            }
        }
    </style>
</head>
<body>
    @php
        $prevUrl = route('finance.attachment.view', ['finance' => $finance->id, 'i' => $prevIndex]);
        $nextUrl = route('finance.attachment.view', ['finance' => $finance->id, 'i' => $nextIndex]);
    @endphp
    <div class="viewer" id="viewer-root" tabindex="0">
        <div class="topbar">
            <div class="title">Transaction Attachment Viewer</div>
            <div class="meta">{{ $index + 1 }} / {{ $total }}</div>
        </div>

        <div class="media-wrap" id="media-wrap">
            <a class="tap-zone left" href="{{ $prevUrl }}" aria-label="Previous"></a>
            <a class="tap-zone right" href="{{ $nextUrl }}" aria-label="Next"></a>

            <a class="side-arrow left" href="{{ $prevUrl }}" aria-label="Previous">&#10094;</a>
            <a class="side-arrow right" href="{{ $nextUrl }}" aria-label="Next">&#10095;</a>

            @if($isImage)
                <img src="{{ asset('storage/' . $currentPath) }}" alt="Attachment {{ $index + 1 }}">
            @else
                <iframe src="{{ asset('storage/' . $currentPath) }}" title="Attachment {{ $index + 1 }}"></iframe>
            @endif
        </div>

        <div class="bottombar">
            <div class="controls">
                <a class="nav-btn" href="{{ $prevUrl }}" aria-label="Previous attachment">&#10094;</a>
                <a class="nav-btn" href="{{ $nextUrl }}" aria-label="Next attachment">&#10095;</a>
            </div>
            <div class="controls">
                <a class="link" href="{{ asset('storage/' . $currentPath) }}" target="_blank">Open File</a>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const prevUrl = @json($prevUrl);
            const nextUrl = @json($nextUrl);
            const root = document.getElementById('viewer-root');

            // Keep keyboard navigation always active.
            root.focus();

            document.addEventListener('keydown', function (e) {
                const key = e.key;
                if (key === 'ArrowLeft') {
                    e.preventDefault();
                    window.location.href = prevUrl;
                }
                if (key === 'ArrowRight') {
                    e.preventDefault();
                    window.location.href = nextUrl;
                }
            });

            // Mobile tap support: left half = previous, right half = next.
            const mediaWrap = document.getElementById('media-wrap');
            mediaWrap.addEventListener('touchend', function (e) {
                if (!e.changedTouches || !e.changedTouches.length) return;
                const touch = e.changedTouches[0];
                const rect = mediaWrap.getBoundingClientRect();
                const x = touch.clientX - rect.left;
                if (x < rect.width / 2) {
                    window.location.href = prevUrl;
                } else {
                    window.location.href = nextUrl;
                }
            }, { passive: true });
        })();
    </script>
</body>
</html>
