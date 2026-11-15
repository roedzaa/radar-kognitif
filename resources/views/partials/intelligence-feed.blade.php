<section class="overflow-hidden rounded-xl border border-slate-800 bg-slate-900/60 shadow-xl shadow-black/15 backdrop-blur">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-800 px-5 py-4">
        <div><p class="font-mono text-[8px] tracking-[.2em] text-emerald-500">OMNI-SOURCE AGGREGATOR</p><h2 class="mt-1 text-sm font-bold tracking-wider text-white">INTELLIGENCE FEED</h2></div>
        <div class="flex flex-wrap gap-2">
            <form method="POST" action="{{ route('osint.test.telegram') }}">@csrf<button class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 font-mono text-[8px] font-bold text-slate-400 hover:text-white">TEST EWS</button></form>
            <form method="POST" action="{{ route('osint.fetch') }}">@csrf<button class="rounded-md border border-emerald-500/30 bg-emerald-500/10 px-3 py-2 font-mono text-[8px] font-bold text-emerald-300 hover:bg-emerald-500/20">JALANKAN FULL SCAN</button></form>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="border-b border-slate-800 bg-slate-950/50 font-mono text-[8px] tracking-wider text-slate-500"><tr><th class="px-5 py-3">STATUS</th><th class="px-5 py-3">LAPORAN</th><th class="px-5 py-3">SUMBER</th><th class="px-5 py-3">WAKTU</th></tr></thead>
            <tbody class="divide-y divide-slate-800/70">
                @forelse($berita->take(12) as $item)
                    @php($statusColor = match($item->status_alert) {'Merah'=>'border-rose-500/30 bg-rose-500/10 text-rose-300','Kuning'=>'border-amber-500/30 bg-amber-500/10 text-amber-300',default=>'border-emerald-500/30 bg-emerald-500/10 text-emerald-300'})
                    <tr class="transition hover:bg-slate-800/30">
                        <td class="px-5 py-4"><span class="rounded border px-2 py-1 font-mono text-[8px] font-bold {{ $statusColor }}">{{ strtoupper($item->status_alert) }}</span></td>
                        <td class="max-w-xl px-5 py-4 font-medium text-slate-200">@if($item->link)<a href="{{ $item->link }}" target="_blank" rel="noopener noreferrer" class="hover:text-emerald-300">{{ $item->judul }}</a>@else{{ $item->judul }}@endif</td>
                        <td class="whitespace-nowrap px-5 py-4 font-mono text-[9px] text-slate-500">{{ $item->sumber }}</td>
                        <td class="whitespace-nowrap px-5 py-4 font-mono text-[9px] text-slate-600">{{ $item->waktu_publish?->format('d/m/Y H:i') ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-12 text-center font-mono text-[10px] text-slate-600">BELUM ADA DATA // JALANKAN FULL SCAN</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
