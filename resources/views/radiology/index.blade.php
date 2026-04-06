@extends('layouts.app')

@section('title', 'Radiologi')

@section('breadcrumb')
    <span class="font-medium" style="color: #7B1D1D;">Radiologi</span>
@endsection

@section('content')
<div class="fade-in">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold" style="color: #1A0A0A;">Radiologi</h2>
            <p class="text-sm mt-0.5" style="color: #6B4C4C;">Kelola permintaan dan hasil pemeriksaan radiologi</p>
        </div>
        <div class="flex items-center gap-2 text-sm" style="color: #6B4C4C;">
            <i class="fa-solid fa-x-ray" style="color: #7B1D1D;"></i>
            <span>{{ now()->format('d M Y') }}</span>
        </div>
    </div>

    {{-- Flash message --}}
    @if(session('success'))
        <div class="flex items-center gap-3 rounded-xl border p-4 mb-5" style="background: #F0FFF4; border-color: #9AE6B4;">
            <i class="fa-solid fa-circle-check flex-shrink-0" style="color: #276749;"></i>
            <span class="text-sm font-medium" style="color: #276749;">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Tab switcher --}}
    <div class="flex gap-1 mb-5 p-1 rounded-xl w-fit" style="background: #F3E8E8;">
        <button id="tab-rj" onclick="switchTab('rj')"
                class="tab-btn px-5 py-2 rounded-lg text-sm font-semibold transition-all"
                style="background: #7B1D1D; color: #fff;">
            <i class="fa-solid fa-person-walking mr-1.5"></i>Rawat Jalan
        </button>
        <button id="tab-ri" onclick="switchTab('ri')"
                class="tab-btn px-5 py-2 rounded-lg text-sm font-semibold transition-all"
                style="color: #7B1D1D;">
            <i class="fa-solid fa-bed mr-1.5"></i>Rawat Inap
        </button>
    </div>

    {{-- Tab: Rawat Jalan --}}
    <div id="panel-rj">
        <div class="bg-white rounded-xl border overflow-hidden" style="border-color: #E8D5D5; box-shadow: 0 1px 3px rgba(123,29,29,0.06);">
            <div class="px-5 py-3.5 border-b flex items-center justify-between" style="background: #FDF8F8; border-color: #E8D5D5;">
                <span class="text-sm font-semibold flex items-center gap-2" style="color: #1A0A0A;">
                    <i class="fa-solid fa-person-walking" style="color: #7B1D1D;"></i>
                    Permintaan Radiologi — Rawat Jalan
                </span>
                <span class="text-xs px-2.5 py-1 rounded-full font-medium" style="background: #F3E8E8; color: #7B1D1D;">
                    {{ $rawatJalan->total() }} permintaan
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead style="background: #FDF8F8;">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">No. Rawat</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Nama Pasien</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Jenis Pemeriksaan</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Waktu</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: #F3E8E8;">
                        @forelse($rawatJalan as $req)
                            <tr class="transition-colors" onmouseover="this.style.background='#FDF8F8'" onmouseout="this.style.background='transparent'">
                                <td class="px-5 py-3.5 font-mono text-xs" style="color: #6B4C4C;">
                                    {{ $req->visit?->no_rawat ?? '-' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                             style="background: linear-gradient(135deg, #7B1D1D, #9B2C2C);">
                                            {{ strtoupper(substr($req->visit?->patient?->nama_lengkap ?? 'P', 0, 1)) }}
                                        </div>
                                        <span class="font-medium" style="color: #1A0A0A;">{{ $req->visit?->patient?->nama_lengkap ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5" style="color: #1A0A0A;">{{ $req->examinationType?->name ?? '-' }}</td>
                                <td class="px-5 py-3.5">
                                    @php
                                        $statusStyle = match($req->status) {
                                            'completed'   => 'background:#F0FFF4;color:#276749;border:1px solid #9AE6B4',
                                            'in_progress' => 'background:#EFF6FF;color:#1D4ED8;border:1px solid #BFDBFE',
                                            default       => 'background:#FFFBEB;color:#B7791F;border:1px solid #FDE68A',
                                        };
                                        $statusLabel = match($req->status) {
                                            'completed'   => 'Selesai',
                                            'in_progress' => 'Diproses',
                                            default       => 'Pending',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="{{ $statusStyle }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-xs" style="color: #6B4C4C;">
                                    {{ $req->created_at?->format('d/m/Y H:i') ?? '-' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <a href="{{ route('radiology.show', $req->id) }}"
                                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white transition-all hover:opacity-90"
                                       style="background: linear-gradient(135deg, #7B1D1D, #9B2C2C);">
                                        <i class="fa-solid fa-x-ray"></i>
                                        Input Hasil
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <i class="fa-solid fa-x-ray text-3xl mb-3 block" style="color: #E8D5D5;"></i>
                                    <p class="text-sm font-medium" style="color: #6B4C4C;">Tidak ada permintaan radiologi rawat jalan</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($rawatJalan->hasPages())
                <div class="px-5 py-3 border-t" style="border-color: #E8D5D5; background: #FDF8F8;">
                    {{ $rawatJalan->appends(['ri_page' => request('ri_page')])->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Tab: Rawat Inap --}}
    <div id="panel-ri" style="display: none;">
        <div class="bg-white rounded-xl border overflow-hidden" style="border-color: #E8D5D5; box-shadow: 0 1px 3px rgba(123,29,29,0.06);">
            <div class="px-5 py-3.5 border-b flex items-center justify-between" style="background: #FDF8F8; border-color: #E8D5D5;">
                <span class="text-sm font-semibold flex items-center gap-2" style="color: #1A0A0A;">
                    <i class="fa-solid fa-bed" style="color: #7B1D1D;"></i>
                    Permintaan Radiologi — Rawat Inap
                </span>
                <span class="text-xs px-2.5 py-1 rounded-full font-medium" style="background: #F3E8E8; color: #7B1D1D;">
                    {{ $rawatInap->total() }} permintaan
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead style="background: #FDF8F8;">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">No. Rawat</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Nama Pasien</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Jenis Pemeriksaan</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Waktu</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: #F3E8E8;">
                        @forelse($rawatInap as $req)
                            <tr class="transition-colors" onmouseover="this.style.background='#FDF8F8'" onmouseout="this.style.background='transparent'">
                                <td class="px-5 py-3.5 font-mono text-xs" style="color: #6B4C4C;">
                                    {{ $req->visit?->no_rawat ?? '-' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                             style="background: linear-gradient(135deg, #7B1D1D, #9B2C2C);">
                                            {{ strtoupper(substr($req->visit?->patient?->nama_lengkap ?? 'P', 0, 1)) }}
                                        </div>
                                        <span class="font-medium" style="color: #1A0A0A;">{{ $req->visit?->patient?->nama_lengkap ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5" style="color: #1A0A0A;">{{ $req->examinationType?->name ?? '-' }}</td>
                                <td class="px-5 py-3.5">
                                    @php
                                        $statusStyle = match($req->status) {
                                            'completed'   => 'background:#F0FFF4;color:#276749;border:1px solid #9AE6B4',
                                            'in_progress' => 'background:#EFF6FF;color:#1D4ED8;border:1px solid #BFDBFE',
                                            default       => 'background:#FFFBEB;color:#B7791F;border:1px solid #FDE68A',
                                        };
                                        $statusLabel = match($req->status) {
                                            'completed'   => 'Selesai',
                                            'in_progress' => 'Diproses',
                                            default       => 'Pending',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="{{ $statusStyle }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-xs" style="color: #6B4C4C;">
                                    {{ $req->created_at?->format('d/m/Y H:i') ?? '-' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <a href="{{ route('radiology.show', $req->id) }}"
                                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white transition-all hover:opacity-90"
                                       style="background: linear-gradient(135deg, #7B1D1D, #9B2C2C);">
                                        <i class="fa-solid fa-x-ray"></i>
                                        Input Hasil
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <i class="fa-solid fa-x-ray text-3xl mb-3 block" style="color: #E8D5D5;"></i>
                                    <p class="text-sm font-medium" style="color: #6B4C4C;">Tidak ada permintaan radiologi rawat inap</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($rawatInap->hasPages())
                <div class="px-5 py-3 border-t" style="border-color: #E8D5D5; background: #FDF8F8;">
                    {{ $rawatInap->appends(['rj_page' => request('rj_page')])->links() }}
                </div>
            @endif
        </div>
    </div>

</div>

@push('scripts')
<script>
function switchTab(tab) {
    const panels = { rj: document.getElementById('panel-rj'), ri: document.getElementById('panel-ri') };
    const btns   = { rj: document.getElementById('tab-rj'),   ri: document.getElementById('tab-ri') };

    Object.keys(panels).forEach(function(key) {
        panels[key].style.display = key === tab ? 'block' : 'none';
        if (key === tab) {
            btns[key].style.background = '#7B1D1D';
            btns[key].style.color = '#fff';
        } else {
            btns[key].style.background = 'transparent';
            btns[key].style.color = '#7B1D1D';
        }
    });
}
</script>
@endpush
@endsection
