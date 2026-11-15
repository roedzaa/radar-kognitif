<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MILITER EYES // Cognitive Intelligence')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: Inter, sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .military-grid { background-image: linear-gradient(rgba(16,185,129,.025) 1px,transparent 1px),linear-gradient(90deg,rgba(16,185,129,.025) 1px,transparent 1px); background-size: 32px 32px; }
        .leaflet-container { background:#020617; font-family:Inter,sans-serif; }
        .leaflet-tile-pane { filter:brightness(.58) invert(1) contrast(2.4) hue-rotate(185deg) saturate(.28); }
        .leaflet-control-zoom a { background:#0f172a!important;color:#94a3b8!important;border-color:#1e293b!important; }
        .leaflet-popup-content-wrapper,.leaflet-popup-tip { background:#0f172a;color:#e2e8f0;border:1px solid #334155; }
        .leaflet-popup-content-wrapper { border-radius:10px; box-shadow:0 20px 50px rgba(0,0,0,.55); }
        .leaflet-popup-content { margin:0!important; width:270px!important; }
        .leaflet-popup-close-button { color:#94a3b8!important; }
        .critical-radar-marker { position:relative;width:26px;height:26px; }
        .critical-radar-marker span { position:absolute;inset:7px;border-radius:999px;background:#f43f5e;border:2px solid #fecdd3;box-shadow:0 0 14px #f43f5e; }
        .critical-radar-marker::before,.critical-radar-marker::after { content:"";position:absolute;inset:3px;border:2px solid rgba(244,63,94,.8);border-radius:999px;animation:radar-ping 1.8s cubic-bezier(0,0,.2,1) infinite; }
        .critical-radar-marker::after { animation-delay:.65s; }
        @keyframes radar-ping { 75%,100% { transform:scale(2.25);opacity:0; } }
        .ticker-track { display:flex;width:max-content;animation:threat-ticker 42s linear infinite; }
        .ticker-track:hover { animation-play-state:paused; }
        @keyframes threat-ticker { from{transform:translateX(0)} to{transform:translateX(-50%)} }
        .scanline::after { content:"";pointer-events:none;position:absolute;inset:0;background:linear-gradient(transparent 50%,rgba(16,185,129,.025) 50%);background-size:100% 4px; }
    </style>
    @stack('styles')
</head>
<body class="military-grid min-h-screen bg-slate-950 text-slate-300 antialiased selection:bg-emerald-500/30 selection:text-emerald-100">
    @include('partials.topbar')

    <main class="mx-auto max-w-[1600px] space-y-6 px-4 py-6 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    <footer class="border-t border-slate-800/70 px-6 py-5 text-center font-mono text-[9px] tracking-[.2em] text-slate-600">
        MILITER EYES // PUSAT KOMANDO SIBER TNI AD // AKSES TERKLASIFIKASI
    </footer>

    @yield('scripts')
</body>
</html>
