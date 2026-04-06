{{-- Dashboard Kasir --}}

{{-- Baris 1: Stat Cards --}}
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.5rem;">
    @php
        $kCards = [
            ['label' => 'Klaim Pending',  'value' => $claimStats['pending'],   'icon' => 'fa-clock',         'color' => '#92400E', 'bg' => '#FFFBEB', 'ring' => '#FDE68A'],
            ['label' => 'Diajukan',       'value' => $claimStats['submitted'], 'icon' => 'fa-paper-plane',   'color' => '#1E40AF', 'bg' => '#EFF6FF', 'ring' => '#BFDBFE'],
            ['label' => 'Disetujui',      'value' => $claimStats['approved'],  'icon' => 'fa-circle-check',  'color' => '#166534', 'bg' => '#F0FFF4', 'ring' => '#BBF7D0'],
            ['label' => 'Ditolak',        'value' => $claimStats['rejected'],  'icon' => 'fa-circle-xmark',  'color' => '#991B1B', 'bg' => '#FEF2F2', 'ring' => '#FECACA'],
        ];
    @endphp
    @foreach($kCards as $card)
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

{{-- Tagihan Pending --}}
<div style="background:#FFFFFF; border-radius:1rem; overflow:hidden; border:1px solid #F0E8E8; box-shadow:0 2px 12px rgba(123,29,29,0.08);">
    <div style="padding:1rem 1.25rem; border-bottom:1px solid #F0E8E8; background:#FDF8F8; display:flex; align-items:center; gap:0.5rem;">
        <i class="fa-solid fa-receipt" style="color:#7B1D1D; font-size:0.85rem;"></i>
        <h3 style="margin:0; font-size:0.875rem; font-weight:700; color:#3D1515;">Tagihan Pending</h3>
        <span style="margin-left:auto; font-size:0.7rem; padding:0.15rem 0.6rem; border-radius:999px; font-weight:700; background:#FFF0F0; color:#7B1D1D;">{{ $pendingBills->count() }}</span>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.875rem;">
            <thead>
                <tr style="background:#FDF8F8;">
                    <th style="padding:0.75rem 1.25rem; text-align:left; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#9B7B7B;">Pasien</th>
                    <th style="padding:0.75rem 1.25rem; text-align:left; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#9B7B7B;">No. Rawat</th>
                    <th style="padding:0.75rem 1.25rem; text-align:right; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#9B7B7B;">Total</th>
                    <th style="padding:0.75rem 1.25rem; text-align:center; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#9B7B7B;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingBills as $bill)
                <tr style="border-top:1px solid #F5ECEC; transition:background 0.15s;" onmouseover="this.style.background='#FFF8F8'" onmouseout="this.style.background=''">
                    <td style="padding:0.75rem 1.25rem; font-weight:600; color:#3D1515;">{{ $bill->visit?->patient?->nama_lengkap }}</td>
                    <td style="padding:0.75rem 1.25rem; font-family:monospace; font-size:0.8rem; color:#9B7B7B;">{{ $bill->visit?->no_rawat }}</td>
                    <td style="padding:0.75rem 1.25rem; text-align:right; font-family:monospace; font-weight:800; color:#7B1D1D;">Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</td>
                    <td style="padding:0.75rem 1.25rem; text-align:center;">
                        <a href="{{ route('billing.show', $bill->visit_id) }}"
                           style="font-size:0.75rem; font-weight:700; padding:0.35rem 0.9rem; border-radius:0.6rem; background:#7B1D1D; color:#FFFFFF; text-decoration:none;">
                            Bayar
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="padding:2.5rem; text-align:center; color:#9B7B7B; font-size:0.875rem;">Tidak ada tagihan pending</td></tr>
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
