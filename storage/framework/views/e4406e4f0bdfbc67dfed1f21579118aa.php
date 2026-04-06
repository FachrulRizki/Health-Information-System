<?php $__env->startSection('title', 'Billing & Kasir'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <span class="font-medium" style="color: #7B1D1D;">Billing & Kasir</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="fade-in">

    
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold" style="color: #1A0A0A;">Billing & Kasir</h2>
            <p class="text-sm mt-0.5" style="color: #6B4C4C;">Kelola dan proses pembayaran tagihan pasien hari ini</p>
        </div>
        <a href="<?php echo e(route('billing.claims')); ?>"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
           style="background: linear-gradient(135deg, #7B1D1D, #9B2C2C);">
            <i class="fa-solid fa-shield-halved"></i>
            Monitoring Klaim BPJS
        </a>
    </div>

    
    <?php if(session('success')): ?>
        <div class="flex items-center gap-3 rounded-xl border p-4 mb-5" style="background: #F0FFF4; border-color: #9AE6B4;">
            <i class="fa-solid fa-circle-check flex-shrink-0" style="color: #276749;"></i>
            <span class="text-sm font-medium" style="color: #276749;"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    
    <div class="flex gap-1 mb-5 p-1 rounded-xl w-fit" style="background: #F3E8E8;">
        <?php $__currentLoopData = ['all' => 'Semua', 'pending' => 'Pending', 'paid' => 'Sudah Bayar']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('billing.index', ['filter' => $key])); ?>"
               class="px-5 py-2 rounded-lg text-sm font-semibold transition-all"
               style="<?php echo e($filter === $key ? 'background: #7B1D1D; color: #fff;' : 'color: #7B1D1D;'); ?>">
                <?php if($key === 'all'): ?> <i class="fa-solid fa-list mr-1.5"></i>
                <?php elseif($key === 'pending'): ?> <i class="fa-solid fa-clock mr-1.5"></i>
                <?php else: ?> <i class="fa-solid fa-circle-check mr-1.5"></i>
                <?php endif; ?>
                <?php echo e($label); ?>

            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="bg-white rounded-xl border overflow-hidden" style="border-color: #E8D5D5; box-shadow: 0 1px 3px rgba(123,29,29,0.06);">
        <div class="px-5 py-3.5 border-b flex items-center justify-between" style="background: #FDF8F8; border-color: #E8D5D5;">
            <span class="text-sm font-semibold flex items-center gap-2" style="color: #1A0A0A;">
                <i class="fa-solid fa-file-invoice-dollar" style="color: #7B1D1D;"></i>
                Pasien Hari Ini — <?php echo e(now()->format('d M Y')); ?>

            </span>
            <span class="text-xs px-2.5 py-1 rounded-full font-medium" style="background: #F3E8E8; color: #7B1D1D;">
                <?php echo e($visits->total()); ?> pasien
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead style="background: #FDF8F8;">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">No. Rawat</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Nama Pasien</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Poli</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Penjamin</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Total Tagihan</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Status Bayar</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: #F3E8E8;">
                    <?php $__empty_1 = true; $__currentLoopData = $visits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $visit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="transition-colors" onmouseover="this.style.background='#FDF8F8'" onmouseout="this.style.background='transparent'">
                            <td class="px-5 py-3.5 font-mono text-xs" style="color: #6B4C4C;">
                                <?php echo e($visit->no_rawat ?? '-'); ?>

                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                         style="background: linear-gradient(135deg, #7B1D1D, #9B2C2C);">
                                        <?php echo e(strtoupper(substr($visit->patient?->nama_lengkap ?? 'P', 0, 1))); ?>

                                    </div>
                                    <span class="font-medium" style="color: #1A0A0A;"><?php echo e($visit->patient?->nama_lengkap ?? '-'); ?></span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5" style="color: #1A0A0A;"><?php echo e($visit->poli?->nama_poli ?? '-'); ?></td>
                            <td class="px-5 py-3.5">
                                <?php
                                    $penjaminStyle = match($visit->jenis_penjamin ?? '') {
                                        'bpjs'     => 'background:#EFF6FF;color:#1D4ED8;border:1px solid #BFDBFE',
                                        'asuransi' => 'background:#F5F3FF;color:#7C3AED;border:1px solid #DDD6FE',
                                        default    => 'background:#F8FAFC;color:#475569;border:1px solid #E2E8F0',
                                    };
                                ?>
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="<?php echo e($penjaminStyle); ?>">
                                    <?php echo e(strtoupper($visit->jenis_penjamin ?? 'UMUM')); ?>

                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <?php if($visit->bill): ?>
                                    <span class="font-mono font-bold text-sm" style="color: #1A0A0A;">
                                        Rp <?php echo e(number_format($visit->bill->total_amount, 0, ',', '.')); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="text-sm" style="color: #6B4C4C;">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-3.5">
                                <?php
                                    $billStatus = $visit->bill?->status;
                                    $payStyle = match($billStatus) {
                                        'paid'    => 'background:#F0FFF4;color:#276749;border:1px solid #9AE6B4',
                                        'pending' => 'background:#FFFBEB;color:#B7791F;border:1px solid #FDE68A',
                                        default   => 'background:#F8FAFC;color:#64748B;border:1px solid #E2E8F0',
                                    };
                                    $payLabel = match($billStatus) {
                                        'paid'    => 'Lunas',
                                        'pending' => 'Pending',
                                        default   => 'Belum Ada Tagihan',
                                    };
                                ?>
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="<?php echo e($payStyle); ?>">
                                    <?php echo e($payLabel); ?>

                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <a href="<?php echo e(route('billing.show', $visit->id)); ?>"
                                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white transition-all hover:opacity-90"
                                   style="background: linear-gradient(135deg, #7B1D1D, #9B2C2C);">
                                    <i class="fa-solid fa-money-bill-wave"></i>
                                    Proses Tagihan
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <i class="fa-solid fa-file-invoice text-3xl mb-3 block" style="color: #E8D5D5;"></i>
                                <p class="text-sm font-medium" style="color: #6B4C4C;">Tidak ada pasien hari ini</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($visits->hasPages()): ?>
            <div class="px-5 py-3 border-t" style="border-color: #E8D5D5; background: #FDF8F8;">
                <?php echo e($visits->links()); ?>

            </div>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/billing/index.blade.php ENDPATH**/ ?>