{{-- Dashboard Manajemen --}}

{{-- Baris 1: Stat Cards --}}
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.5rem;">
    @php
        $mCards = [
            [
                'label' => 'Kunjungan Hari Ini',
                'value' => $visitStats['today'],
                'icon'  => 'fa-calendar-day',
                'color' => '#7B1D1D',
                'bg'    => '#FFF0F0',
                'ring'  => '#F5C6C6',
                'counter' => true,
            ],
            [
                'label' => 'Kunjungan Bulan Ini',
                'value' => $visitStats['this_month'],
                'icon'  => 'fa-calendar-check',
                'color' => '#1E40AF',
                'bg'    => '#EFF6FF',
                'ring'  => '#BFDBFE',
                'counter' => true,
            ],
            [
                'label' => 'Pendapatan Bulan Ini',
                'value' => 'Rp ' . number_format($financialStats['paid_this_month'], 0, ',', '.'),
                'icon'  => 'fa-money-bill-trend-up',
                'color' => '#166534',
                'bg'    => '#F0FFF4',
                'ring'  => '#BBF7D0',
                'counter' => false,
            ],
            [
                'label' => 'Tagihan Pending',
                'value' => 'Rp ' . number_format($financialStats['pending_amount'], 0, ',', '.'),
                'icon'  => 'fa-clock',
                'color' => '#92400E',
                'bg'    => '#FFFBEB',
                'ring'  => '#FDE68A',
                'counter' => false,
            ],
        ];
    @endphp
    @foreach($mCards as $card)
    <div style="background:#FFFFFF; border-radius:1rem; padding:1.25rem; display:flex; align-items:center; justify-content:space-between; border:1px solid #F0E8E8; box-shadow:0 2px 12px rgba(123,29,29,0.08);">
        <div style="min-width:0; flex:1;">
            <p style="font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; color:#9B7B7B; margin:0 0 0.4rem;">{{ $card['label'] }}</p>
            <p style="font-size:{{ $card['counter'] ? '2rem' : '1.1rem' }}; font-weight:800; color:{{ $card['color'] }}; margin:0; line-height:1.1; word-break:break-all;"
               @if($card['counter']) data-counter="{{ $card['value'] }}" @endif>{{ $card['value'] }}</p>
        </div>
        <div style="width:3.5rem; height:3.5rem; border-radius:50%; background:{{ $card['bg'] }}; border:2px solid {{ $card['ring'] }}; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-left:0.75rem;">
            <i class="fa-solid {{ $card['icon'] }}" style="font-size:1.25rem; color:{{ $card['color'] }};"></i>
        </div>
    </div>
    @endforeach
</div>

