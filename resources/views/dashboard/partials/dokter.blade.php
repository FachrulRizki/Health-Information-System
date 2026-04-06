{{-- Dashboard Dokter --}}

{{-- Baris 1: Stat Cards --}}
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.5rem;">
    @php
        $dCards = [
            ['label' => 'Pasien Hari Ini',          'value' => $visits->count(),          'icon' => 'fa-user-doctor',          'color' => '#7B1D1D', 'bg' => '#FFF0F0', 'ring' => '#F5C6C6'],
            ['label' => 'Antrian Aktif',             'value' => $queue->count(),           'icon' => 'fa-list-ol',              'color' => '#1E40AF', 'bg' => '#EFF6FF', 'ring' => '#BFDBFE'],
            ['label' => 'Hasil Lab Pending',         'value' => $pendingLab->count(),      'icon' => 'fa-flask',                'color' => '#92400E', 'bg' => '#FFFBEB', 'ring' => '#FDE68A'],
            ['label' => 'Hasil Radiologi Pending',   'value' => $pendingRadiology->count(),'icon' => 'fa-x-ray',               'color' => '#166534', 'bg' => '#F0FFF4', 'ring' => '#BBF7D0'],
        ];
    @endphp
    @foreach($dCards as $card)
    <div style="background:#FFFFFF; border-radius:1rem; padding:1.25rem; display:flex; align-items:center; justify-content:space-between; border:1px solid #F0E8E8; box-shadow:0 2px 12px rgba(123,29,29,0.08);">
        <div>
            <p style="font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; color:#9B7B7B; margin:0 0 0.4rem;">{{ $card['label'] }}</p>
            <p style="font-size:2rem; font-weight:800; color:{{ $card['color'] }}; margin:0; line-height:1;"
               data-counter="{{ $card['value'] }}">{{ $card['value'] }}</p>
        </div>
        <div style="width:3.5rem; height:3.5rem; border-radius:50%; background:{{ $card['bg'] }}; border:2px solid {{ $card['ring'] }}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <i class="fa-solid {{ $card['icon'] }}" style="font-size:1.25rem; color:{{ $card['color'] }};"></i>
        </div>
    </div>
    @endforeach
</div>

