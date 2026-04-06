@extends('layouts.app')

@section('title', 'Detail Tagihan')

@section('breadcrumb')
    <a href="{{ route('billing.index') }}" class="hover:text-blue-600" style="color: #64748B;">Billing</a>
    <i class="fa-solid fa-chevron-right text-xs mx-2" style="color: #cbd5e1;"></i>
    <span class="font-medium" style="color: #0F172A;">Detail Tagihan</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('billing.index') }}"
           class="w-9 h-9 rounded-xl border border-slate-200 flex items-center justify-center hover:bg-slate-50 transition-colors"
           style="color: #64748B;">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>
        <h2 class="text-xl font-bold" style="color: #0F172A;">Detail Tagihan</h2>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 rounded-xl border p-4 mb-5" style="background: #ECFDF5; border-color: #A7F3D0;">
            <i class="fa-solid fa-circle-check flex-shrink-0" style="color: #10B981;"></i>
            <span class="text-sm font-medium" style="color: #065F46;">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Patient Info --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5 mb-5 grid grid-cols-2 gap-4" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
        <div>
            <p class="text-xs font-medium mb-0.5" style="color: #64748B;">Nama Pasien</p>
            <p class="font-semibold" style="color: #0F172A;">{{ $visit->patient?->nama_lengkap }}</p>
        </div>
        <div>
            <p class="text-xs font-medium mb-0.5" style="color: #64748B;">No. Rawat</p>
            <p class="font-mono font-semibold" style="color: #0F172A;">{{ $visit->no_rawat }}</p>
        </div>
        <div>
            <p class="text-xs font-medium mb-0.5" style="color: #64748B;">Poli</p>
            <p style="color: #0F172A;">{{ $visit->poli?->nama_poli }}</p>
        </div>
        <div>
            <p class="text-xs font-medium mb-0.5" style="color: #64748B;">Jenis Penjamin</p>
            @php
                $penjaminStyle = match($visit->jenis_penjamin) {
                    'bpjs'     => 'background:#EFF6FF;color:#1D4ED8',
                    'asuransi' => 'background:#F5F3FF;color:#7C3AED',
                    default    => 'background:#F8FAFC;color:#475569',
                };
            @endphp
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="{{ $penjaminStyle }}">
                {{ strtoupper($visit->jenis_penjamin) }}
            </span>
        </div>
    </div>

    {{-- Bill Items --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-5" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center gap-2" style="background: #F8FAFC;">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: #FFFBEB;">
                <i class="fa-solid fa-list-check text-xs" style="color: #F59E0B;"></i>
            </div>
            <h3 class="text-sm font-semibold" style="color: #0F172A;">Rincian Tagihan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead style="background: #F8FAFC;">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Item</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Tipe</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Harga</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Qty</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($bill->items as $item)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5 font-medium" style="color: #0F172A;">{{ $item->item_name }}</td>
                            <td class="px-5 py-3.5 text-sm" style="color: #64748B;">{{ $item->item_type }}</td>
                            <td class="px-5 py-3.5 text-right font-mono text-sm" style="color: #0F172A;">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="px-5 py-3.5 text-right text-sm" style="color: #0F172A;">{{ $item->quantity }}</td>
                            <td class="px-5 py-3.5 text-right font-mono font-semibold text-sm" style="color: #0F172A;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-sm" style="color: #94a3b8;">Belum ada item tagihan.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot style="background: #F8FAFC; border-top: 2px solid #E2E8F0;">
                    <tr>
                        <td colspan="4" class="px-5 py-4 text-right font-semibold" style="color: #0F172A;">Total Tagihan</td>
                        <td class="px-5 py-4 text-right">
                            <span class="font-mono font-bold text-lg" style="color: #2563EB;">
                                Rp {{ number_format($bill->total_amount, 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Payment --}}
    @if($bill->status === 'pending')
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center gap-2" style="background: #F8FAFC;">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: #ECFDF5;">
                <i class="fa-solid fa-money-bill-wave text-xs" style="color: #10B981;"></i>
            </div>
            <h3 class="text-sm font-semibold" style="color: #0F172A;">Proses Pembayaran</h3>
        </div>
        <form method="POST" action="{{ route('billing.payment', $bill->id) }}" class="p-5">
            @csrf
            <p class="text-sm font-semibold mb-3" style="color: #374151;">Metode Pembayaran <span style="color: #EF4444;">*</span></p>
            <div class="flex gap-3 mb-5">
                @foreach(['umum' => ['Umum (Tunai)','fa-money-bill','#10B981'], 'bpjs' => ['BPJS','fa-shield-halved','#2563EB'], 'asuransi' => ['Asuransi','fa-building-shield','#7C3AED']] as $val => [$label, $icon, $color])
                    <label class="flex-1 flex items-center gap-2.5 p-3 rounded-xl border-2 cursor-pointer transition-all
                                  {{ $visit->jenis_penjamin === $val ? 'border-blue-500 bg-blue-50' : 'border-slate-200 hover:border-slate-300' }}">
                        <input type="radio" name="payment_method" value="{{ $val }}"
                            {{ $visit->jenis_penjamin === $val ? 'checked' : '' }}
                            class="sr-only">
                        <i class="fa-solid {{ $icon }}" style="color: {{ $color }};"></i>
                        <span class="text-sm font-medium" style="color: #0F172A;">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            <button type="submit"
                    onclick="return confirm('Konfirmasi pembayaran?')"
                    class="w-full inline-flex items-center justify-center gap-2 py-3 rounded-xl text-sm font-bold text-white transition-all hover:scale-[1.01]"
                    style="background: linear-gradient(135deg, #10B981, #059669);">
                <i class="fa-solid fa-circle-check text-base"></i>
                Proses Pembayaran — Rp {{ number_format($bill->total_amount, 0, ',', '.') }}
            </button>
        </form>
    </div>
    @else
        <div class="flex items-center gap-3 rounded-xl border p-4" style="background: #ECFDF5; border-color: #A7F3D0;">
            <i class="fa-solid fa-circle-check text-xl flex-shrink-0" style="color: #10B981;"></i>
            <div>
                <p class="font-semibold text-sm" style="color: #065F46;">Tagihan Sudah Dibayar</p>
                <p class="text-xs mt-0.5" style="color: #059669;">Metode pembayaran: <strong>{{ strtoupper($bill->payment_method) }}</strong></p>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.querySelectorAll('input[name="payment_method"]').forEach(r => {
    r.addEventListener('change', function() {
        document.querySelectorAll('input[name="payment_method"]').forEach(rb => {
            const label = rb.closest('label');
            if (rb.checked) {
                label.classList.add('border-blue-500', 'bg-blue-50');
                label.classList.remove('border-slate-200');
            } else {
                label.classList.remove('border-blue-500', 'bg-blue-50');
                label.classList.add('border-slate-200');
            }
        });
    });
});
</script>
@endpush
@endsection
