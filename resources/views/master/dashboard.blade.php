@extends('layouts.app')

@section('title', 'Master Data')

@section('breadcrumb')
    <span class="font-medium" style="color: #1A0A0A;">Master Data</span>
@endsection

@section('content')
<div class="fade-in">

<div class="mb-6">
    <h2 class="text-xl font-bold" style="color: #1A0A0A;">Master Data</h2>
    <p class="text-sm mt-0.5" style="color: #6B4C4C;">Kelola semua data referensi sistem</p>
</div>

<style>
.master-card {
    background: #FFFFFF;
    border-radius: 1rem;
    padding: 1.5rem;
    border: 1px solid #F0E8E8;
    box-shadow: 0 2px 12px rgba(123,29,29,0.08);
    transition: all 0.2s ease;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 0.75rem;
}
.master-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(123,29,29,0.15);
    border-color: #D4A017;
}
.master-card-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 0.875rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    background: linear-gradient(135deg, #FFF5F5, #FFE8E8);
    color: #7B1D1D;
    border: 1px solid #F0E8E8;
}
.master-card-title {
    font-size: 0.9rem;
    font-weight: 700;
    color: #1A0A0A;
    margin: 0;
}
.master-card-desc {
    font-size: 0.78rem;
    color: #6B4C4C;
    margin: 0;
    line-height: 1.4;
}
</style>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1.25rem;">

    <a href="{{ route('master.polis.index') }}" class="master-card">
        <div class="master-card-icon"><i class="fa-solid fa-hospital"></i></div>
        <div>
            <p class="master-card-title">Input Poli</p>
            <p class="master-card-desc">Kelola data poliklinik</p>
        </div>
    </a>

    <a href="{{ route('master.api-settings.index') }}" class="master-card">
        <div class="master-card-icon"><i class="fa-solid fa-plug"></i></div>
        <div>
            <p class="master-card-title">Setting API</p>
            <p class="master-card-desc">Konfigurasi integrasi eksternal</p>
        </div>
    </a>

    <a href="{{ route('master.staff.index') }}" class="master-card">
        <div class="master-card-icon"><i class="fa-solid fa-users"></i></div>
        <div>
            <p class="master-card-title">Manajemen Staf</p>
            <p class="master-card-desc">Data petugas non-dokter</p>
        </div>
    </a>

    <a href="{{ route('master.doctors.index') }}" class="master-card">
        <div class="master-card-icon"><i class="fa-solid fa-user-doctor"></i></div>
        <div>
            <p class="master-card-title">Manajemen Dokter</p>
            <p class="master-card-desc">Data dokter &amp; spesialis</p>
        </div>
    </a>

    <a href="{{ route('master.action-masters.index') }}" class="master-card">
        <div class="master-card-icon"><i class="fa-solid fa-syringe"></i></div>
        <div>
            <p class="master-card-title">Tarif &amp; Jenis Tindakan</p>
            <p class="master-card-desc">Kelola tindakan medis dan tarif</p>
        </div>
    </a>

    <a href="{{ route('master.drugs.index') }}" class="master-card">
        <div class="master-card-icon"><i class="fa-solid fa-pills"></i></div>
        <div>
            <p class="master-card-title">Manajemen Obat</p>
            <p class="master-card-desc">Data obat &amp; stok</p>
        </div>
    </a>

    <a href="{{ route('master.schedules.index') }}" class="master-card">
        <div class="master-card-icon"><i class="fa-solid fa-calendar-days"></i></div>
        <div>
            <p class="master-card-title">Jadwal Dokter</p>
            <p class="master-card-desc">Jadwal praktik dokter</p>
        </div>
    </a>

    <a href="{{ route('master.rooms.index') }}" class="master-card">
        <div class="master-card-icon"><i class="fa-solid fa-bed"></i></div>
        <div>
            <p class="master-card-title">Manajemen Bed</p>
            <p class="master-card-desc">Kamar &amp; tempat tidur</p>
        </div>
    </a>

    <a href="{{ route('master.permissions.index') }}" class="master-card">
        <div class="master-card-icon"><i class="fa-solid fa-shield-halved"></i></div>
        <div>
            <p class="master-card-title">Manajemen Hak Akses</p>
            <p class="master-card-desc">Atur hak akses per peran</p>
        </div>
    </a>

    <a href="{{ route('admin.failed-jobs.index') }}" class="master-card">
        <div class="master-card-icon"><i class="fa-solid fa-list-check"></i></div>
        <div>
            <p class="master-card-title">Log Aktivitas</p>
            <p class="master-card-desc">Audit trail sistem</p>
        </div>
    </a>

</div>

</div>
@endsection
