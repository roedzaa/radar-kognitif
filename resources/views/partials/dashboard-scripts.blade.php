<script>
document.addEventListener('DOMContentLoaded', () => {
    const reports = @json($berita);
    const threatTrend = @json($statistikTren);
    const cognitiveTrend = @json($statistikKognitif);
    const liveMetricsUrl = @json(route('osint.live-metrics'));
    const palette = { Merah: '#f43f5e', Kuning: '#f59e0b', Aman: '#10b981' };
    const numberFormatter = new Intl.NumberFormat('id-ID');

    const escapeHtml = (value) => String(value ?? '').replace(/[&<>'"]/g, (character) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'
    })[character]);
    const updateText = (id, value) => {
        const element = document.getElementById(id);
        if (element) element.textContent = value;
    };

    // Geospatial threat map and layered filters.
    const mapElement = document.getElementById('threatMap');
    if (mapElement && window.L) {
        const map = L.map(mapElement, { zoomControl: true, minZoom: 4 }).setView([-2.4, 118], 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);

        const layers = { Merah: L.layerGroup(), Kuning: L.layerGroup(), Aman: L.layerGroup() };
        Object.values(layers).forEach((layer) => layer.addTo(map));

        reports.forEach((report) => {
            const latitude = Number(report.latitude);
            const longitude = Number(report.longitude);
            if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) return;

            const status = Object.hasOwn(layers, report.status_alert) ? report.status_alert : 'Aman';
            const color = palette[status];
            const marker = status === 'Merah'
                ? L.marker([latitude, longitude], {
                    icon: L.divIcon({
                        className: '',
                        html: '<div class="critical-radar-marker" aria-label="Ancaman kritis"><span></span></div>',
                        iconSize: [26, 26],
                        iconAnchor: [13, 13],
                    }),
                })
                : L.circleMarker([latitude, longitude], {
                    radius: status === 'Kuning' ? 7 : 6,
                    color,
                    fillColor: color,
                    fillOpacity: .78,
                    weight: 2,
                });
            const publishedAt = report.waktu_publish
                ? new Date(report.waktu_publish).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' })
                : 'Waktu tidak tersedia';
            const documentButton = report.link
                ? `<a href="${escapeHtml(report.link)}" target="_blank" rel="noopener noreferrer" style="display:block;margin-top:12px;padding:8px;border:1px solid ${color}55;border-radius:6px;background:${color}18;color:${color};text-align:center;font:700 10px 'JetBrains Mono',monospace;text-decoration:none">BACA DOKUMEN →</a>`
                : '<div style="margin-top:12px;color:#64748b;font:10px JetBrains Mono,monospace">DOKUMEN INTERNAL / TANPA TAUTAN</div>';
            marker.bindPopup(`
                <div style="padding:15px">
                    <span style="display:inline-block;padding:4px 7px;border:1px solid ${color}55;border-radius:4px;background:${color}18;color:${color};font:700 9px 'JetBrains Mono',monospace">${escapeHtml(status.toUpperCase())}</span>
                    <h3 style="margin:10px 0 8px;color:#f8fafc;font-size:13px;line-height:1.5;font-weight:700">${escapeHtml(report.judul)}</h3>
                    <div style="color:#94a3b8;font:9px/1.7 'JetBrains Mono',monospace">SOURCE // ${escapeHtml(report.sumber)}<br>TIME // ${escapeHtml(publishedAt)}</div>
                    ${documentButton}
                </div>
            `, { maxWidth: 310 });
            marker.addTo(layers[status]);
        });

        const filterLabels = { all: 'Semua ancaman', Merah: 'Kritis (Merah)', Kuning: 'Waspada (Kuning)', Aman: 'Aman (Hijau)' };
        document.querySelectorAll('[data-map-filter]').forEach((button) => {
            button.addEventListener('click', () => {
                const selected = button.dataset.mapFilter;
                Object.entries(layers).forEach(([status, layer]) => {
                    const visible = selected === 'all' || selected === status;
                    if (visible && !map.hasLayer(layer)) map.addLayer(layer);
                    if (!visible && map.hasLayer(layer)) map.removeLayer(layer);
                });
                document.querySelectorAll('[data-map-filter]').forEach((candidate) => {
                    const active = candidate === button;
                    candidate.setAttribute('aria-pressed', String(active));
                    candidate.classList.toggle('border-emerald-500/40', active);
                    candidate.classList.toggle('bg-emerald-500/10', active);
                    candidate.classList.toggle('text-emerald-300', active);
                    candidate.classList.toggle('border-slate-700', !active);
                    candidate.classList.toggle('bg-slate-900', !active);
                    candidate.classList.toggle('text-slate-400', !active);
                });
                updateText('activeMapFilterLabel', `Layer aktif: ${filterLabels[selected]}`);
            });
        });
        setTimeout(() => map.invalidateSize(), 100);
    }

    const chartTextColor = '#64748b';
    const gridColor = 'rgba(51,65,85,.34)';
    const commonScales = {
        x: { ticks: { color: chartTextColor, font: { family: 'JetBrains Mono', size: 9 } }, grid: { color: gridColor } },
        y: { beginAtZero: true, ticks: { color: chartTextColor, precision: 0, font: { family: 'JetBrains Mono', size: 9 } }, grid: { color: gridColor } },
    };
    const lineDataset = (label, data, color) => ({ label, data, borderColor: color, backgroundColor: `${color}16`, borderWidth: 2, tension: .35, pointRadius: 2, fill: true });

    let threatTrendChart = null;
    const trendCanvas = document.getElementById('trendChart');
    if (trendCanvas && window.Chart) {
        threatTrendChart = new Chart(trendCanvas, {
            type: 'line',
            data: { labels: threatTrend.labels, datasets: [lineDataset('Kritis', threatTrend.merah, palette.Merah), lineDataset('Waspada', threatTrend.kuning, palette.Kuning), lineDataset('Aman', threatTrend.aman, palette.Aman)] },
            options: { responsive: true, maintainAspectRatio: false, interaction: { intersect: false, mode: 'index' }, plugins: { legend: { labels: { color: '#94a3b8', boxWidth: 8, font: { family: 'JetBrains Mono', size: 9 } } } }, scales: commonScales },
        });
    }

    let sentimentTrendChart = null;
    const sentimentCanvas = document.getElementById('sentimentTrendChart');
    if (sentimentCanvas && window.Chart) {
        sentimentTrendChart = new Chart(sentimentCanvas, {
            type: 'line',
            data: { labels: cognitiveTrend.labels, datasets: [lineDataset('Positif', cognitiveTrend.positif, palette.Aman), lineDataset('Netral', cognitiveTrend.netral, '#94a3b8'), lineDataset('Negatif', cognitiveTrend.negatif, palette.Merah)] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { display: false }, y: { display: false, beginAtZero: true } } },
        });
    }

    const applySentiment = (prefix, percentage) => {
        updateText(`sentiment${prefix}Percent`, `${Number(percentage || 0).toFixed(1)}%`);
        const bar = document.getElementById(`sentiment${prefix}Bar`);
        if (bar) bar.style.width = `${Math.min(100, Math.max(0, Number(percentage || 0)))}%`;
    };
    const pollLiveMetrics = async () => {
        try {
            const response = await fetch(liveMetricsUrl, { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            const payload = await response.json();
            const counts = payload.counts || {};
            const mapCounts = payload.map_counts || {};
            [['statTotal','total'],['statMerah','merah'],['statKuning','kuning'],['statAman','aman']].forEach(([id,key]) => updateText(id, numberFormatter.format(counts[key] || 0)));
            [['mappedCount','total'],['filterCountAll','total'],['filterCountMerah','merah'],['filterCountKuning','kuning'],['filterCountAman','aman']].forEach(([id,key]) => updateText(id, numberFormatter.format(mapCounts[key] || 0)));

            if (threatTrendChart && payload.threat_trend) {
                threatTrendChart.data.labels = payload.threat_trend.labels;
                [payload.threat_trend.merah, payload.threat_trend.kuning, payload.threat_trend.aman].forEach((data, index) => threatTrendChart.data.datasets[index].data = data);
                threatTrendChart.update('none');
            }
            if (sentimentTrendChart && payload.cognitive_trend) {
                sentimentTrendChart.data.labels = payload.cognitive_trend.labels;
                [payload.cognitive_trend.positif, payload.cognitive_trend.netral, payload.cognitive_trend.negatif].forEach((data, index) => sentimentTrendChart.data.datasets[index].data = data);
                sentimentTrendChart.update('none');
            }
            if (payload.sentiment) {
                applySentiment('Positive', payload.sentiment.percentages?.positif);
                applySentiment('Neutral', payload.sentiment.percentages?.netral);
                applySentiment('Negative', payload.sentiment.percentages?.negatif);
                updateText('sentimentTotal', numberFormatter.format(payload.sentiment.total || 0));
                updateText('detectedBotCount', numberFormatter.format(payload.sentiment.bots || 0));
            }
            updateText('lastLiveSync', `Sinkronisasi terakhir // ${new Date(payload.generated_at).toLocaleTimeString('id-ID', { hour12: false })}`);
        } catch (error) {
            updateText('lastLiveSync', 'Sinkronisasi tertunda // koneksi akan dicoba kembali');
            console.warn('MILITER EYES live polling failed:', error);
        }
    };
    window.setInterval(pollLiveMetrics, 60_000);
});
</script>
