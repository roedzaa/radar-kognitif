<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Security Gateway — MILITER EYES</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .security-grid {
            background-image:
                linear-gradient(rgba(51, 65, 85, 0.12) 1px, transparent 1px),
                linear-gradient(90deg, rgba(51, 65, 85, 0.12) 1px, transparent 1px);
            background-size: 36px 36px;
            mask-image: radial-gradient(circle at center, black 20%, transparent 78%);
        }

        .radar-sweep {
            background: conic-gradient(from 0deg, transparent 0deg, rgba(16, 185, 129, 0.14) 35deg, transparent 70deg);
            animation: radar-rotate 8s linear infinite;
        }

        @keyframes radar-rotate {
            to {
                transform: rotate(360deg);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .radar-sweep {
                animation: none;
            }
        }
    </style>
</head>
<body class="min-h-screen overflow-x-hidden bg-slate-950 font-sans text-slate-200 antialiased selection:bg-emerald-500/20 selection:text-emerald-200">
    <div class="pointer-events-none fixed inset-0 security-grid"></div>
    <div class="pointer-events-none fixed left-1/2 top-1/2 size-[44rem] -translate-x-1/2 -translate-y-1/2 rounded-full border border-emerald-500/5 opacity-70">
        <div class="absolute inset-[12%] rounded-full border border-emerald-500/5"></div>
        <div class="absolute inset-[28%] rounded-full border border-emerald-500/5"></div>
        <div class="radar-sweep absolute inset-0 rounded-full"></div>
    </div>
    <div class="pointer-events-none fixed inset-0 bg-[radial-gradient(circle_at_center,_transparent_0%,_rgba(2,6,23,0.65)_52%,_#020617_82%)]"></div>

    <main class="relative z-10 flex min-h-screen items-center justify-center px-4 py-10 sm:px-6">
        <div class="w-full max-w-md">
            <div class="mb-6 flex flex-col items-center text-center">
                <div class="relative flex size-16 items-center justify-center rounded-2xl border border-emerald-500/30 bg-slate-900/80 shadow-[0_0_48px_rgba(16,185,129,0.12)] backdrop-blur-xl">
                    <span class="absolute inset-2 rounded-full border border-dashed border-emerald-500/30"></span>
                    <svg class="size-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 3l7 3v5c0 4.45-2.99 8.42-7 9.62C7.99 19.42 5 15.45 5 11V6l7-3z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M9.5 11.5 11 13l3.5-3.5"></path>
                    </svg>
                </div>
                <div class="mt-4 font-mono text-[10px] font-bold tracking-[0.32em] text-emerald-400">MILITARY SECURITY GATEWAY</div>
                <h1 class="mt-2 text-2xl font-black tracking-[0.08em] text-slate-100">MILITER EYES</h1>
                <p class="mt-1 text-xs tracking-wide text-slate-500">Cognitive Defense &amp; Global OSINT Command System</p>
            </div>

            <section aria-labelledby="login-title" class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/60 shadow-[0_30px_90px_rgba(0,0,0,0.55)] backdrop-blur-xl">
                <div class="border-b border-rose-500/20 bg-rose-500/5 px-5 py-3.5">
                    <div class="flex items-start gap-3">
                        <span class="relative mt-1 flex size-2 shrink-0">
                            <span class="absolute inline-flex size-full animate-ping rounded-full bg-rose-400 opacity-70"></span>
                            <span class="relative inline-flex size-2 rounded-full bg-rose-500"></span>
                        </span>
                        <div>
                            <div class="font-mono text-[10px] font-extrabold tracking-[0.2em] text-rose-300">RESTRICTED AREA // TOP SECRET</div>
                            <p class="mt-1 text-[10px] leading-relaxed text-slate-500">PUSAT KOMANDO SIBER TNI AD · AKSES PERSONEL TEROTORISASI</p>
                        </div>
                    </div>
                </div>

                <div class="p-5 sm:p-7">
                    <div class="mb-6 flex items-end justify-between gap-4">
                        <div>
                            <h2 id="login-title" class="text-base font-bold tracking-wide text-slate-100">Otentikasi Operator</h2>
                            <p class="mt-1 text-[11px] text-slate-500">Masukkan kredensial komando untuk melanjutkan.</p>
                        </div>
                        <span class="rounded-md border border-emerald-500/20 bg-emerald-500/10 px-2 py-1 font-mono text-[9px] font-semibold tracking-wider text-emerald-300">TLS CHANNEL</span>
                    </div>

                    @if (session('status'))
                        <div class="mb-5 rounded-lg border border-emerald-500/20 bg-emerald-500/5 px-3.5 py-3 text-xs text-emerald-300">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="email" class="mb-2 block font-mono text-[10px] font-bold tracking-[0.18em] text-slate-400">OPERATOR EMAIL</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 6.75A2.25 2.25 0 015.25 4.5h13.5A2.25 2.25 0 0121 6.75v10.5a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 17.25V6.75zm0 .75 8.05 5.37a1.75 1.75 0 001.9 0L21 7.5"></path>
                                    </svg>
                                </span>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                    class="block w-full rounded-lg border border-slate-800 bg-slate-950/80 py-3 pl-10 pr-3 font-mono text-sm text-slate-100 placeholder:text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
                                    placeholder="operator@militereyes.mil.id">
                            </div>
                            @foreach ($errors->get('email') as $message)
                                <p class="mt-2 text-[11px] font-medium text-rose-400">{{ $message }}</p>
                            @endforeach
                        </div>

                        <div>
                            <label for="password" class="mb-2 block font-mono text-[10px] font-bold tracking-[0.18em] text-slate-400">SECURITY KEY</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M16.5 10.5V7.875a4.5 4.5 0 00-9 0V10.5m-.75 0h10.5A2.25 2.25 0 0119.5 12.75v6A2.25 2.25 0 0117.25 21H6.75a2.25 2.25 0 01-2.25-2.25v-6a2.25 2.25 0 012.25-2.25z"></path>
                                    </svg>
                                </span>
                                <input id="password" name="password" type="password" required autocomplete="current-password"
                                    class="block w-full rounded-lg border border-slate-800 bg-slate-950/80 py-3 pl-10 pr-3 font-mono text-sm text-slate-100 placeholder:text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
                                    placeholder="••••••••••••">
                            </div>
                            @foreach ($errors->get('password') as $message)
                                <p class="mt-2 text-[11px] font-medium text-rose-400">{{ $message }}</p>
                            @endforeach
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <label for="remember_me" class="inline-flex cursor-pointer items-center gap-2.5 text-xs text-slate-500">
                                <input id="remember_me" name="remember" type="checkbox"
                                    class="size-4 rounded border-slate-700 bg-slate-950 text-emerald-500 focus:ring-2 focus:ring-emerald-500/30 focus:ring-offset-0">
                                <span>Pertahankan sesi terenkripsi</span>
                            </label>
                            <span class="font-mono text-[9px] tracking-wider text-slate-600">NODE: JKT-01</span>
                        </div>

                        <button type="submit"
                            class="group inline-flex w-full items-center justify-center gap-2.5 rounded-lg border border-emerald-400/30 bg-emerald-500 px-4 py-3 text-xs font-extrabold tracking-[0.18em] text-slate-950 shadow-[0_0_28px_rgba(16,185,129,0.16)] transition hover:bg-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-slate-950 active:bg-emerald-600">
                            <svg class="size-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"></path>
                            </svg>
                            INITIALIZE SECURE SESSION
                        </button>
                    </form>
                </div>

                <div class="flex items-center justify-between gap-4 border-t border-slate-800 bg-slate-950/40 px-5 py-3 font-mono text-[9px] tracking-[0.12em] text-slate-600 sm:px-7">
                    <span>ACCESS LOGGING: ENABLED</span>
                    <span>ZERO TRUST PROTOCOL</span>
                </div>
            </section>

            <p class="mx-auto mt-5 max-w-sm text-center text-[10px] leading-relaxed text-slate-600">
                Seluruh upaya akses direkam dan diaudit. Akses tanpa otorisasi dapat dikenakan tindakan sesuai protokol keamanan yang berlaku.
            </p>
        </div>
    </main>
</body>
</html>
