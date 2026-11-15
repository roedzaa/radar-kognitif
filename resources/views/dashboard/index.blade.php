@extends('layouts.military')

@section('content')
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
