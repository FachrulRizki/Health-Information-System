<?php $__env->startSection('title', 'Detail Tagihan'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('billing.index')); ?>" class="hover:text-blue-600" style="color: #64748B;">Billing</a>
    <i class="fa-solid fa-chevron-right text-xs mx-2" style="color: #cbd5e1;"></i>
    <span class="font-medium" style="color: #0F172A;">Detail Tagihan</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="<?php echo e(route('billing.index')); ?>"
           class="w-9 h-9 rounded-xl border border-slate-200 flex items-center justify-center hover:bg-slate-50 transition-colors"
           style="color: #64748B;">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>
        <h2 class="text-xl font-bold" style="color: #0F172A;">Detail Tagihan</h2>
    </div>

    <?php if(session('success')): ?>
        <div class="flex items-center gap-3 rounded-xl border p-4 mb-5" style="background: #ECFDF5; border-color: #A7F3D0;">
            <i class="fa-solid fa-circle-check flex-shrink-0" style="color: #10B981;"></i>
            <span class="text-sm font-medium" style="color: #065F46;"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    
    <div class="bg-white rounded-xl border border-slate-200 p-5 mb-5 grid grid-cols-2 gap-4" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
        <div>
            <p class="text-xs font-medium mb-0.5" style="color: #64748B;">Nama Pasien</p>
            <p class="font-semibold" style="color: #0F172A;"><?php echo e($visit->patient?->nama_lengkap); ?></p>
        </div>
        <div>
            <p class="text-xs font-medium mb-0.5" style="color: #64748B;">No. Rawat</p>
            <p class="font-mono font-semibold" style="color: #0F172A;"><?php echo e($visit->no_rawat); ?></p>
        </div>
        <div>
            <p class="text-xs font-medium mb-0.5" style="color: #64748B;">Poli</p>
            <p style="color: #0F172A;"><?php echo e($visit->poli?->nama_poli); ?></p>
        </div>
        <div>
            <p class="text-xs font-medium mb-0.5" style="color: #64748B;">Jenis Penjamin</p>
            <?php
                $penjaminStyle = match($visit->jenis_penjamin) {
                    'bpjs'     => 'background:#EFF6FF;color:#1D4ED8',
                    'asuransi' => 'background:#F5F3FF;color:#7C3AED',
                    default    => 'background:#F8FAFC;color:#475569',
                };
            ?>
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="<?php echo e($penjaminStyle); ?>">
                <?php echo e(strtoupper($visit->jenis_penjamin)); ?>

            </span>
        </div>
    </div>

    
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
                    <?php $__empty_1 = true; $__currentLoopData = $bill->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5 font-medium" style="color: #0F172A;"><?php echo e($item->item_name); ?></td>
                            <td class="px-5 py-3.5 text-sm" style="color: #64748B;"><?php echo e($item->item_type); ?></td>
                            <td class="px-5 py-3.5 text-right font-mono text-sm" style="color: #0F172A;">Rp <?php echo e(number_format($item->unit_price, 0, ',', '.')); ?></td>
                            <td class="px-5 py-3.5 text-right text-sm" style="color: #0F172A;"><?php echo e($item->quantity); ?></td>
                            <td class="px-5 py-3.5 text-right font-mono font-semibold text-sm" style="color: #0F172A;">Rp <?php echo e(number_format($item->subtotal, 0, ',', '.')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-sm" style="color: #94a3b8;">Belum ada item tagihan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot style="background: #F8FAFC; border-top: 2px solid #E2E8F0;">
                    <tr>
                        <td colspan="4" class="px-5 py-4 text-right font-semibold" style="color: #0F172A;">Total Tagihan</td>
                        <td class="px-5 py-4 text-right">
                            <span class="font-mono font-bold text-lg" style="color: #2563EB;">
                                Rp <?php echo e(number_format($bill->total_amount, 0, ',', '.')); ?>

                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    
    <?php if($bill->status === 'pending'): ?>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center gap-2" style="background: #F8FAFC;">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: #ECFDF5;">
                <i class="fa-solid fa-money-bill-wave text-xs" style="color: #10B981;"></i>
            </div>
            <h3 class="text-sm font-semibold" style="color: #0F172A;">Proses Pembayaran</h3>
        </div>
        <form method="POST" action="<?php echo e(route('billing.payment', $bill->id)); ?>" class="p-5">
            <?php echo csrf_field(); ?>
            <p class="text-sm font-semibold mb-3" style="color: #374151;">Metode Pembayaran <span style="color: #EF4444;">*</span></p>
            <div class="flex gap-3 mb-5">
                <?php $__currentLoopData = ['umum' => ['Umum (Tunai)','fa-money-bill','#10B981'], 'bpjs' => ['BPJS','fa-shield-halved','#2563EB'], 'asuransi' => ['Asuransi','fa-building-shield','#7C3AED']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => [$label, $icon, $color]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="flex-1 flex items-center gap-2.5 p-3 rounded-xl border-2 cursor-pointer transition-all
                                  <?php echo e($visit->jenis_penjamin === $val ? 'border-blue-500 bg-blue-50' : 'border-slate-200 hover:border-slate-300'); ?>">
                        <input type="radio" name="payment_method" value="<?php echo e($val); ?>"
                            <?php echo e($visit->jenis_penjamin === $val ? 'checked' : ''); ?>

                            class="sr-only">
                        <i class="fa-solid <?php echo e($icon); ?>" style="color: <?php echo e($color); ?>;"></i>
                        <span class="text-sm font-medium" style="color: #0F172A;"><?php echo e($label); ?></span>
                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <button type="submit"
                    onclick="return confirm('Konfirmasi pembayaran?')"
                    class="w-full inline-flex items-center justify-center gap-2 py-3 rounded-xl text-sm font-bold text-white transition-all hover:scale-[1.01]"
                    style="background: linear-gradient(135deg, #10B981, #059669);">
                <i class="fa-solid fa-circle-check text-base"></i>
                Proses Pembayaran — Rp <?php echo e(number_format($bill->total_amount, 0, ',', '.')); ?>

            </button>
        </form>
    </div>
    <?php else: ?>
        <div class="flex items-center gap-3 rounded-xl border p-4" style="background: #ECFDF5; border-color: #A7F3D0;">
            <i class="fa-solid fa-circle-check text-xl flex-shrink-0" style="color: #10B981;"></i>
            <div>
                <p class="font-semibold text-sm" style="color: #065F46;">Tagihan Sudah Dibayar</p>
                <p class="text-xs mt-0.5" style="color: #059669;">Metode pembayaran: <strong><?php echo e(strtoupper($bill->payment_method)); ?></strong></p>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/billing/show.blade.php ENDPATH**/ ?>