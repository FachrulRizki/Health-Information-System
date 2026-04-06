<?php $__env->startSection('title', 'Laporan Kunjungan'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <span style="color: #64748B;">Laporan</span>
    <i class="fa-solid fa-chevron-right text-xs mx-2" style="color: #cbd5e1;"></i>
    <span class="font-medium" style="color: #0F172A;">Kunjungan Pasien</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold" style="color: #0F172A;">Laporan Kunjungan Pasien</h2>
        <p class="text-sm mt-0.5" style="color: #64748B;">Rekap data kunjungan berdasarkan filter</p>
    </div>
</div>


<div class="bg-white rounded-xl border border-slate-200 p-5 mb-5" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
    <div class="flex items-center gap-2 mb-4">
        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: #EFF6FF;">
            <i class="fa-solid fa-filter text-xs" style="color: #2563EB;"></i>
        </div>
        <h3 class="text-sm font-semibold" style="color: #0F172A;">Filter Laporan</h3>
    </div>
    <form method="GET" action="<?php echo e(route('report.visits')); ?>">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-4">
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color: #374151;">Dari Tanggal</label>
                <input type="date" name="date_from" value="<?php echo e($filters['date_from'] ?? ''); ?>"
                       class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:border-blue-400"
                       style="color: #0F172A;">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color: #374151;">Sampai Tanggal</label>
                <input type="date" name="date_to" value="<?php echo e($filters['date_to'] ?? ''); ?>"
                       class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:border-blue-400"
                       style="color: #0F172A;">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color: #374151;">Poli</label>
                <select name="poli_id"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:border-blue-400 appearance-none"
                        style="color: #0F172A;">
                    <option value="">Semua Poli</option>
                    <?php $__currentLoopData = $polis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $poli): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($poli->id); ?>" <?php echo e(($filters['poli_id'] ?? '') == $poli->id ? 'selected' : ''); ?>>
                            <?php echo e($poli->nama_poli); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color: #374151;">Dokter</label>
                <select name="doctor_id"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:border-blue-400 appearance-none"
                        style="color: #0F172A;">
                    <option value="">Semua Dokter</option>
                    <?php $__currentLoopData = $doctors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doctor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($doctor->id); ?>" <?php echo e(($filters['doctor_id'] ?? '') == $doctor->id ? 'selected' : ''); ?>>
                            <?php echo e($doctor->nama_dokter); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color: #374151;">Jenis Penjamin</label>
                <select name="jenis_penjamin"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:border-blue-400 appearance-none"
                        style="color: #0F172A;">
                    <option value="">Semua</option>
                    <option value="umum"     <?php echo e(($filters['jenis_penjamin'] ?? '') === 'umum'     ? 'selected' : ''); ?>>Umum</option>
                    <option value="bpjs"     <?php echo e(($filters['jenis_penjamin'] ?? '') === 'bpjs'     ? 'selected' : ''); ?>>BPJS</option>
                    <option value="asuransi" <?php echo e(($filters['jenis_penjamin'] ?? '') === 'asuransi' ? 'selected' : ''); ?>>Asuransi</option>
                </select>
            </div>
        </div>
        <div class="flex gap-2">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white"
                    style="background: #2563EB;">
                <i class="fa-solid fa-magnifying-glass"></i> Tampilkan
            </button>
            <a href="<?php echo e(route('report.visits')); ?>"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium border border-slate-200 hover:bg-slate-50 transition-colors"
               style="color: #64748B;">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
        </div>
    </form>
</div>

<?php if($visits->isNotEmpty()): ?>
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
            <i class="fa-solid fa-chart-bar" style="color: #2563EB;"></i>
            <span style="color: #64748B;">Total kunjungan:</span>
            <span class="font-bold" style="color: #0F172A;"><?php echo e($visits->count()); ?></span>
        </div>
    </div>
<?php endif; ?>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead style="background: #F8FAFC;">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">No. Rawat</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Tanggal</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Nama Pasien</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">No. RM</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Poli</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Dokter</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Penjamin</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php $__empty_1 = true; $__currentLoopData = $visits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $visit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5 font-mono text-xs" style="color: #64748B;"><?php echo e($visit->no_rawat); ?></td>
                        <td class="px-5 py-3.5 text-sm" style="color: #0F172A;"><?php echo e($visit->tanggal_kunjungan?->format('d/m/Y')); ?></td>
                        <td class="px-5 py-3.5 font-medium" style="color: #0F172A;"><?php echo e($visit->patient?->nama_lengkap ?? '-'); ?></td>
                        <td class="px-5 py-3.5 font-mono text-xs" style="color: #64748B;"><?php echo e($visit->patient?->no_rm ?? '-'); ?></td>
                        <td class="px-5 py-3.5 text-sm" style="color: #0F172A;"><?php echo e($visit->poli?->nama_poli ?? '-'); ?></td>
                        <td class="px-5 py-3.5 text-sm" style="color: #0F172A;"><?php echo e($visit->doctor?->nama_dokter ?? '-'); ?></td>
                        <td class="px-5 py-3.5">
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
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="background: #ECFDF5; color: #065F46;">
                                <?php echo e($visit->status); ?>

                            </span>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center">
                            <i class="fa-solid fa-chart-bar text-3xl mb-3 block" style="color: #cbd5e1;"></i>
                            <p class="text-sm font-medium" style="color: #64748B;">
                                <?php echo e(request()->hasAny(['date_from','date_to','poli_id','doctor_id','jenis_penjamin'])
                                    ? 'Tidak ada data kunjungan.'
                                    : 'Gunakan filter di atas untuk menampilkan laporan.'); ?>

                            </p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/report/visits.blade.php ENDPATH**/ ?>