{{-- Baris 2: Top 10 Penyakit + List Poliklinik --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:1.5rem;">

    {{-- Top 10 Penyakit --}}
    <div style="background:#FFFFFF; border-radius:1rem; overflow:hidden; border:1px solid #F0E8E8; box-shadow:0 2px 12px rgba(123,29,29,0.08);">
        <div style="padding:1rem 1.25rem; border-bottom:1px solid #F0E8E8; background:#FDF8F8; display:flex; align-items:center; gap:0.5rem;">
            <i class="fa-solid fa-virus" style="color:#7B1D1D; font-size:0.85rem;"></i>
            <h3 style="margin:0; font-size:0.875rem; font-weight:700; color:#3D1515;">10 Penyakit Terbanyak Hari Ini</h3>
        </div>
        <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
            @forelse($top10Penyakit as $idx => $d)
            @php
                $maxVal = $top10Penyakit->first()->total ?? 1;
                $pct    = $maxVal > 0 ? round(($d->total / $maxVal) * 100) : 0;
            @endphp
            <div style="animation: slideInLeft 0.4s ease both; animation-delay: {{ $idx * 0.06 }}s;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.25rem;">
                    <div style="display:flex; align-items:center; gap:0.5rem; min-width:0; flex:1;">
                        <span style="font-size:0.7rem; font-weight:700; color:#9B7B7B; width:1.2rem; text-align:center; flex-shrink:0;">{{ $idx + 1 }}</span>
                        <span style="font-size:0.7rem; font-family:monospace; background:#FFF0F0; color:#7B1D1D; padding:0.15rem 0.4rem; border-radius:0.3rem; flex-shrink:0; font-weight:600;">{{ $d->icd10_code }}</span>
                        <span style="font-size:0.75rem; color:#3D1515; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ Str::limit($d->icd10Code?->deskripsi ?? '-', 30) }}</span>
                    </div>
                    <span style="font-size:0.75rem; font-weight:800; color:#7B1D1D; flex-shrink:0; margin-left:0.5rem;">{{ $d->total }}</span>
                </div>
                <div style="height:6px; background:#F5ECEC; border-radius:999px; overflow:hidden;">
                    <div style="height:100%; width:{{ $pct }}%; background:linear-gradient(90deg,#7B1D1D,#C53030); border-radius:999px; transform:scaleX(0); transform-origin:left; animation:barGrow 0.6s ease {{ $idx * 0.06 + 0.2 }}s both;"></div>
                </div>
            </div>
            @empty
            <p style="text-align:center; color:#9B7B7B; font-size:0.875rem; padding:1.5rem 0;">Belum ada data diagnosa hari ini</p>
            @endforelse
        </div>
    </div>

    {{-- List Poliklinik --}}
    <div style="background:#FFFFFF; border-radius:1rem; overflow:hidden; border:1px solid #F0E8E8; box-shadow:0 2px 12px rgba(123,29,29,0.08);">
        <div style="padding:1rem 1.25rem; border-bottom:1px solid #F0E8E8; background:#FDF8F8; display:flex; align-items:center; gap:0.5rem;">
            <i class="fa-solid fa-hospital" style="color:#7B1D1D; font-size:0.85rem;"></i>
            <h3 style="margin:0; font-size:0.875rem; font-weight:700; color:#3D1515;">Pasien per Poliklinik</h3>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:0.875rem;">
                <thead>
                    <tr style="background:#FDF8F8;">
                        <th style="padding:0.75rem 1.25rem; text-align:left; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#9B7B7B;">Poliklinik</th>
                        <th style="padding:0.75rem 1.25rem; text-align:center; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#9B7B7B;">Pasien</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($poliStats as $poli)
                    <tr style="border-top:1px solid #F5ECEC; transition:background 0.15s;" onmouseover="this.style.background='#FFF8F8'" onmouseout="this.style.background=''">
                        <td style="padding:0.75rem 1.25rem; color:#3D1515; font-weight:500;">{{ $poli->nama_poli }}</td>
                        <td style="padding:0.75rem 1.25rem; text-align:center;">
                            <span style="display:inline-block; padding:0.2rem 0.75rem; border-radius:999px; font-size:0.75rem; font-weight:700; background:{{ $poli->visits_count > 0 ? '#FFF0F0' : '#F9F5F5' }}; color:{{ $poli->visits_count > 0 ? '#7B1D1D' : '#9B7B7B' }};">
                                {{ $poli->visits_count }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="2" style="padding:2rem; text-align:center; color:#9B7B7B;">Tidak ada poli aktif</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Baris 3: Progress Rings --}}
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1.5rem; margin-bottom:1.5rem;">
    @php
        $rings = [
            ['label' => 'RME CPPT',  'sub' => 'Kelengkapan rekam medis', 'pct' => $rmePercent,    'color' => '#7B1D1D', 'track' => '#F5ECEC', 'icon' => 'fa-file-medical'],
            ['label' => 'Resep',     'sub' => 'Resep telah diserahkan',   'pct' => $resepPercent,  'color' => '#166534', 'track' => '#DCFCE7', 'icon' => 'fa-pills'],
            ['label' => 'Resume',    'sub' => 'Resume medis terisi',      'pct' => $resumePercent, 'color' => '#1E40AF', 'track' => '#DBEAFE', 'icon' => 'fa-clipboard-check'],
        ];
    @endphp
    @foreach($rings as $ring)
    <div style="background:#FFFFFF; border-radius:1rem; padding:1.5rem; display:flex; flex-direction:column; align-items:center; border:1px solid #F0E8E8; box-shadow:0 2px 12px rgba(123,29,29,0.08);">
        <div style="position:relative; width:7rem; height:7rem; margin-bottom:1rem;">
            <svg viewBox="0 0 36 36" style="width:100%; height:100%; transform:rotate(-90deg);">
                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                    fill="none" stroke="{{ $ring['track'] }}" stroke-width="3.5"/>
                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                    fill="none" stroke="{{ $ring['color'] }}" stroke-width="3.5"
                    stroke-dasharray="{{ $ring['pct'] }}, 100"
                    stroke-linecap="round"
                    style="transition: stroke-dasharray 1.2s ease;"/>
            </svg>
            <div style="position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                <span style="font-size:1.4rem; font-weight:800; color:{{ $ring['color'] }}; line-height:1;">{{ $ring['pct'] }}</span>
                <span style="font-size:0.65rem; color:#9B7B7B; font-weight:600;">%</span>
            </div>
        </div>
        <div style="text-align:center;">
            <div style="display:flex; align-items:center; justify-content:center; gap:0.4rem; margin-bottom:0.25rem;">
                <i class="fa-solid {{ $ring['icon'] }}" style="font-size:0.8rem; color:{{ $ring['color'] }};"></i>
                <span style="font-size:0.9rem; font-weight:700; color:#3D1515;">{{ $ring['label'] }}</span>
            </div>
            <p style="font-size:0.75rem; color:#9B7B7B; margin:0;">{{ $ring['sub'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- Baris 4: Ringkasan Keuangan --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">

    {{-- Kunjungan per Penjamin --}}
    <div style="background:#FFFFFF; border-radius:1rem; overflow:hidden; border:1px solid #F0E8E8; box-shadow:0 2px 12px rgba(123,29,29,0.08);">
        <div style="padding:1rem 1.25rem; border-bottom:1px solid #F0E8E8; background:#FDF8F8; display:flex; align-items:center; gap:0.5rem;">
            <i class="fa-solid fa-shield-halved" style="color:#7B1D1D; font-size:0.85rem;"></i>
            <h3 style="margin:0; font-size:0.875rem; font-weight:700; color:#3D1515;">Kunjungan per Penjamin (Bulan Ini)</h3>
        </div>
        <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
            @php $totalPenjamin = $visitStats['by_penjamin']->sum(); @endphp
            @foreach($visitStats['by_penjamin'] as $penjamin => $count)
            @php
                $pct = $totalPenjamin > 0 ? round(($count / $totalPenjamin) * 100) : 0;
                $pColor = match($penjamin) { 'bpjs' => '#1E40AF', 'asuransi' => '#6B21A8', default => '#7B1D1D' };
                $pTrack = match($penjamin) { 'bpjs' => '#DBEAFE', 'asuransi' => '#F3E8FF', default => '#F5ECEC' };
            @endphp
            <div>
                <div style="display:flex; justify-content:space-between; margin-bottom:0.3rem;">
                    <span style="font-size:0.875rem; font-weight:600; color:#3D1515; text-transform:capitalize;">{{ $penjamin }}</span>
                    <span style="font-size:0.875rem; font-weight:700; color:{{ $pColor }};">{{ $count }} <span style="font-weight:400; color:#9B7B7B;">({{ $pct }}%)</span></span>
                </div>
                <div style="height:8px; background:{{ $pTrack }}; border-radius:999px; overflow:hidden;">
                    <div style="height:100%; width:{{ $pct }}%; background:{{ $pColor }}; border-radius:999px;"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Klaim BPJS --}}
    <div style="background:#FFFFFF; border-radius:1rem; overflow:hidden; border:1px solid #F0E8E8; box-shadow:0 2px 12px rgba(123,29,29,0.08);">
        <div style="padding:1rem 1.25rem; border-bottom:1px solid #F0E8E8; background:#FDF8F8; display:flex; align-items:center; gap:0.5rem;">
            <i class="fa-solid fa-file-invoice-dollar" style="color:#7B1D1D; font-size:0.85rem;"></i>
            <h3 style="margin:0; font-size:0.875rem; font-weight:700; color:#3D1515;">Klaim BPJS Diajukan</h3>
        </div>
        <div style="padding:2rem; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center;">
            <p style="font-size:1.75rem; font-weight:800; color:#7B1D1D; margin:0 0 0.5rem;">Rp {{ number_format($financialStats['bpjs_submitted'], 0, ',', '.') }}</p>
            <p style="font-size:0.875rem; color:#9B7B7B; margin:0 0 1.25rem;">Total klaim BPJS yang sedang diproses</p>
            <a href="{{ route('billing.claims') }}"
               style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.5rem 1.25rem; border-radius:0.75rem; font-size:0.875rem; font-weight:700; color:#FFFFFF; background:#7B1D1D; text-decoration:none;">
                <i class="fa-solid fa-eye"></i> Lihat Detail
            </a>
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
@keyframes slideInLeft {
    from { opacity: 0; transform: translateX(-16px); }
    to   { opacity: 1; transform: translateX(0); }
}
@keyframes barGrow {
    from { transform: scaleX(0); }
    to   { transform: scaleX(1); }
}
</style>
@endpush
