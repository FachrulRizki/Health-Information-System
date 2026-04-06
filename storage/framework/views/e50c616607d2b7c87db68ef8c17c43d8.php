<?php $__env->startSection('title', 'Pendaftaran'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <span class="font-medium" style="color: var(--color-text);">Pendaftaran</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="fade-in">


<div class="flex items-center justify-between mb-5">
    <div>
        <h2 class="text-xl font-bold" style="color: #1A0A0A;">Pendaftaran Pasien</h2>
        <p class="text-sm mt-0.5" style="color: #6B4C4C;"><?php echo e(now()->format('d F Y')); ?></p>
    </div>
    <a href="<?php echo e(route('registration.create')); ?>"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
       style="background: linear-gradient(135deg, #7B1D1D, #5C1414); box-shadow: 0 2px 8px rgba(123,29,29,0.3);">
        <i class="fa-solid fa-user-plus"></i>
        Daftar Pasien Baru
    </a>
</div>

<?php if(session('success')): ?>
<div class="flex items-center gap-3 rounded-xl border p-4 mb-5" style="background: #F0FFF4; border-color: #9AE6B4;">
    <i class="fa-solid fa-circle-check flex-shrink-0" style="color: #276749;"></i>
    <span class="text-sm font-medium" style="color: #276749;"><?php echo e(session('success')); ?></span>
</div>
<?php endif; ?>


<div class="bg-white rounded-xl p-4 mb-5" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">Cari Pasien</label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2" style="color: #6B4C4C;">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </span>
                <input type="text" name="q" value="<?php echo e($q); ?>"
                       placeholder="Cari nama pasien atau No. RM..."
                       class="w-full border rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 transition-all"
                       style="color: #1A0A0A; border-color: #E8D5D5; --tw-ring-color: rgba(123,29,29,0.15);">
            </div>
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">Dari Tanggal</label>
            <input type="date" name="date_from" value="<?php echo e($dateFrom); ?>"
                   class="border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                   style="border-color:#E8D5D5; color:#1A0A0A; --tw-ring-color:rgba(123,29,29,0.15);">
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">Sampai Tanggal</label>
            <input type="date" name="date_to" value="<?php echo e($dateTo); ?>"
                   class="border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                   style="border-color:#E8D5D5; color:#1A0A0A; --tw-ring-color:rgba(123,29,29,0.15);">
        </div>
        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white" style="background:#7B1D1D;">
            <i class="fa-solid fa-filter"></i> Filter
        </button>
        <a href="<?php echo e(route('registration.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium border" style="color:#6B4C4C; border-color:#E8D5D5;">
            <i class="fa-solid fa-rotate-left"></i> Hari Ini
        </a>
    </form>
</div>

<?php if($q !== ''): ?>

<div class="bg-white rounded-xl overflow-hidden" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <div class="px-5 py-3.5 border-b flex items-center justify-between" style="background: #F9F5F5; border-color: #E8D5D5;">
        <span class="text-sm font-semibold flex items-center gap-2" style="color: #1A0A0A;">
            <i class="fa-solid fa-magnifying-glass" style="color: #7B1D1D;"></i>
            Hasil Pencarian: "<?php echo e($q); ?>"
        </span>
        <span class="text-xs px-2.5 py-1 rounded-full font-medium" style="background: #FFF5F5; color: #7B1D1D;">
            <?php echo e($patients->count()); ?> pasien
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead style="background: #F9F5F5;">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">No. RM</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Nama Pasien</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Tgl Lahir</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Penjamin</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: #E8D5D5;">
                <?php $__empty_1 = true; $__currentLoopData = $patients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $patient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-red-50 transition-colors" style="border-bottom: 1px solid #F0E8E8;">
                    <td class="px-5 py-3.5 font-mono text-xs" style="color: #6B4C4C;"><?php echo e($patient->no_rm); ?></td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                 style="background: linear-gradient(135deg, #7B1D1D, #9B2C2C);">
                                <?php echo e(strtoupper(substr($patient->nama_lengkap ?? 'P', 0, 1))); ?>

                            </div>
                            <span class="font-medium" style="color: #1A0A0A;"><?php echo e($patient->nama_lengkap); ?></span>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-sm" style="color: #6B4C4C;">
                        <?php echo e($patient->tanggal_lahir?->format('d/m/Y') ?? '-'); ?>

                    </td>
                    <td class="px-5 py-3.5">
                        <?php
                            $pStyle = match($patient->jenis_penjamin) {
                                'bpjs'     => 'background:#EBF8FF;color:#2B6CB0',
                                'asuransi' => 'background:#FAF5FF;color:#6B21A8',
                                default    => 'background:#F9F5F5;color:#6B4C4C',
                            };
                        ?>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="<?php echo e($pStyle); ?>">
                            <?php echo e(strtoupper($patient->jenis_penjamin)); ?>

                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex gap-2">
                            <a href="<?php echo e(route('registration.show', $patient->id)); ?>"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-all hover:opacity-90"
                               style="background: #2B6CB0;">
                                <i class="fa-solid fa-eye"></i> Cek Detail
                            </a>
                            <a href="<?php echo e(route('registration.create-visit', $patient->id)); ?>"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-all hover:opacity-90"
                               style="background: #7B1D1D;">
                                <i class="fa-solid fa-calendar-plus"></i> Buat Kunjungan
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="px-5 py-12 text-center">
                        <i class="fa-solid fa-user-slash text-3xl mb-3 block" style="color: #E8D5D5;"></i>
                        <p class="text-sm font-medium" style="color: #6B4C4C;">Tidak ada pasien ditemukan untuk "<?php echo e($q); ?>"</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php else: ?>

<div class="bg-white rounded-xl overflow-hidden" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <div class="px-5 py-3.5 border-b flex items-center justify-between" style="background: #F9F5F5; border-color: #E8D5D5;">
        <span class="text-sm font-semibold flex items-center gap-2" style="color: #1A0A0A;">
            <i class="fa-solid fa-user-plus" style="color: #7B1D1D;"></i>
            Pasien Terdaftar Hari Ini
        </span>
        <span class="text-xs px-2.5 py-1 rounded-full font-medium" style="background: #FFF5F5; color: #7B1D1D;">
            <?php echo e($todayVisits->count()); ?> pasien
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead style="background: #F9F5F5;">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">No. Rawat</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">No. RM</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Nama Pasien</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Poli</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Dokter</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Penjamin</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: #E8D5D5;">
                <?php $__empty_1 = true; $__currentLoopData = $todayVisits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $visit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $statusStyle = match($visit->status) {
                        'menunggu','pendaftaran' => 'background:#FEF9C3;color:#854D0E',
                        'dipanggil','dalam_pemeriksaan' => 'background:#DBEAFE;color:#1D4ED8',
                        'farmasi' => 'background:#EDE9FE;color:#6D28D9',
                        'kasir'   => 'background:#DCFCE7;color:#166534',
                        'selesai' => 'background:#374151;color:#F9FAFB',
                        'batal'   => 'background:#FEE2E2;color:#991B1B',
                        default   => 'background:#F9F5F5;color:#6B4C4C',
                    };
                    $penjaminStyle = match($visit->jenis_penjamin) {
                        'bpjs'     => 'background:#EBF8FF;color:#2B6CB0',
                        'asuransi' => 'background:#FAF5FF;color:#6B21A8',
                        default    => 'background:#F9F5F5;color:#6B4C4C',
                    };
                ?>
                <tr class="hover:bg-red-50 transition-colors" style="border-bottom: 1px solid #F0E8E8;">
                    <td class="px-5 py-3.5 font-mono text-xs" style="color: #6B4C4C;"><?php echo e($visit->no_rawat); ?></td>
                    <td class="px-5 py-3.5 font-mono text-xs" style="color: #6B4C4C;"><?php echo e($visit->patient?->no_rm ?? '-'); ?></td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                 style="background: linear-gradient(135deg, #7B1D1D, #9B2C2C);">
                                <?php echo e(strtoupper(substr($visit->patient?->nama_lengkap ?? 'P', 0, 1))); ?>

                            </div>
                            <span class="font-medium" style="color: #1A0A0A;"><?php echo e($visit->patient?->nama_lengkap ?? '-'); ?></span>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-sm" style="color: #1A0A0A;"><?php echo e($visit->poli?->nama_poli ?? '-'); ?></td>
                    <td class="px-5 py-3.5 text-sm" style="color: #1A0A0A;"><?php echo e($visit->doctor?->nama_dokter ?? '-'); ?></td>
                    <td class="px-5 py-3.5">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="<?php echo e($penjaminStyle); ?>">
                            <?php echo e(strtoupper($visit->jenis_penjamin)); ?>

                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="<?php echo e($statusStyle); ?>">
                            <?php echo e(ucfirst(str_replace('_', ' ', $visit->status))); ?>

                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <a href="<?php echo e(route('registration.show', $visit->patient_id)); ?>"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-all hover:opacity-90"
                           style="background: #7B1D1D;">
                            <i class="fa-solid fa-eye"></i> Cek Detail
                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center">
                        <i class="fa-solid fa-user-slash text-3xl mb-3 block" style="color: #E8D5D5;"></i>
                        <p class="text-sm font-medium" style="color: #6B4C4C;">Belum ada pasien terdaftar hari ini</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/registration/index.blade.php ENDPATH**/ ?>