{{-- Dashboard Farmasi --}}

{{-- Baris 1: Stat Cards --}}
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.5rem;">
    @php
        $fCards = [
            ['label' => 'Resep Pending',    'value' => $prescriptions->where('status','pending')->count(),   'icon' => 'fa-prescription-bottle-medical', 'color' => '#7B1D1D', 'bg' => '#FFF0F0', 'ring' => '#F5C6C6'],
            ['label' => 'Stok Menipis',     'value' => $lowStock->count(),                                   'icon' => 'fa-triangle-exclamation',        'color' => '#92400E', 'bg' => '#FFFBEB', 'ring' => '#FDE68A'],
            ['label' => 'Hampir Kadaluarsa','value' => $nearExpiry->count(),                                 'icon' => 'fa-calendar-xmark',              'color' => '#1E40AF', 'bg' => '#EFF6FF', 'ring' => '#BFDBFE'],
            ['label' => 'Sudah Kadaluarsa', 'value' => $expired->count(),                                    'icon' => 'fa-ban',                         'color' => '#991B1B', 'bg' => '#FEF2F2', 'ring' => '#FECACA'],
        ];
    @endphp
    @foreach($fCards as $card)
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

{{-- Baris 2: Resep Pending + Stok Alerts --}}
<div style="display:grid; grid-template-columns:2fr 1fr; gap:1.5rem;">

    {{-- Resep Menunggu --}}
    <div style="background:#FFFFFF; border-radius:1rem; overflow:hidden; border:1px solid #F0E8E8; box-shadow:0 2px 12px rgba(123,29,29,0.08);">
        <div style="padding:1rem 1.25rem; border-bottom:1px solid #F0E8E8; background:#FDF8F8; display:flex; align-items:center; gap:0.5rem;">
            <i class="fa-solid fa-prescription-bottle-medical" style="color:#7B1D1D; font-size:0.85rem;"></i>
            <h3 style="margin:0; font-size:0.875rem; font-weight:700; color:#3D1515;">Resep Menunggu Proses</h3>
            <span style="margin-left:auto; font-size:0.7rem; padding:0.15rem 0.6rem; border-radius:999px; font-weight:700; background:#FFF0F0; color:#7B1D1D;">{{ $prescriptions->count() }}</span>
        </div>
        <div>
            @forelse($prescriptions as $rx)
            <div style="padding:0.75rem 1.25rem; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #F5ECEC; transition:background 0.15s;" onmouseover="this.style.background='#FFF8F8'" onmouseout="this.style.background=''">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <div style="width:2.25rem; height:2.25rem; border-radius:50%; background:linear-gradient(135deg,#7B1D1D,#9B2C2C); display:flex; align-items:center; justify-content:center; color:#FFFFFF; font-size:0.75rem; font-weight:700; flex-shrink:0;">
                        {{ strtoupper(substr($rx->visit?->patient?->nama_lengkap ?? 'P', 0, 1)) }}
                    </div>
                    <div>
                        <p style="margin:0; font-size:0.875rem; font-weight:600; color:#3D1515;">{{ $rx->visit?->patient?->nama_lengkap }}</p>
                        <p style="margin:0; font-size:0.75rem; color:#9B7B7B;">{{ ucfirst($rx->type ?? 'Resep') }}</p>
                    </div>
                </div>
                <a href="{{ route('pharmacy.show', $rx->id) }}"
                   style="font-size:0.75rem; font-weight:700; padding:0.35rem 0.9rem; border-radius:0.6rem; background:#7B1D1D; color:#FFFFFF; text-decoration:none;">
                    Proses
                </a>
            </div>
            @empty
            <div style="padding:2.5rem; text-align:center; color:#9B7B7B; font-size:0.875rem;">
                <i class="fa-solid fa-circle-check" style="color:#166534; font-size:1.5rem; display:block; margin-bottom:0.5rem;"></i>
                Tidak ada resep pending
            </div>
            @endforelse
        </div>
    </div>

    {{-- Stok Alerts --}}
    <div style="display:flex; flex-direction:column; gap:1rem;">

        {{-- Stok Menipis --}}
        <div style="background:#FFFFFF; border-radius:1rem; overflow:hidden; border:1px solid #F0E8E8; box-shadow:0 2px 12px rgba(123,29,29,0.08);">
            <div style="padding:0.875rem 1.25rem; border-bottom:1px solid #FDE68A; background:#FFFBEB; display:flex; align-items:center; gap:0.5rem;">
                <i class="fa-solid fa-triangle-exclamation" style="color:#92400E; font-size:0.85rem;"></i>
                <h3 style="margin:0; font-size:0.875rem; font-weight:700; color:#3D1515;">Stok Menipis</h3>
                <span style="margin-left:auto; font-size:0.7rem; padding:0.15rem 0.5rem; border-radius:999px; font-weight:700; background:#FEF3C7; color:#92400E;">{{ $lowStock->count() }}</span>
            </div>
            <div style="max-height:12rem; overflow-y:auto;">
                @forelse($lowStock as $s)
                <div style="padding:0.6rem 1.25rem; border-bottom:1px solid #F5ECEC;">
                    <p style="margin:0; font-size:0.8rem; font-weight:600; color:#3D1515;">{{ $s->drug?->nama_obat }}</p>
                    <p style="margin:0; font-size:0.7rem; color:#92400E;">Sisa: {{ $s->quantity }} {{ $s->drug?->satuan }}</p>
                </div>
                @empty
                <div style="padding:1rem; text-align:center; font-size:0.8rem; color:#9B7B7B;">Stok aman</div>
                @endforelse
            </div>
        </div>

        {{-- Kadaluarsa --}}
        <div style="background:#FFFFFF; border-radius:1rem; overflow:hidden; border:1px solid #F0E8E8; box-shadow:0 2px 12px rgba(123,29,29,0.08);">
            <div style="padding:0.875rem 1.25rem; border-bottom:1px solid #FECACA; background:#FEF2F2; display:flex; align-items:center; gap:0.5rem;">
                <i class="fa-solid fa-calendar-xmark" style="color:#991B1B; font-size:0.85rem;"></i>
                <h3 style="margin:0; font-size:0.875rem; font-weight:700; color:#3D1515;">Sudah Kadaluarsa</h3>
                <span style="margin-left:auto; font-size:0.7rem; padding:0.15rem 0.5rem; border-radius:999px; font-weight:700; background:#FECACA; color:#991B1B;">{{ $expired->count() }}</span>
            </div>
            <div style="max-height:12rem; overflow-y:auto;">
                @forelse($expired as $s)
                <div style="padding:0.6rem 1.25rem; border-bottom:1px solid #F5ECEC;">
                    <p style="margin:0; font-size:0.8rem; font-weight:600; color:#3D1515;">{{ $s->drug?->nama_obat }}</p>
                    <p style="margin:0; font-size:0.7rem; color:#991B1B;">Exp: {{ $s->expiry_date?->format('d/m/Y') }}</p>
                </div>
                @empty
                <div style="padding:1rem; text-align:center; font-size:0.8rem; color:#9B7B7B;">Tidak ada obat kadaluarsa</div>
                @endforelse
            </div>
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
@endpush
