<section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan intelijen">
    @php
        $cards = [
            ['id' => 'statTotal', 'label' => 'TOTAL INTEL', 'value' => $berita->count(), 'color' => 'text-cyan-400', 'border' => 'group-hover:border-cyan-500/35'],
            ['id' => 'statMerah', 'label' => 'ANCAMAN KRITIS', 'value' => $berita->where('status_alert', 'Merah')->count(), 'color' => 'text-rose-400', 'border' => 'group-hover:border-rose-500/35'],
            ['id' => 'statKuning', 'label' => 'STATUS WASPADA', 'value' => $berita->where('status_alert', 'Kuning')->count(), 'color' => 'text-amber-400', 'border' => 'group-hover:border-amber-500/35'],
            ['id' => 'statAman', 'label' => 'SEKTOR AMAN', 'value' => $berita->where('status_alert', 'Aman')->count(), 'color' => 'text-emerald-400', 'border' => 'group-hover:border-emerald-500/35'],
        ];
    @endphp

    @foreach($cards as $card)
        <article class="group relative overflow-hidden rounded-xl border border-slate-800 bg-slate-900/60 p-5 shadow-xl shadow-black/10 backdrop-blur transition {{ $card['border'] }}">
            <div class="absolute -right-8 -top-8 size-24 rounded-full bg-current opacity-[.025] {{ $card['color'] }}"></div>
            <div class="font-mono text-[9px] font-bold tracking-[.18em] text-slate-500">{{ $card['label'] }}</div>
            <div class="mt-3 flex items-end justify-between">
                <span id="{{ $card['id'] }}" class="font-mono text-3xl font-bold tracking-tight {{ $card['color'] }}">{{ number_format($card['value'], 0, ',', '.') }}</span>
                <span class="mb-1 flex items-center gap-1 font-mono text-[8px] text-slate-600"><span class="size-1.5 animate-pulse rounded-full bg-emerald-400"></span>LIVE</span>
            </div>
        </article>
    @endforeach
</section>
