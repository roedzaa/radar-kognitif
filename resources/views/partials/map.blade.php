<section class="grid gap-6 xl:grid-cols-[minmax(0,1.65fr)_minmax(360px,.8fr)]">
    <article class="overflow-hidden rounded-xl border border-slate-800 bg-slate-900/60 shadow-2xl shadow-black/20 backdrop-blur">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-800 px-5 py-4">
            <div>
                <p class="font-mono text-[8px] tracking-[.2em] text-emerald-500">GEOSPATIAL THREAT MATRIX</p>
                <h2 class="mt-1 text-sm font-bold tracking-wider text-white">PETA ANCAMAN NASIONAL</h2>
            </div>
            <span class="rounded border border-emerald-500/20 bg-emerald-500/5 px-2 py-1 font-mono text-[8px] text-emerald-400"><span id="mappedCount">{{ $beritaTerpetakan->count() }}</span> TITIK TERPETAKAN</span>
        </div>

        <div id="mapThreatFilters" class="flex flex-wrap items-center gap-2 border-b border-slate-800 bg-slate-950/35 px-4 py-3" role="group" aria-label="Filter tingkat ancaman pada peta">
            <span class="mr-1 font-mono text-[8px] font-bold tracking-[.16em] text-slate-600">FILTER LAYER</span>
            <button type="button" data-map-filter="all" aria-pressed="true" class="map-filter-button rounded-md border border-emerald-500/40 bg-emerald-500/10 px-3 py-1.5 font-mono text-[9px] font-bold text-emerald-300">SEMUA <span id="filterCountAll">{{ $beritaTerpetakan->count() }}</span></button>
            <button type="button" data-map-filter="Merah" aria-pressed="false" class="map-filter-button rounded-md border border-slate-700 bg-slate-900 px-3 py-1.5 font-mono text-[9px] font-bold text-slate-400">● KRITIS <span id="filterCountMerah">{{ $beritaTerpetakan->where('status_alert', 'Merah')->count() }}</span></button>
            <button type="button" data-map-filter="Kuning" aria-pressed="false" class="map-filter-button rounded-md border border-slate-700 bg-slate-900 px-3 py-1.5 font-mono text-[9px] font-bold text-slate-400">● WASPADA <span id="filterCountKuning">{{ $beritaTerpetakan->where('status_alert', 'Kuning')->count() }}</span></button>
            <button type="button" data-map-filter="Aman" aria-pressed="false" class="map-filter-button rounded-md border border-slate-700 bg-slate-900 px-3 py-1.5 font-mono text-[9px] font-bold text-slate-400">● AMAN <span id="filterCountAman">{{ $beritaTerpetakan->where('status_alert', 'Aman')->count() }}</span></button>
        </div>
        <div class="p-3"><div id="threatMap" class="h-[440px] w-full rounded-lg border border-slate-800"></div></div>
        <div class="flex items-center justify-between border-t border-slate-800 px-4 py-3 font-mono text-[8px] text-slate-500">
            <span id="activeMapFilterLabel" class="text-emerald-400">Layer aktif: Semua ancaman</span>
            <span>LAT/LNG // EPSG:4326</span>
        </div>
    </article>

    <article class="rounded-xl border border-slate-800 bg-slate-900/60 p-5 shadow-2xl shadow-black/20 backdrop-blur">
        <div class="mb-5">
            <p class="font-mono text-[8px] tracking-[.2em] text-cyan-500">7-DAY TELEMETRY</p>
            <h2 class="mt-1 text-sm font-bold tracking-wider text-white">TREN ANCAMAN</h2>
        </div>
        <div class="h-[350px]"><canvas id="trendChart" aria-label="Grafik tren ancaman tujuh hari"></canvas></div>
        <div class="mt-5 border-t border-slate-800 pt-3 font-mono text-[8px] text-slate-600"><span id="lastLiveSync">Auto-refresh aktif // menunggu sinkronisasi</span></div>
    </article>
</section>
