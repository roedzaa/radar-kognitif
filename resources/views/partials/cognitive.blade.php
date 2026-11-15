<section class="overflow-hidden rounded-xl border border-cyan-500/15 bg-slate-900/60 shadow-2xl shadow-black/20 backdrop-blur" aria-labelledby="cognitive-domain-title">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-800 bg-gradient-to-r from-cyan-500/5 to-transparent px-5 py-4">
        <div>
            <p class="font-mono text-[8px] tracking-[.2em] text-cyan-500">COGNITIVE WARFARE OPERATIONS</p>
            <h2 id="cognitive-domain-title" class="mt-1 text-sm font-extrabold tracking-[.12em] text-white">ANALISIS SIBER SOSIAL <span class="text-cyan-400">(COGNITIVE DOMAIN)</span></h2>
        </div>
        <span class="rounded border border-cyan-500/20 bg-cyan-500/5 px-2 py-1 font-mono text-[8px] text-cyan-300">AI FUSION ONLINE</span>
    </div>

    <div class="grid gap-4 p-4 lg:grid-cols-2 2xl:grid-cols-4">
        <article class="rounded-lg border border-slate-800 bg-slate-950/55 p-4">
            <h3 class="font-mono text-[10px] font-bold tracking-wider text-slate-200">SENTIMENT ANALYSIS</h3>
            <p class="mt-1 text-[10px] text-slate-600">Distribusi persepsi publik terkini</p>
            <div class="mt-5 space-y-4">
                @foreach([
                    ['key'=>'Positive','label'=>'POSITIF','count'=>$metricKognitifTerbaru?->sentimen_positif ?? 0,'percent'=>$persentaseSentimen['positif'],'color'=>'bg-emerald-400','text'=>'text-emerald-300'],
                    ['key'=>'Neutral','label'=>'NETRAL','count'=>$metricKognitifTerbaru?->sentimen_netral ?? 0,'percent'=>$persentaseSentimen['netral'],'color'=>'bg-slate-400','text'=>'text-slate-300'],
                    ['key'=>'Negative','label'=>'NEGATIF','count'=>$metricKognitifTerbaru?->sentimen_negatif ?? 0,'percent'=>$persentaseSentimen['negatif'],'color'=>'bg-rose-400','text'=>'text-rose-300'],
                ] as $sentiment)
                    <div>
                        <div class="mb-1.5 flex items-center justify-between font-mono text-[9px]"><span class="text-slate-500">{{ $sentiment['label'] }}</span><span class="{{ $sentiment['text'] }}"><span id="sentiment{{ $sentiment['key'] }}Count" class="mr-2 text-slate-600">{{ number_format($sentiment['count'], 0, ',', '.') }}</span><span id="sentiment{{ $sentiment['key'] }}Percent">{{ number_format($sentiment['percent'], 1) }}%</span></span></div>
                        <div class="h-1.5 overflow-hidden rounded-full bg-slate-800"><div id="sentiment{{ $sentiment['key'] }}Bar" class="h-full rounded-full {{ $sentiment['color'] }} transition-all duration-700" style="width: {{ $sentiment['percent'] }}%"></div></div>
                    </div>
                @endforeach
            </div>
            <div class="mt-5 h-28"><canvas id="sentimentTrendChart" aria-label="Grafik tren sentimen tujuh hari"></canvas></div>
            <p class="mt-3 border-t border-slate-800 pt-2 font-mono text-[8px] text-slate-600">SAMPLE: <span id="sentimentTotal">{{ number_format($totalSentimen, 0, ',', '.') }}</span> MENTIONS</p>
        </article>

        <article class="rounded-lg border border-slate-800 bg-slate-950/55 p-4">
            <h3 class="font-mono text-[10px] font-bold tracking-wider text-slate-200">BUZZER / BOT & HOAX CLASSIFIER</h3>
            <p class="mt-1 text-[10px] text-slate-600">Deteksi operasi pengaruh non-organik</p>
            <div class="mt-5 rounded-lg border border-rose-500/20 bg-rose-500/5 p-4">
                <div class="flex items-center justify-between"><span class="font-mono text-[9px] text-rose-300">ENTITAS TERDETEKSI</span><span class="rounded bg-rose-500/15 px-2 py-1 font-mono text-[8px] text-rose-300">DETECTED</span></div>
                <div id="detectedBotCount" class="mt-2 font-mono text-3xl font-black text-rose-400">{{ number_format($metricKognitifTerbaru?->jumlah_bot_terdeteksi ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="mt-4 space-y-3 font-mono text-[9px]">
                <div class="flex justify-between text-slate-500"><span>Coordinated behavior</span><span class="text-amber-300">87.4%</span></div>
                <div class="h-1.5 rounded-full bg-slate-800"><div class="h-full w-[87%] rounded-full bg-amber-400"></div></div>
                <div class="flex justify-between text-slate-500"><span>Hoax probability</span><span class="text-rose-300">72.8%</span></div>
                <div class="h-1.5 rounded-full bg-slate-800"><div class="h-full w-[73%] rounded-full bg-rose-400"></div></div>
            </div>
            <div class="mt-5 flex items-center gap-2 font-mono text-[8px] text-cyan-400"><span class="size-1.5 animate-pulse rounded-full bg-cyan-400"></span>SCANNING NARRATIVE CLUSTERS...</div>
        </article>

        <article class="rounded-lg border border-slate-800 bg-slate-950/55 p-4">
            <div class="flex items-start justify-between gap-3">
                <div><h3 class="font-mono text-[10px] font-bold tracking-wider text-slate-200">EARLY WARNING ISU VIRAL</h3><p class="mt-1 text-[10px] text-slate-600">Prioritas eskalasi narasi</p></div>
                <span class="font-mono text-lg font-black text-amber-400">{{ $persentaseIsuPrioritas }}%</span>
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
                    <div class="rounded-md border border-slate-800 p-4 text-center font-mono text-[9px] text-slate-600">BELUM ADA ISU TERDETEKSI</div>
                @endforelse
            </div>
        </article>

        <article class="rounded-lg border border-slate-800 bg-slate-950/55 p-4">
            <h3 class="font-mono text-[10px] font-bold tracking-wider text-slate-200">PETA JARINGAN BUZZER &amp; BOTNET (SNA)</h3>
            <p class="mt-1 text-[10px] text-slate-600">Korelasi kluster akun dan narasi target</p>
            <div class="mt-3 h-72"><canvas id="socialNetworkChart" aria-label="Grafik jaringan buzzer dan botnet"></canvas></div>
            <div class="mt-3 flex flex-wrap gap-3 font-mono text-[8px] text-slate-500"><span><b class="text-rose-400">●</b> BOTNET</span><span><b class="text-amber-400">●</b> BUZZER</span><span><b class="text-cyan-400">●</b> TARGET</span></div>
        </article>
    </div>
</section>
