<section class="overflow-hidden rounded-xl border border-cyan-500/15 bg-slate-900/60 shadow-2xl shadow-black/20 backdrop-blur" aria-labelledby="cognitive-domain-title">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-800 bg-gradient-to-r from-cyan-500/5 to-transparent px-5 py-4">
        <div>
            <p class="font-mono text-[8px] tracking-[.2em] text-cyan-500">METRIK KOGNITIF</p>
            <h2 id="cognitive-domain-title" class="mt-1 text-sm font-extrabold tracking-[.12em] text-white">ANALISIS SIBER SOSIAL</h2>
        </div>
        <span class="rounded border border-cyan-500/20 bg-cyan-500/5 px-2 py-1 font-mono text-[8px] text-cyan-300">{{ $metricKognitifTerbaru ? 'DATA TERCATAT' : 'BELUM ADA DATA' }}</span>
    </div>

    <div class="grid gap-4 p-4 lg:grid-cols-2 2xl:grid-cols-4">
        <article class="rounded-lg border border-slate-800 bg-slate-950/55 p-4">
            <h3 class="font-mono text-[10px] font-bold tracking-wider text-slate-200">SENTIMENT ANALYSIS</h3>
            @if($metricKognitifTerbaru)
                <p class="mt-1 text-[10px] text-slate-600">Ringkasan dari metrik yang tercatat</p>
                <div class="mt-5 space-y-4">
                    @foreach([
                        ['key'=>'Positive','label'=>'POSITIF','count'=>$metricKognitifTerbaru->sentimen_positif,'percent'=>$persentaseSentimen['positif'],'color'=>'bg-emerald-400','text'=>'text-emerald-300'],
                        ['key'=>'Neutral','label'=>'NETRAL','count'=>$metricKognitifTerbaru->sentimen_netral,'percent'=>$persentaseSentimen['netral'],'color'=>'bg-slate-400','text'=>'text-slate-300'],
                        ['key'=>'Negative','label'=>'NEGATIF','count'=>$metricKognitifTerbaru->sentimen_negatif,'percent'=>$persentaseSentimen['negatif'],'color'=>'bg-rose-400','text'=>'text-rose-300'],
                    ] as $sentiment)
                        <div>
                            <div class="mb-1.5 flex items-center justify-between font-mono text-[9px]"><span class="text-slate-500">{{ $sentiment['label'] }}</span><span class="{{ $sentiment['text'] }}"><span id="sentiment{{ $sentiment['key'] }}Count" class="mr-2 text-slate-600">{{ number_format($sentiment['count'], 0, ',', '.') }}</span><span id="sentiment{{ $sentiment['key'] }}Percent">{{ number_format($sentiment['percent'], 1) }}%</span></span></div>
                            <div class="h-1.5 overflow-hidden rounded-full bg-slate-800"><div id="sentiment{{ $sentiment['key'] }}Bar" class="h-full rounded-full {{ $sentiment['color'] }} transition-all duration-700" style="width: {{ $sentiment['percent'] }}%"></div></div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-5 h-28"><canvas id="sentimentTrendChart" aria-label="Grafik tren sentimen dari data yang tercatat"></canvas></div>
                <p class="mt-3 border-t border-slate-800 pt-2 font-mono text-[8px] text-slate-600">TOTAL TERCATAT: <span id="sentimentTotal">{{ number_format($totalSentimen, 0, ',', '.') }}</span></p>
            @else
                <p class="mt-4 rounded-md border border-slate-800 p-4 text-center font-mono text-[9px] text-slate-500">BELUM ADA DATA SENTIMEN DARI SUMBER TERHUBUNG</p>
            @endif
        </article>

        <article class="rounded-lg border border-slate-800 bg-slate-950/55 p-4">
            <h3 class="font-mono text-[10px] font-bold tracking-wider text-slate-200">KLASIFIKASI BOT / HOAKS</h3>
            @if($metricKognitifTerbaru)
                <p class="mt-1 text-[10px] text-slate-600">Jumlah pada metrik terbaru</p>
                <div id="detectedBotCount" class="mt-5 rounded-lg border border-slate-800 bg-slate-900/60 p-4 font-mono text-3xl font-black text-slate-300">{{ number_format($metricKognitifTerbaru->jumlah_bot_terdeteksi, 0, ',', '.') }}</div>
            @else
                <p class="mt-4 rounded-md border border-slate-800 p-4 text-center font-mono text-[9px] text-slate-500">BELUM ADA HASIL KLASIFIKASI TERVERIFIKASI</p>
            @endif
        </article>

        <article class="rounded-lg border border-slate-800 bg-slate-950/55 p-4">
            <div class="flex items-start justify-between gap-3">
                <div><h3 class="font-mono text-[10px] font-bold tracking-wider text-slate-200">EARLY WARNING ISU</h3><p class="mt-1 text-[10px] text-slate-600">Isu dari metrik yang tercatat</p></div>
                @if($metricKognitifTerbaru)
                    <span class="font-mono text-lg font-black text-amber-400">{{ $persentaseIsuPrioritas }}%</span>
                @endif
            </div>
            <div class="mt-4 space-y-2">
                @forelse($trendingIssues as $issue)
                    @php
                        $critical = in_array($issue['tingkat'] ?? '', ['Kritis', 'Tinggi'], true);
                        $issueLabel = $issue['tagar'] ?? $issue['tag'] ?? $issue['isu'] ?? 'UNCLASSIFIED';
                        $issueVolume = $issue['volume'] ?? $issue['jumlah_mention'] ?? 0;
                    @endphp
                    <div class="rounded-md border {{ $critical ? 'border-rose-500/20 bg-rose-500/5' : 'border-slate-800 bg-slate-900/60' }} p-3">
                        <div class="flex items-center justify-between gap-2"><span class="truncate font-mono text-[10px] font-bold {{ $critical ? 'text-rose-300' : 'text-slate-300' }}">{{ $issueLabel }}</span><span class="font-mono text-[8px] text-amber-400">+{{ $issue['pertumbuhan'] ?? 0 }}%</span></div>
                        <div class="mt-1 font-mono text-[8px] text-slate-600">{{ strtoupper($issue['tingkat'] ?? 'Monitor') }} // {{ number_format($issueVolume, 0, ',', '.') }} mention</div>
                    </div>
                @empty
                    <div class="rounded-md border border-slate-800 p-4 text-center font-mono text-[9px] text-slate-500">BELUM ADA DATA ISU</div>
                @endforelse
            </div>
        </article>

        <article class="rounded-lg border border-slate-800 bg-slate-950/55 p-4">
            <h3 class="font-mono text-[10px] font-bold tracking-wider text-slate-200">ANALISIS JEJARING SOSIAL</h3>
            <p class="mt-1 text-[10px] text-slate-600">Memerlukan data platform sosial yang terhubung</p>
            <div class="mt-4 flex h-48 items-center justify-center rounded-md border border-slate-800 p-4 text-center font-mono text-[9px] text-slate-500">BELUM ADA DATA JEJARING SOSIAL</div>
        </article>
    </div>
</section>
