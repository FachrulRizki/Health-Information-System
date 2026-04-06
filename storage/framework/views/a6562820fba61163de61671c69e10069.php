<?php $__env->startSection('title', 'Berkas Digital'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <span class="font-medium" style="color: #1A0A0A;">Berkas Digital</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="fade-in" style="min-width:0; overflow-x:hidden;">

<div class="mb-6">
    <h2 class="text-xl font-bold" style="color: #1A0A0A;">Berkas Digital Pasien</h2>
    <p class="text-sm mt-0.5" style="color: #6B4C4C;">Cari pasien untuk melihat repositori berkas digital dan klaim</p>
</div>


<div class="bg-white rounded-2xl p-5 mb-6" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <form method="GET" class="flex gap-3">
        <div class="relative flex-1">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2" style="color: #9B7B7B;">
                <i class="fa-solid fa-magnifying-glass text-sm"></i>
            </span>
            <input type="text" name="q" value="<?php echo e($q); ?>"
                   placeholder="Cari nama pasien, No. RM, atau No. BPJS..."
                   class="w-full border rounded-xl pl-10 pr-4 py-3 text-sm focus:outline-none focus:ring-2"
                   style="border-color: #E8D5D5; --tw-ring-color: rgba(123,29,29,0.15); color: #1A0A0A;"
                   autofocus>
        </div>
        <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
                style="background: linear-gradient(135deg, #7B1D1D, #5C1414); box-shadow: 0 2px 8px rgba(123,29,29,0.3);">
            <i class="fa-solid fa-search"></i> Cari
        </button>
        <?php if($q): ?>
        <a href="<?php echo e(route('berkas-digital.index')); ?>"
           class="inline-flex items-center gap-2 px-4 py-3 rounded-xl text-sm font-medium border transition-all hover:bg-red-50"
           style="color: #6B4C4C; border-color: #E8D5D5;">
            <i class="fa-solid fa-xmark"></i> Reset
        </a>
        <?php endif; ?>
    </form>
</div>

<?php if($q === ''): ?>

<div class="bg-white rounded-2xl p-12 text-center" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <div class="w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-4"
         style="background: linear-gradient(135deg, #FFF5F5, #FFE8E8); border: 1px solid #F0E8E8;">
        <i class="fa-solid fa-folder-open text-3xl" style="color: #7B1D1D;"></i>
    </div>
    <h3 class="text-base font-bold mb-2" style="color: #1A0A0A;">Cari Pasien</h3>
    <p class="text-sm" style="color: #6B4C4C;">Masukkan nama pasien, No. RM, atau No. BPJS untuk melihat berkas digital</p>
</div>

<?php elseif($patients->isEmpty()): ?>

<div class="bg-white rounded-2xl p-12 text-center" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <i class="fa-solid fa-user-slash text-3xl mb-3 block" style="color: #E8D5D5;"></i>
    <p class="text-sm font-medium" style="color: #6B4C4C;">Tidak ada pasien ditemukan untuk "<strong><?php echo e($q); ?></strong>"</p>
</div>

<?php else: ?>

<div class="bg-white rounded-2xl overflow-hidden" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <div class="px-5 py-3.5 border-b flex items-center justify-between" style="background: #F9F5F5; border-color: #E8D5D5;">
        <span class="text-sm font-semibold flex items-center gap-2" style="color: #1A0A0A;">
            <i class="fa-solid fa-users" style="color: #7B1D1D;"></i>
            Hasil Pencarian: "<?php echo e($q); ?>"
        </span>
        <span class="text-xs px-2.5 py-1 rounded-full font-medium" style="background: #FFF5F5; color: #7B1D1D;">
            <?php echo e($patients->total()); ?> pasien
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead style="background: #F9F5F5;">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Pasien</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">No. RM</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Tgl Lahir</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Penjamin</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: #E8D5D5;">
                <?php $__currentLoopData = $patients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $patient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-red-50 transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                 style="background: linear-gradient(135deg, #7B1D1D, #9B2C2C);">
                                <?php echo e(strtoupper(substr($patient->nama_lengkap, 0, 1))); ?>

                            </div>
                            <div>
                                <p class="font-semibold" style="color: #1A0A0A;"><?php echo e($patient->nama_lengkap); ?></p>
                                <?php if($patient->no_bpjs): ?>
                                <p class="text-xs font-mono" style="color: #6B4C4C;">BPJS: <?php echo e($patient->no_bpjs); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 font-mono text-xs" style="color: #6B4C4C;"><?php echo e($patient->no_rm); ?></td>
                    <td class="px-5 py-3.5 text-sm" style="color: #1A0A0A;">
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
                        <a href="<?php echo e(route('claims.index', $patient->id)); ?>"
                           class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white transition-all hover:opacity-90"
                           style="background: linear-gradient(135deg, #7B1D1D, #5C1414);">
                            <i class="fa-solid fa-folder-open"></i> Lihat Berkas
                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <?php if($patients->hasPages()): ?>
    <div class="px-5 py-4 border-t" style="border-color: #E8D5D5;">
        <?php echo e($patients->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/claims/search.blade.php ENDPATH**/ ?>