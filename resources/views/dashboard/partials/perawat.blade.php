{{-- Dashboard Perawat --}}

{{-- Baris 1: Stat Cards --}}
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.5rem;">
    @php
        $pCards = [
            ['label' => 'Kunjungan Hari Ini', 'value' => $total,                  'icon' => 'fa-calendar-check', 'color' => '#7B1D1D', 'bg' => '#FFF0F0', 'ring' => '#F5C6C6'],
            ['label' => 'Tempat Tidur Tersedia', 'value' => $bedStats['available'], 'icon' => 'fa-bed',           'color' => '#166534', 'bg' => '#F0FFF4', 'ring' => '#BBF7D0'],
            ['label' => 'Tempat Tidur Terisi',   'value' => $bedStats['occupied'],  'icon' => 'fa-bed-pulse',     'color' => '#7B1D1D', 'bg' => '#FFF0F0', 'ring' => '#F5C6C6'],
            ['label' => 'Maintenance',           'value' => $bedStats['maintenance'],'icon' => 'fa-wrench',       'color' => '#92400E', 'bg' => '#FFFBEB', 'ring' => '#FDE68A'],
        ];
    @endphp
    @foreach($pCards as $card)
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

{{-- Progress Bars: RME, Resep, Resume --}}
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:1.5rem;">
    @foreach([
        ['label' => 'Kelengkapan RME', 'persen' => $persenRme,    'icon' => 'fa-file-medical',    'color' => '#7B1D1D'],
        ['label' => 'Resep Diserahkan', 'persen' => $persenResep,  'icon' => 'fa-pills',           'color' => '#166534'],
        ['label' => 'Resume Terisi',    'persen' => $persenResume, 'icon' => 'fa-clipboard-check', 'color' => '#1E40AF'],
    ] as $prog)
    <div style="background:#FFFFFF; border-radius:1rem; padding:1.25rem; border:1px solid #F0E8E8; box-shadow:0 2px 12px rgba(123,29,29,0.08);">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.75rem;">
            <div style="display:flex; align-items:center; gap:0.5rem;">
                <i class="fa-solid {{ $prog['icon'] }}" style="font-size:0.85rem; color:{{ $prog['color'] }};"></i>
                <span style="font-size:0.875rem; font-weight:700; color:#3D1515;">{{ $prog['label'] }}</span>
            </div>
            <span style="font-size:1.1rem; font-weight:800; color:{{ $prog['color'] }};">{{ $prog['persen'] }}%</span>
        </div>
        <div style="height:8px; background:#F5ECEC; border-radius:999px; overflow:hidden;">
            <div style="height:100%; width:{{ $prog['persen'] }}%; background:{{ $prog['color'] }}; border-radius:999px; transition:width 1s ease;"></div>
        </div>
    </div>
    @endforeach
</div>

{{-- Antrian Aktif --}}
<div style="background:#FFFFFF; border-radius:1rem; overflow:hidden; border:1px solid #F0E8E8; box-shadow:0 2px 12px rgba(123,29,29,0.08);">
    <div style="padding:1rem 1.25rem; border-bottom:1px solid #F0E8E8; background:#FDF8F8; display:flex; align-items:center; gap:0.5rem;">
        <i class="fa-solid fa-list-ol" style="color:#7B1D1D; font-size:0.85rem;"></i>
        <h3 style="margin:0; font-size:0.875rem; font-weight:700; color:#3D1515;">Antrian Aktif</h3>
        <span style="margin-left:auto; font-size:0.7rem; padding:0.15rem 0.6rem; border-radius:999px; font-weight:700; background:#FFF0F0; color:#7B1D1D;">{{ $queue->count() }}</span>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.875rem;">
            <thead>
                <tr style="background:#FDF8F8;">
                    <th style="padding:0.75rem 1.25rem; text-align:left; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#9B7B7B;">No.</th>
                    <th style="padding:0.75rem 1.25rem; text-align:left; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#9B7B7B;">Pasien</th>
                    <th style="padding:0.75rem 1.25rem; text-align:left; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#9B7B7B;">Poli</th>
                    <th style="padding:0.75rem 1.25rem; text-align:left; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#9B7B7B;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($queue as $entry)
                @php $isCalled = in_array($entry->status, ['called', 'dipanggil']); @endphp
                <tr style="border-top:1px solid #F5ECEC; transition:background 0.15s;" onmouseover="this.style.background='#FFF8F8'" onmouseout="this.style.background=''">
                    <td style="padding:0.75rem 1.25rem;">
                        <div style="width:2.5rem; height:2.5rem; border-radius:0.75rem; background:{{ $isCalled ? '#7B1D1D' : '#FFF0F0' }}; display:flex; align-items:center; justify-content:center; font-size:1rem; font-weight:800; color:{{ $isCalled ? '#FFFFFF' : '#7B1D1D' }};">
                            {{ $entry->queue_number }}
                        </div>
                    </td>
                    <td style="padding:0.75rem 1.25rem; font-weight:600; color:#3D1515;">{{ $entry->visit?->patient?->nama_lengkap ?? '-' }}</td>
                    <td style="padding:0.75rem 1.25rem; color:#9B7B7B;">{{ $entry->poli?->nama_poli ?? '-' }}</td>
                    <td style="padding:0.75rem 1.25rem;">
                        <span style="font-size:0.75rem; font-weight:700; padding:0.2rem 0.6rem; border-radius:999px; background:{{ $isCalled ? '#FFF0F0' : '#F9F5F5' }}; color:{{ $isCalled ? '#7B1D1D' : '#9B7B7B' }};">
                            {{ ucfirst(str_replace('_', ' ', $entry->status)) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="padding:2.5rem; text-align:center; color:#9B7B7B; font-size:0.875rem;">Tidak ada antrian aktif</td></tr>
                @endforelse
            </tbody>
        </table>
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
@endpush
