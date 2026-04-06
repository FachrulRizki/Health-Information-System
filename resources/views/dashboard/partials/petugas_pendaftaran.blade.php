{{-- Dashboard Petugas Pendaftaran --}}

{{-- Baris 1: Stat Cards --}}
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.5rem;">
    @php
        $ppCards = [
            ['label' => 'Total Kunjungan',  'value' => $total,        'icon' => 'fa-calendar-check', 'color' => '#7B1D1D', 'bg' => '#FFF0F0', 'ring' => '#F5C6C6'],
            ['label' => 'Antrian Aktif',    'value' => $queue->whereIn('status',['waiting','called'])->count(), 'icon' => 'fa-list-ol', 'color' => '#1E40AF', 'bg' => '#EFF6FF', 'ring' => '#BFDBFE'],
            ['label' => 'Selesai',          'value' => $selesai,      'icon' => 'fa-circle-check',   'color' => '#166534', 'bg' => '#F0FFF4', 'ring' => '#BBF7D0'],
            ['label' => 'Belum Selesai',    'value' => $belumSelesai, 'icon' => 'fa-hourglass-half', 'color' => '#92400E', 'bg' => '#FFFBEB', 'ring' => '#FDE68A'],
        ];
    @endphp
    @foreach($ppCards as $card)
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

{{-- Header + Tombol Daftar --}}
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
    <h3 style="margin:0; font-size:1rem; font-weight:700; color:#3D1515;">Antrian Hari Ini</h3>
    <a href="{{ route('registration.create') }}"
       style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.5rem 1.25rem; border-radius:0.75rem; font-size:0.875rem; font-weight:700; color:#FFFFFF; background:#7B1D1D; text-decoration:none;">
        <i class="fa-solid fa-user-plus"></i> Pasien Baru
    </a>
</div>

{{-- Tabel Antrian --}}
<div style="background:#FFFFFF; border-radius:1rem; overflow:hidden; border:1px solid #F0E8E8; box-shadow:0 2px 12px rgba(123,29,29,0.08);">
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
                @php
                    $isCalled = in_array($entry->status, ['called', 'dipanggil']);
                    $statusColors = [
                        'waiting'        => ['bg' => '#EFF6FF', 'color' => '#1E40AF'],
                        'called'         => ['bg' => '#FFF0F0', 'color' => '#7B1D1D'],
                        'dipanggil'      => ['bg' => '#FFF0F0', 'color' => '#7B1D1D'],
                        'in_examination' => ['bg' => '#FFFBEB', 'color' => '#92400E'],
                        'selesai'        => ['bg' => '#F0FFF4', 'color' => '#166534'],
                    ];
                    $sc = $statusColors[$entry->status] ?? ['bg' => '#F9F5F5', 'color' => '#9B7B7B'];
                @endphp
                <tr style="border-top:1px solid #F5ECEC; transition:background 0.15s;" onmouseover="this.style.background='#FFF8F8'" onmouseout="this.style.background=''">
                    <td style="padding:0.75rem 1.25rem;">
                        <div style="width:2.5rem; height:2.5rem; border-radius:0.75rem; background:{{ $isCalled ? '#7B1D1D' : '#FFF0F0' }}; display:flex; align-items:center; justify-content:center; font-size:1rem; font-weight:800; color:{{ $isCalled ? '#FFFFFF' : '#7B1D1D' }};">
                            {{ $entry->queue_number }}
                        </div>
                    </td>
                    <td style="padding:0.75rem 1.25rem; font-weight:600; color:#3D1515;">{{ $entry->visit?->patient?->nama_lengkap ?? '-' }}</td>
                    <td style="padding:0.75rem 1.25rem; color:#9B7B7B;">{{ $entry->poli?->nama_poli ?? '-' }}</td>
                    <td style="padding:0.75rem 1.25rem;">
                        <span style="font-size:0.75rem; font-weight:700; padding:0.2rem 0.6rem; border-radius:999px; background:{{ $sc['bg'] }}; color:{{ $sc['color'] }};">
                            {{ ucfirst(str_replace('_', ' ', $entry->status)) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="padding:2.5rem; text-align:center; color:#9B7B7B; font-size:0.875rem;">Belum ada antrian hari ini</td></tr>
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