{{-- Baris 2: Tabel Pasien + Antrian --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:1.5rem;">

    {{-- Tabel Pasien Hari Ini --}}
    <div style="background:#FFFFFF; border-radius:1rem; overflow:hidden; border:1px solid #F0E8E8; box-shadow:0 2px 12px rgba(123,29,29,0.08);">
        <div style="padding:1rem 1.25rem; border-bottom:1px solid #F0E8E8; background:#FDF8F8; display:flex; align-items:center; gap:0.5rem;">
            <i class="fa-solid fa-stethoscope" style="color:#7B1D1D; font-size:0.85rem;"></i>
            <h3 style="margin:0; font-size:0.875rem; font-weight:700; color:#3D1515;">Pasien Saya Hari Ini</h3>
            <span style="margin-left:auto; font-size:0.7rem; padding:0.15rem 0.6rem; border-radius:999px; font-weight:700; background:#FFF0F0; color:#7B1D1D;">{{ $visits->count() }}</span>
        </div>
        <div>
            @forelse($visits->take(8) as $v)
            @php
                $statusMap = [
                    'dalam_pemeriksaan' => ['color' => '#92400E', 'bg' => '#FFFBEB', 'label' => 'Pemeriksaan'],
                    'selesai'           => ['color' => '#166534', 'bg' => '#F0FFF4', 'label' => 'Selesai'],
                    'farmasi'           => ['color' => '#1E40AF', 'bg' => '#EFF6FF', 'label' => 'Farmasi'],
                    'kasir'             => ['color' => '#6B21A8', 'bg' => '#F5F3FF', 'label' => 'Kasir'],
                ];
                $st = $statusMap[$v->status] ?? ['color' => '#6B4C4C', 'bg' => '#F9F5F5', 'label' => ucfirst(str_replace('_', ' ', $v->status))];
            @endphp
            <div style="padding:0.75rem 1.25rem; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #F5ECEC; transition:background 0.15s;" onmouseover="this.style.background='#FFF8F8'" onmouseout="this.style.background=''">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <div style="width:2.25rem; height:2.25rem; border-radius:50%; background:linear-gradient(135deg,#7B1D1D,#9B2C2C); display:flex; align-items:center; justify-content:center; color:#FFFFFF; font-size:0.75rem; font-weight:700; flex-shrink:0;">
                        {{ strtoupper(substr($v->patient?->nama_lengkap ?? 'P', 0, 1)) }}
                    </div>
                    <div>
                        <p style="margin:0; font-size:0.875rem; font-weight:600; color:#3D1515;">{{ $v->patient?->nama_lengkap }}</p>
                        <p style="margin:0; font-size:0.75rem; color:#9B7B7B;">{{ $v->poli?->nama_poli }}</p>
                    </div>
                </div>
                <span style="font-size:0.7rem; font-weight:700; padding:0.2rem 0.6rem; border-radius:999px; background:{{ $st['bg'] }}; color:{{ $st['color'] }};">{{ $st['label'] }}</span>
            </div>
            @empty
            <div style="padding:2.5rem; text-align:center; color:#9B7B7B; font-size:0.875rem;">Tidak ada pasien hari ini</div>
            @endforelse
        </div>
    </div>

    {{-- Antrian Poli --}}
    <div style="background:#FFFFFF; border-radius:1rem; overflow:hidden; border:1px solid #F0E8E8; box-shadow:0 2px 12px rgba(123,29,29,0.08);">
        <div style="padding:1rem 1.25rem; border-bottom:1px solid #F0E8E8; background:#FDF8F8; display:flex; align-items:center; gap:0.5rem;">
            <i class="fa-solid fa-list-ol" style="color:#7B1D1D; font-size:0.85rem;"></i>
            <h3 style="margin:0; font-size:0.875rem; font-weight:700; color:#3D1515;">Antrian Aktif</h3>
            <span style="margin-left:auto; font-size:0.7rem; padding:0.15rem 0.6rem; border-radius:999px; font-weight:700; background:#FFF0F0; color:#7B1D1D;">{{ $queue->count() }}</span>
        </div>
        <div>
            @forelse($queue->take(8) as $entry)
            @php
                $isCalled = in_array($entry->status, ['called', 'dipanggil']);
            @endphp
            <div style="padding:0.75rem 1.25rem; display:flex; align-items:center; gap:1rem; border-bottom:1px solid #F5ECEC; transition:background 0.15s;" onmouseover="this.style.background='#FFF8F8'" onmouseout="this.style.background=''">
                <div style="width:2.75rem; height:2.75rem; border-radius:0.75rem; background:{{ $isCalled ? '#7B1D1D' : '#FFF0F0' }}; display:flex; align-items:center; justify-content:center; font-size:1.1rem; font-weight:800; color:{{ $isCalled ? '#FFFFFF' : '#7B1D1D' }}; flex-shrink:0; {{ $isCalled ? 'animation:pulseBg 1.5s ease infinite;' : '' }}">
                    {{ $entry->queue_number }}
                </div>
                <div style="flex:1; min-width:0;">
                    <p style="margin:0; font-size:0.875rem; font-weight:600; color:#3D1515; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $entry->visit?->patient?->nama_lengkap ?? '-' }}</p>
                    <p style="margin:0; font-size:0.75rem; color:#9B7B7B;">{{ $entry->poli?->nama_poli ?? '-' }}</p>
                </div>
                <span style="font-size:0.7rem; font-weight:700; padding:0.2rem 0.6rem; border-radius:999px; background:{{ $isCalled ? '#FFF0F0' : '#F9F5F5' }}; color:{{ $isCalled ? '#7B1D1D' : '#9B7B7B' }}; flex-shrink:0;">
                    {{ ucfirst(str_replace('_', ' ', $entry->status)) }}
                </span>
            </div>
            @empty
            <div style="padding:2.5rem; text-align:center; color:#9B7B7B; font-size:0.875rem;">Tidak ada antrian aktif</div>
            @endforelse
        </div>
    </div>
</div>

{{-- Baris 3: Notifikasi Lab & Radiologi --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">

    {{-- Hasil Lab --}}
    <div style="background:#FFFFFF; border-radius:1rem; overflow:hidden; border:1px solid #F0E8E8; box-shadow:0 2px 12px rgba(123,29,29,0.08);">
        <div style="padding:1rem 1.25rem; border-bottom:1px solid #FDE68A; background:#FFFBEB; display:flex; align-items:center; gap:0.5rem;">
            <i class="fa-solid fa-flask" style="color:#92400E; font-size:0.85rem;"></i>
            <h3 style="margin:0; font-size:0.875rem; font-weight:700; color:#3D1515;">Hasil Lab Menunggu Review</h3>
            @if($pendingLab->count() > 0)
            <span style="margin-left:auto; font-size:0.7rem; padding:0.15rem 0.6rem; border-radius:999px; font-weight:700; background:#FEF3C7; color:#92400E;">{{ $pendingLab->count() }}</span>
            @endif
        </div>
        <div>
            @forelse($pendingLab as $lab)
            <div style="padding:0.75rem 1.25rem; border-bottom:1px solid #F5ECEC; display:flex; align-items:center; gap:0.75rem; transition:background 0.15s;" onmouseover="this.style.background='#FFFBEB'" onmouseout="this.style.background=''">
                <div style="width:2rem; height:2rem; border-radius:50%; background:#FEF3C7; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fa-solid fa-flask" style="font-size:0.75rem; color:#92400E;"></i>
                </div>
                <div>
                    <p style="margin:0; font-size:0.875rem; font-weight:600; color:#3D1515;">{{ $lab->visit?->patient?->nama_lengkap }}</p>
                    <p style="margin:0; font-size:0.75rem; color:#9B7B7B;">{{ $lab->examinationType?->name ?? 'Pemeriksaan Lab' }}</p>
                </div>
            </div>
            @empty
            <div style="padding:2rem; text-align:center; color:#9B7B7B; font-size:0.875rem;">
                <i class="fa-solid fa-circle-check" style="color:#166534; font-size:1.5rem; display:block; margin-bottom:0.5rem;"></i>
                Tidak ada hasil lab pending
            </div>
            @endforelse
        </div>
    </div>

    {{-- Hasil Radiologi --}}
    <div style="background:#FFFFFF; border-radius:1rem; overflow:hidden; border:1px solid #F0E8E8; box-shadow:0 2px 12px rgba(123,29,29,0.08);">
        <div style="padding:1rem 1.25rem; border-bottom:1px solid #BFDBFE; background:#EFF6FF; display:flex; align-items:center; gap:0.5rem;">
            <i class="fa-solid fa-x-ray" style="color:#1E40AF; font-size:0.85rem;"></i>
            <h3 style="margin:0; font-size:0.875rem; font-weight:700; color:#3D1515;">Hasil Radiologi Menunggu Review</h3>
            @if($pendingRadiology->count() > 0)
            <span style="margin-left:auto; font-size:0.7rem; padding:0.15rem 0.6rem; border-radius:999px; font-weight:700; background:#DBEAFE; color:#1E40AF;">{{ $pendingRadiology->count() }}</span>
            @endif
        </div>
        <div>
            @forelse($pendingRadiology as $rad)
            <div style="padding:0.75rem 1.25rem; border-bottom:1px solid #F5ECEC; display:flex; align-items:center; gap:0.75rem; transition:background 0.15s;" onmouseover="this.style.background='#EFF6FF'" onmouseout="this.style.background=''">
                <div style="width:2rem; height:2rem; border-radius:50%; background:#DBEAFE; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fa-solid fa-x-ray" style="font-size:0.75rem; color:#1E40AF;"></i>
                </div>
                <div>
                    <p style="margin:0; font-size:0.875rem; font-weight:600; color:#3D1515;">{{ $rad->visit?->patient?->nama_lengkap }}</p>
                    <p style="margin:0; font-size:0.75rem; color:#9B7B7B;">{{ $rad->examinationType?->name ?? 'Pemeriksaan Radiologi' }}</p>
                </div>
            </div>
            @empty
            <div style="padding:2rem; text-align:center; color:#9B7B7B; font-size:0.875rem;">
                <i class="fa-solid fa-circle-check" style="color:#166534; font-size:1.5rem; display:block; margin-bottom:0.5rem;"></i>
                Tidak ada hasil radiologi pending
            </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
function animateCounter(el, target) {
    let start = 0;
    const duration = 1000;
    const step = target / (duration / 16);
    const timer = setInterval(() => {
        start += step;
        if (start >= target) { el.textContent = target; clearInterval(timer); return; }
        el.textContent = Math.floor(start);
    }, 16);
}
document.querySelectorAll('[data-counter]').forEach(el => {
    animateCounter(el, parseInt(el.dataset.counter));
});
</script>
<style>
@keyframes pulseBg {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.65; }
}
</style>
@endpush
