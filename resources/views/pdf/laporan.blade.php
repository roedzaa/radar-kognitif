<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Intelijen {{ $nomorDokumen }}</title>
    <style>
        @page { margin: 34px 38px 44px; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111; font-family: DejaVu Sans, sans-serif; font-size: 10px; line-height: 1.45; }
        .classification { border: 3px double #000; padding: 8px; text-align: center; font-size: 22px; font-weight: 900; letter-spacing: 6px; }
        .header { margin-top: 14px; border-bottom: 2px solid #000; padding-bottom: 12px; text-align: center; }
        .header h1 { margin: 0; font-size: 20px; letter-spacing: 2px; }
        .header p { margin: 4px 0 0; font-size: 9px; letter-spacing: 1px; }
        .meta { width: 100%; margin-top: 16px; border-collapse: collapse; }
        .meta td { width: 50%; border: 1px solid #000; padding: 7px; vertical-align: top; }
        .label { display: block; margin-bottom: 2px; font-size: 8px; font-weight: bold; letter-spacing: 1px; }
        .summary { width: 100%; margin: 16px 0; border-collapse: collapse; }
        .summary td { border: 1px solid #000; padding: 9px; text-align: center; }
        .summary strong { display: block; font-size: 18px; }
        h2 { margin: 18px 0 7px; border-bottom: 1px solid #000; padding-bottom: 4px; font-size: 12px; letter-spacing: 1px; }
        .report { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .report th, .report td { border: 1px solid #000; padding: 6px; vertical-align: top; word-wrap: break-word; }
        .report th { background: #dedede; font-size: 8px; letter-spacing: .6px; text-transform: uppercase; }
        .center { text-align: center; }
        .empty { padding: 28px !important; text-align: center; font-style: italic; }
        .notice { margin-top: 16px; border: 1px solid #000; padding: 9px; font-size: 8px; text-align: justify; }
        .signature { width: 100%; margin-top: 24px; border-collapse: collapse; }
        .signature td { width: 50%; text-align: center; vertical-align: top; }
        .signature-space { height: 52px; }
        .footer { position: fixed; right: 0; bottom: -28px; left: 0; border-top: 1px solid #000; padding-top: 5px; text-align: center; font-size: 7px; letter-spacing: 1px; }
    </style>
</head>
<body>
    <div class="classification">TOP SECRET</div>

    <div class="header">
        <h1>MILITER EYES</h1>
        <p>LAPORAN HARIAN INTELIJEN ANCAMAN KRITIS // COGNITIVE DEFENSE &amp; OSINT COMMAND</p>
    </div>

    <table class="meta">
        <tr>
            <td><span class="label">NOMOR DOKUMEN</span>{{ $nomorDokumen }}</td>
            <td><span class="label">TANGGAL OPERASI</span>{{ $tanggal->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td><span class="label">DIHASILKAN OLEH</span>{{ $operator?->name ?? 'SYSTEM OPERATOR' }}</td>
            <td><span class="label">KLASIFIKASI</span>TOP SECRET // EYES ONLY</td>
        </tr>
    </table>

    <table class="summary">
        <tr>
            <td><strong>{{ (int) ($ringkasanHariIni['Merah'] ?? 0) }}</strong>KRITIS</td>
            <td><strong>{{ (int) ($ringkasanHariIni['Kuning'] ?? 0) }}</strong>WASPADA</td>
            <td><strong>{{ (int) ($ringkasanHariIni['Aman'] ?? 0) }}</strong>AMAN</td>
            <td><strong>{{ (int) $ringkasanHariIni->sum() }}</strong>TOTAL SINYAL</td>
        </tr>
    </table>

    <h2>DAFTAR ANCAMAN PRIORITAS SATU</h2>
    <table class="report">
        <thead>
            <tr>
                <th style="width: 5%">No.</th>
                <th style="width: 17%">Waktu</th>
                <th style="width: 18%">Sumber</th>
                <th style="width: 42%">Laporan Intelijen</th>
                <th style="width: 18%">Koordinat</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($ancamanKritis as $ancaman)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $ancaman->waktu_publish?->format('d/m/Y H:i:s') ?? '-' }}</td>
                    <td>{{ $ancaman->sumber ?? 'OSINT' }}</td>
                    <td>
                        <strong>{{ $ancaman->judul }}</strong>
                        @if ($ancaman->link)
                            <div style="margin-top: 3px; font-size: 7px;">{{ $ancaman->link }}</div>
                        @endif
                    </td>
                    <td class="center">
                        {{ $ancaman->latitude !== null ? number_format($ancaman->latitude, 5) : '-' }}<br>
                        {{ $ancaman->longitude !== null ? number_format($ancaman->longitude, 5) : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="empty">TIDAK ADA ANCAMAN KRITIS YANG TERCATAT PADA PERIODE LAPORAN.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="notice">
        <strong>CATATAN:</strong> Dokumen ini mengagregasi laporan dari feed RSS publik dan memberi label risiko berdasarkan pencocokan kata kunci. Sistem ini tidak terhubung ke platform media sosial; koordinat hanya dicantumkan jika lokasi disebut dalam judul laporan. Verifikasi setiap temuan sebelum digunakan sebagai dasar keputusan.
    </div>

    <table class="signature">
        <tr>
            <td>
                ANALIS INTELIJEN
                <div class="signature-space"></div>
                (____________________________)
            </td>
            <td>
                PERWIRA PENGENDALI
                <div class="signature-space"></div>
                (____________________________)
            </td>
        </tr>
    </table>

    <div class="footer">TOP SECRET // MILITER EYES // {{ $nomorDokumen }} // HANDLE VIA AUTHORIZED CHANNELS ONLY</div>
</body>
</html>
