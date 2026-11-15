@extends('layouts.military')

@section('content')
    @guest
        <section class="rounded-xl border border-cyan-500/25 bg-cyan-500/5 px-5 py-4">
            <p class="font-mono text-[9px] font-bold tracking-[.18em] text-cyan-300">PROTOTIPE LOMBA // DEMO PUBLIK // MODE BACA SAJA</p>
            <p class="mt-2 max-w-4xl text-xs leading-6 text-slate-300">
                Data laporan contoh, sinyal sosial, dan metrik kognitif pada demo dapat berupa simulasi; bukan intelijen terverifikasi atau pemantauan langsung media sosial.
            </p>
        </section>
    @endguest
    @include('partials.alerts')
    @include('partials.stats')
    @include('partials.map')
    @include('partials.cognitive')
    @include('partials.terminal')
    @include('partials.intelligence-feed')
@endsection

@section('scripts')
    @include('partials.dashboard-scripts')
@endsection
