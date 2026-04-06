<?php $__env->startSection('title', $label); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <span style="color: #6B4C4C;">Master Data</span>
    <i class="fa-solid fa-chevron-right text-xs mx-2" style="color: #E8D5D5;"></i>
    <span class="font-medium" style="color: #1A0A0A;"><?php echo e($label); ?></span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold" style="color: #1A0A0A;">Master Data: <?php echo e($label); ?></h2>
        <p class="text-sm mt-0.5" style="color: #6B4C4C;">Kelola data <?php echo e(strtolower($label)); ?></p>
    </div>
    <a href="<?php echo e(route("{$routePrefix}.create")); ?>"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
       style="background: linear-gradient(135deg, #7B1D1D, #5C1414); box-shadow: 0 2px 8px rgba(123,29,29,0.3);">
        <i class="fa-solid fa-plus"></i>
        Tambah <?php echo e($label); ?>

    </a>
</div>

<?php if(session('success')): ?>
    <div class="flex items-center gap-3 rounded-xl border p-4 mb-5" style="background: #ECFDF5; border-color: #A7F3D0;">
        <i class="fa-solid fa-circle-check flex-shrink-0" style="color: #10B981;"></i>
        <span class="text-sm font-medium" style="color: #065F46;"><?php echo e(session('success')); ?></span>
    </div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="flex items-center gap-3 rounded-xl border p-4 mb-5" style="background: #FEF2F2; border-color: #FECACA;">
        <i class="fa-solid fa-circle-exclamation flex-shrink-0" style="color: #EF4444;"></i>
        <span class="text-sm font-medium" style="color: #B91C1C;"><?php echo e(session('error')); ?></span>
    </div>
<?php endif; ?>


<div class="bg-white rounded-xl p-4 mb-5" style="border: 1px solid #E8D5D5; box-shadow: 0 1px 3px rgba(123,29,29,0.06);">
    <form method="GET" class="flex gap-3">
        <div class="relative flex-1">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2" style="color: #9B7B7B;">
                <i class="fa-solid fa-magnifying-glass text-sm"></i>
            </span>
            <input type="text" name="q" value="<?php echo e($q); ?>"
                   placeholder="Cari <?php echo e(strtolower($label)); ?>..."
                   class="w-full border rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2"
                   style="border-color: #E8D5D5; --tw-ring-color: rgba(123,29,29,0.15); color: #1A0A0A;">
        </div>
        <button type="submit"
                class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white"
                style="background: #7B1D1D;">
            <i class="fa-solid fa-search mr-1.5"></i> Cari
        </button>
    </form>
</div>

<div class="bg-white rounded-xl overflow-hidden" style="border: 1px solid #E8D5D5; box-shadow: 0 1px 3px rgba(123,29,29,0.06);">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead style="background: #F9F5F5;">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">#</th>
                    <?php $__currentLoopData = $items->first() ? array_keys($items->first()->toArray()) : []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(!in_array($col, ['id','created_at','updated_at'])): ?>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">
                                <?php echo e(ucfirst(str_replace('_', ' ', $col))); ?>

                            </th>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: #F0E8E8;">
                <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-red-50 transition-colors">
                        <td class="px-5 py-3.5 text-xs font-mono" style="color: #9B7B7B;"><?php echo e($item->id); ?></td>
                        <?php $__currentLoopData = array_keys($item->toArray()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(!in_array($col, ['id','created_at','updated_at'])): ?>
                                <td class="px-5 py-3.5 text-sm" style="color: #1A0A0A;">
                                    <?php echo e(is_bool($item->$col) ? ($item->$col ? 'Ya' : 'Tidak') : $item->$col); ?>

                                </td>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <td class="px-5 py-3.5">
                            <div class="flex gap-2">
                                <a href="<?php echo e(route("{$routePrefix}.edit", $item->id)); ?>"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-all hover:opacity-90"
                                   style="background: #7B1D1D;">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </a>
                                <form method="POST" action="<?php echo e(route("{$routePrefix}.destroy", $item->id)); ?>"
                                      onsubmit="return confirm('Hapus data ini?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-all hover:opacity-90"
                                            style="background: #EF4444;">
                                        <i class="fa-solid fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="99" class="px-5 py-12 text-center">
                            <i class="fa-solid fa-database text-3xl mb-3 block" style="color: #E8D5D5;"></i>
                            <p class="text-sm font-medium" style="color: #6B4C4C;">Tidak ada data <?php echo e(strtolower($label)); ?></p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4"><?php echo e($items->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/master/index.blade.php ENDPATH**/ ?>