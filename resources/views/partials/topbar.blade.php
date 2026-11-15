@php($beritaKritis = $berita->where('status_alert', 'Merah')->values())

<header class="sticky top-0 z-50 border-b border-slate-800/80 bg-slate-950/90 shadow-2xl shadow-black/30 backdrop-blur-xl">
    <div class="mx-auto flex max-w-[1600px] items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
        <div class="flex min-w-0 items-center gap-3">
            <div class="grid size-10 shrink-0 place-items-center rounded-lg border border-emerald-500/30 bg-emerald-500/10 shadow-[0_0_24px_rgba(16,185,129,.12)]">
                <svg class="size-5 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="3"/><path d="M12 2v2m0 16v2M2 12h2m16 0h2"/></svg>
            </div>
            <div class="min-w-0">
                <h1 class="truncate text-sm font-black tracking-[.16em] text-white sm:text-base">MILITER <span class="text-emerald-400">EYES</span></h1>
                <p class="truncate font-mono text-[8px] tracking-[.18em] text-slate-500 sm:text-[9px]">COGNITIVE INTELLIGENCE COMMAND</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('osint.export.pdf') }}" class="hidden items-center gap-2 rounded-md border border-slate-700 bg-slate-900 px-3 py-2 font-mono text-[9px] font-bold tracking-wider text-slate-300 transition hover:border-emerald-500/40 hover:text-emerald-300 sm:inline-flex">
                EKSPOR INTELIJEN (PDF)
            </a>
            <div class="hidden border-l border-slate-800 pl-3 text-right md:block">
                <div class="text-xs font-semibold text-slate-200">{{ auth()->user()->name }}</div>
                <div class="font-mono text-[8px] text-slate-500">{{ auth()->user()->email }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="rounded-md border border-rose-500/25 bg-rose-500/5 px-3 py-2 font-mono text-[9px] font-bold tracking-wider text-rose-400 transition hover:bg-rose-500/15">LOGOUT</button>
            </form>
        </div>
    </div>

    <div class="overflow-hidden border-y border-rose-500/20 bg-slate-950">
        <div class="flex items-center">
            <div class="z-10 shrink-0 border-r border-rose-500/30 bg-rose-950/80 px-4 py-2 font-mono text-[9px] font-black tracking-[.18em] text-rose-300">LIVE THREAT</div>
            <div class="min-w-0 overflow-hidden py-2 font-mono text-[10px] text-rose-500">
                <div class="ticker-track">
                    @for($tickerPass = 0; $tickerPass < 2; $tickerPass++)
                        <div class="flex shrink-0 items-center gap-12 pr-12">
                            @forelse($beritaKritis as $item)
                                <span class="whitespace-nowrap">⚠ {{ $item->judul }}</span>
                            @empty
                                <span class="whitespace-nowrap">SYSTEM NOMINAL // BELUM ADA ANCAMAN KRITIS AKTIF</span>
                            @endforelse
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</header>
