<?php $__env->startSection('title', 'Hak Akses — ' . ucfirst(str_replace('_', ' ', $role))); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('master.dashboard')); ?>" class="hover:opacity-70 transition-opacity" style="color:#6B4C4C;">Master Data</a>
    <span style="color:#E8D5D5;">/</span>
    <a href="<?php echo e(route('master.permissions.index')); ?>" class="hover:opacity-70 transition-opacity" style="color:#6B4C4C;">Hak Akses</a>
    <span style="color:#E8D5D5;">/</span>
    <span class="font-medium" style="color:#1A0A0A;"><?php echo e(ucfirst(str_replace('_', ' ', $role))); ?></span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="fade-in max-w-2xl">

<div class="flex items-center gap-3 mb-6">
    <a href="<?php echo e(route('master.permissions.index')); ?>"
       class="w-9 h-9 rounded-xl border flex items-center justify-center transition-colors"
       style="border-color:#E8D5D5; color:#6B4C4C;"
       onmouseover="this.style.background='#FFF0F0'" onmouseout="this.style.background='transparent'">
        <i class="fa-solid fa-arrow-left text-sm"></i>
    </a>
    <div>
        <h2 class="text-xl font-bold" style="color:#1A0A0A;">Hak Akses: <?php echo e(ucfirst(str_replace('_', ' ', $role))); ?></h2>
        <p class="text-sm mt-0.5" style="color:#6B4C4C;">
            Berlaku untuk <?php echo e($users->count()); ?> pengguna dengan peran ini
        </p>
    </div>
</div>

<?php if($users->isEmpty()): ?>
<div class="flex items-start gap-3 rounded-xl border p-4 mb-5" style="background:#FFFBEB; border-color:#FDE68A;">
    <i class="fa-solid fa-triangle-exclamation flex-shrink-0 mt-0.5" style="color:#B7791F;"></i>
    <p class="text-sm" style="color:#92400E;">Belum ada pengguna dengan peran ini. Pengaturan akan diterapkan saat pengguna ditambahkan.</p>
</div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('master.permissions.update', $role)); ?>">
    <?php echo csrf_field(); ?>

    <div class="bg-white rounded-2xl overflow-hidden" style="border:1px solid #E8D5D5; box-shadow:0 2px 8px rgba(123,29,29,0.06);">
        <div class="px-5 py-3.5 border-b flex items-center justify-between" style="background:#FDF8F8; border-color:#E8D5D5;">
            <span class="text-sm font-semibold flex items-center gap-2" style="color:#1A0A0A;">
                <i class="fa-solid fa-shield-halved" style="color:#7B1D1D;"></i>
                Pilih Menu yang Dapat Diakses
            </span>
            <div class="flex gap-2">
                <button type="button" onclick="toggleAll(true)"
                        class="text-xs px-3 py-1 rounded-lg font-medium"
                        style="background:#F0FFF4; color:#276749;">
                    Pilih Semua
                </button>
                <button type="button" onclick="toggleAll(false)"
                        class="text-xs px-3 py-1 rounded-lg font-medium"
                        style="background:#FFF0F0; color:#7B1D1D;">
                    Hapus Semua
                </button>
            </div>
        </div>

        <div class="p-5 space-y-2">
            <?php $__empty_1 = true; $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <label class="flex items-center gap-3 p-3 rounded-xl cursor-pointer transition-colors"
                   style="border:1px solid #F0E8E8;"
                   onmouseover="this.style.background='#FFF8F8'" onmouseout="this.style.background='transparent'">
                <input type="checkbox"
                       name="permissions[]"
                       value="<?php echo e($permission->menu_key); ?>"
                       class="perm-checkbox w-4 h-4 rounded"
                       style="accent-color:#7B1D1D;"
                       <?php echo e(in_array($permission->menu_key, $grantedKeys) ? 'checked' : ''); ?>>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold" style="color:#1A0A0A; margin:0;"><?php echo e($permission->menu_label); ?></p>
                    <p class="text-xs font-mono" style="color:#9B7B7B; margin:0;"><?php echo e($permission->menu_key); ?></p>
                </div>
                <?php if($permission->parent_key): ?>
                <span class="text-xs px-2 py-0.5 rounded-full" style="background:#F9F5F5; color:#9B7B7B;">
                    <?php echo e($permission->parent_key); ?>

                </span>
                <?php endif; ?>
            </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-sm text-center py-6" style="color:#9B7B7B;">Belum ada data permission</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="flex gap-3 mt-5">
        <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
                style="background:linear-gradient(135deg,#7B1D1D,#5C1414); box-shadow:0 2px 8px rgba(123,29,29,0.3);">
            <i class="fa-solid fa-floppy-disk"></i> Simpan Hak Akses
        </button>
        <a href="<?php echo e(route('master.permissions.index')); ?>"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium border transition-colors"
           style="color:#6B4C4C; border-color:#E8D5D5;"
           onmouseover="this.style.background='#FFF0F0'" onmouseout="this.style.background='transparent'">
            <i class="fa-solid fa-xmark"></i> Batal
        </a>
    </div>
</form>

</div>

<?php $__env->startPush('scripts'); ?>
<script>
function toggleAll(state) {
    document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = state);
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/master/permissions/show.blade.php ENDPATH**/ ?>