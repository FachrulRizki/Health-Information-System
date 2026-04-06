@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <span style="color: #7B1D1D; font-weight: 600;">Dashboard</span>
@endsection

@section('content')
<div class="fade-in">
    @if ($role === 'admin')
        @include('dashboard.partials.admin')
    @elseif ($role === 'dokter')
        @include('dashboard.partials.dokter')
    @elseif ($role === 'perawat')
        @include('dashboard.partials.perawat')
    @elseif ($role === 'farmasi')
        @include('dashboard.partials.farmasi')
    @elseif ($role === 'kasir')
        @include('dashboard.partials.kasir')
    @elseif ($role === 'petugas_pendaftaran')
        @include('dashboard.partials.petugas_pendaftaran')
    @elseif ($role === 'manajemen')
        @include('dashboard.partials.manajemen')
    @else
        <p style="color:#6B4C4C;">Dashboard belum tersedia untuk peran ini.</p>
    @endif
</div>
@endsection
