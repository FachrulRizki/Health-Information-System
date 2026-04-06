<?php $__env->startSection('title', 'Stok Obat'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('pharmacy.index')); ?>" class="hover:opacity-70 transition-opacity" style="color:#6B4C4C;">Farmasi</a>
    <span style="color:#E8D5D5;">/</span>
    <span class="font-medium" style="color:#1A0A0A;">Stok Obat</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="fade-in">

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold" style="color:#1A0A0A;">Stok Obat</h2>
        <p class="text-sm mt-0.5" style="color:#6B4C4C;">Pantau ketersediaan dan masa berlaku obat</p>
    </div>
    <a href="<?php echo e(route('pharmacy.index')); ?>"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium border transition-colors"
       style="color:#6B4C4C; border-color:#E8D5D5;"
       onmouseover="this.style.background='#FFF0F0'" onmouseout="this.style.background='transparent'">
        <i class="fa-solid fa-arrow-left"></i> Daftar Resep
    </a>
</div>

<?php if($hasAlerts): ?>
<div class="flex items-start gap-3 rounded-xl border p-4 mb-5" style="background:#FEF2F2; border-color:#FECACA;">
    <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background:#7B1D1D;">
        <i class="fa-solid fa-triangle-exclamation text-white text-sm"></i>
    </div>
    <div>
        <p class="font-semibold text-sm" style="color:#7B1D1D;">Peringatan Stok Obat</p>
        <p class="text-xs mt-0.5" style="color:#991B1B;">Terdapat obat yang memerlukan perhatian segera. Periksa baris yang ditandai di bawah.</p>
    </div>
</div>
<?php endif; ?>


<div class="flex flex-wrap gap-3 mb-5">
    <?php $__currentLoopData = [
        ['#FEF2F2','#FECACA','#B91C1C','Kadaluarsa'],
        ['#FFFBEB','#FDE68A','#92400E','Mendekati Kadaluarsa (<30 hari)'],
        ['#FFF7ED','#FED7AA','#C2410C','Stok Minimum'],
        ['#F0FFF4','#BBF7D0','#065F46','Normal'],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$bg,$border,$color,$label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg border text-xs font-medium"
         style="background:<?php echo e($bg); ?>; border-color:<?php echo e($border); ?>; color:<?php echo e($color); ?>;">
        <span class="w-2.5 h-2.5 rounded-full" style="background:<?php echo e($color); ?>;"></span>
        <?php echo e($label); ?>

    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div class="bg-white rounded-xl overflow-hidden" style="border:1px solid #E8D5D5; box-shadow:0 2px 8px rgba(123,29,29,0.06);">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead style="background:#FDF8F8;">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:#6B4C4C;">Nama Obat</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color:#6B4C4C;">Total Stok</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color:#6B4C4C;">Min. Stok</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:#6B4C4C;">Satuan</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:#6B4C4C;">Batch / Kadaluarsa</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:#6B4C4C;">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color:#F0E8E8;">
                <?php $__empty_1 = true; $__currentLoopData = $drugs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $rowBg = match($item['alert']) {
                        'expired'     => 'background:#FEF2F2',
                        'near_expiry' => 'background:#FFFBEB',
                        'low_stock'   => 'background:#FFF7ED',
                        default       => '',
                    };
                    $statusStyle = match($item['alert']) {
                        'expired'     => ['background:#FEF2F2;color:#B91C1C', 'Kadaluarsa'],
                        'near_expiry' => ['background:#FFFBEB;color:#92400E', 'Mendekati Kadaluarsa'],
                        'low_stock'   => ['background:#FFF7ED;color:#C2410C', 'Stok Minimum'],
                        default       => ['background:#F0FFF4;color:#065F46', 'Normal'],
                    };
                    $stockPct = $item['min_stock'] > 0
                        ? min(100, ($item['total_stock'] / ($item['min_stock'] * 3)) * 100)
                        : ($item['total_stock'] > 0 ? 100 : 0);
                    $barColor = match($item['alert']) {
                        'expired','low_stock' => '#C53030',
                        'near_expiry'         => '#B7791F',
                        default               => '#276749',
                    };
                    // Nearest expiry batch
                    $nearestBatch = $item['stocks']->sortBy('expiry_date')->first();
                ?>
                <tr style="<?php echo e($rowBg); ?>" onmouseover="this.style.filter='brightness(0.97)'" onmouseout="this.style.filter=''">
                    <td class="px-5 py-3.5 font-medium" style="color:#1A0A0A;">
                        <?php echo e($item['drug']->nama); ?>

                        <?php if($item['stocks']->count() > 1): ?>
                        <span class="ml-1 text-xs px-1.5 py-0.5 rounded-full" style="background:#F9F5F5; color:#9B7B7B;">
                            <?php echo e($item['stocks']->count()); ?> batch
                        </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex flex-col items-end gap-1">
                            <span class="font-mono font-semibold text-sm" style="color:#1A0A0A;"><?php echo e(number_format($item['total_stock'], 0)); ?></span>
                            <div class="w-16 h-1.5 rounded-full" style="background:#E8D5D5;">
                                <div class="h-1.5 rounded-full" style="width:<?php echo e($stockPct); ?>%; background:<?php echo e($barColor); ?>;"></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-right font-mono text-sm" style="color:#6B4C4C;"><?php echo e(number_format($item['min_stock'], 0)); ?></td>
                    <td class="px-5 py-3.5 text-sm" style="color:#6B4C4C;"><?php echo e($item['drug']->unit?->nama ?? '-'); ?></td>
                    <td class="px-5 py-3.5 text-xs" style="color:#6B4C4C;">
                        <?php if($nearestBatch): ?>
                            <span class="font-mono"><?php echo e($nearestBatch->batch_number ?? '-'); ?></span>
                            <?php if($nearestBatch->expiry_date): ?>
                            <br><span style="color:<?php echo e($item['alert'] === 'expired' ? '#B91C1C' : ($item['alert'] === 'near_expiry' ? '#92400E' : '#6B4C4C')); ?>;">
                                <?php echo e($nearestBatch->expiry_date->format('d/m/Y')); ?>

                            </span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span style="color:#9B7B7B;">Belum ada stok</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="<?php echo e($statusStyle[0]); ?>"><?php echo e($statusStyle[1]); ?></span>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center">
                        <i class="fa-solid fa-boxes-stacked text-3xl mb-3 block" style="color:#E8D5D5;"></i>
                        <p class="text-sm font-medium" style="color:#6B4C4C;">Tidak ada data obat aktif</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/pharmacy/stock.blade.php ENDPATH**/ ?>