<?php $__env->startSection('title', 'Berkas Digital — ' . $patient->nama_lengkap); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('berkas-digital.index')); ?>" class="hover:opacity-70 transition-opacity" style="color: #6B4C4C;">Berkas Digital</a>
    <span style="color: #E8D5D5;">/</span>
    <span class="font-medium" style="color: #1A0A0A;"><?php echo e($patient->nama_lengkap); ?></span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="fade-in" style="min-width:0; overflow-x:hidden;">


<div class="bg-white rounded-2xl p-5 mb-5" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white text-xl font-bold flex-shrink-0"
                 style="background: linear-gradient(135deg, #7B1D1D, #9B2C2C);">
                <?php echo e(strtoupper(substr($patient->nama_lengkap, 0, 1))); ?>

            </div>
            <div>
                <h2 class="text-lg font-bold" style="color: #1A0A0A;"><?php echo e($patient->nama_lengkap); ?></h2>
                <div class="flex items-center gap-3 mt-1 flex-wrap text-xs" style="color: #6B4C4C;">
                    <span><i class="fa-solid fa-id-card mr-1"></i>No. RM: <span class="font-mono font-semibold"><?php echo e($patient->no_rm); ?></span></span>
                    <?php if($patient->no_bpjs): ?>
                    <span>|</span>
                    <span><i class="fa-solid fa-shield-halved mr-1"></i>BPJS: <span class="font-mono"><?php echo e($patient->no_bpjs); ?></span></span>
                    <?php endif; ?>
                    <?php if($patient->tanggal_lahir): ?>
                    <span>|</span>
                    <span><i class="fa-solid fa-cake-candles mr-1"></i><?php echo e($patient->tanggal_lahir->format('d/m/Y')); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2 text-sm font-semibold" style="color: #7B1D1D;">
            <i class="fa-solid fa-folder-open"></i>
            <span><?php echo e($visits->count()); ?> Kunjungan</span>
        </div>
    </div>
</div>


<div class="bg-white rounded-2xl p-4 mb-5" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs font-semibold mb-1.5" style="color: #6B4C4C;">Dari Tanggal</label>
            <input type="date" name="start_date" value="<?php echo e(request('start_date')); ?>"
                   class="border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                   style="border-color: #E8D5D5; --tw-ring-color: rgba(123,29,29,0.15);">
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1.5" style="color: #6B4C4C;">Sampai Tanggal</label>
            <input type="date" name="end_date" value="<?php echo e(request('end_date')); ?>"
                   class="border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                   style="border-color: #E8D5D5; --tw-ring-color: rgba(123,29,29,0.15);">
        </div>
        <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white"
                style="background: linear-gradient(135deg, #7B1D1D, #5C1414);">
            <i class="fa-solid fa-filter"></i> Filter
        </button>
        <?php if(request('start_date') || request('end_date')): ?>
        <a href="<?php echo e(route('claims.index', $patient->id)); ?>"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium border"
           style="color: #6B4C4C; border-color: #E8D5D5;">
            <i class="fa-solid fa-xmark"></i> Reset
        </a>
        <?php endif; ?>
    </form>
</div>


<div class="bg-white rounded-2xl overflow-hidden" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <div class="px-5 py-3.5 border-b" style="background: #F9F5F5; border-color: #E8D5D5;">
        <span class="text-sm font-semibold flex items-center gap-2" style="color: #1A0A0A;">
            <i class="fa-solid fa-folder-open" style="color: #7B1D1D;"></i>
            Repositori Berkas Digital
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead style="background: #F9F5F5;">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">No. Rawat</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Tanggal</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Poli</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Penjamin</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Dokumen</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: #E8D5D5;">
                <?php $__empty_1 = true; $__currentLoopData = $visits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $visit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-red-50 transition-colors">
                    <td class="px-5 py-3.5 font-mono text-xs" style="color: #6B4C4C;"><?php echo e($visit->no_rawat); ?></td>
                    <td class="px-5 py-3.5 text-sm" style="color: #1A0A0A;">
                        <?php echo e($visit->tanggal_kunjungan?->format('d/m/Y') ?? '-'); ?>

                    </td>
                    <td class="px-5 py-3.5 text-sm" style="color: #1A0A0A;"><?php echo e($visit->poli?->nama_poli ?? '-'); ?></td>
                    <td class="px-5 py-3.5">
                        <?php
                            $pStyle = match($visit->jenis_penjamin) {
                                'bpjs'     => 'background:#EBF8FF;color:#2B6CB0',
                                'asuransi' => 'background:#FAF5FF;color:#6B21A8',
                                default    => 'background:#F9F5F5;color:#6B4C4C',
                            };
                        ?>
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold" style="<?php echo e($pStyle); ?>">
                            <?php echo e(strtoupper($visit->jenis_penjamin)); ?>

                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex gap-1 flex-wrap">
                            <?php if($visit->medicalRecord): ?>
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold" style="background:#F0FFF4;color:#276749;">RME</span>
                            <?php endif; ?>
                            <?php if($visit->diagnoses->count()): ?>
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold" style="background:#EBF8FF;color:#2B6CB0;">Diagnosa</span>
                            <?php endif; ?>
                            <?php if($visit->bill): ?>
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold" style="background:#FAF5FF;color:#6B21A8;">Tagihan</span>
                            <?php endif; ?>
                            <?php if($visit->no_sep): ?>
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold" style="background:#FFFBEB;color:#B45309;">SEP</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            <a href="<?php echo e(route('claims.show', $visit->id)); ?>"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-all hover:opacity-90"
                               style="background: #7B1D1D;">
                                <i class="fa-solid fa-eye"></i> Detail Klaim
                            </a>
                            <a href="<?php echo e(route('claims.export-pdf', $visit->id)); ?>"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all hover:bg-red-50"
                               style="color: #7B1D1D; border-color: #E8D5D5;">
                                <i class="fa-solid fa-file-pdf"></i> PDF
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center">
                        <i class="fa-solid fa-folder-open text-3xl mb-3 block" style="color: #E8D5D5;"></i>
                        <p class="text-sm font-medium" style="color: #6B4C4C;">Tidak ada kunjungan ditemukan.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/claims/index.blade.php ENDPATH**/ ?>