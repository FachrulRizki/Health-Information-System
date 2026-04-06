<?php $__env->startSection('title', 'Manajemen Hak Akses'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('master.dashboard')); ?>" class="hover:opacity-70 transition-opacity" style="color:#6B4C4C;">Master Data</a>
    <span style="color:#E8D5D5;">/</span>
    <span class="font-medium" style="color:#1A0A0A;">Hak Akses</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="fade-in">

<?php if(session('success')): ?>
<div class="flex items-center gap-3 rounded-xl border p-4 mb-5" style="background:#F0FFF4; border-color:#9AE6B4;">
    <i class="fa-solid fa-circle-check flex-shrink-0" style="color:#276749;"></i>
    <span class="text-sm font-medium" style="color:#276749;"><?php echo e(session('success')); ?></span>
</div>
<?php endif; ?>

<div class="mb-6">
    <h2 class="text-xl font-bold" style="color:#1A0A0A;">Manajemen Hak Akses</h2>
    <p class="text-sm mt-0.5" style="color:#6B4C4C;">Atur hak akses menu per peran pengguna</p>
</div>


<div class="flex items-start gap-3 rounded-xl border p-4 mb-6" style="background:#FFFBEB; border-color:#FDE68A;">
    <i class="fa-solid fa-circle-info flex-shrink-0 mt-0.5" style="color:#B7791F;"></i>
    <p class="text-sm" style="color:#92400E;">
        Peran <strong>Admin</strong> memiliki akses penuh ke semua menu dan tidak dapat diubah.
        Pengaturan di bawah berlaku untuk semua pengguna dengan peran yang sama.
    </p>
</div>

<?php
$roleConfig = [
    'dokter'               => ['icon'=>'fa-user-doctor',    'label'=>'Dokter',               'color'=>'#7B1D1D', 'bg'=>'#FFF0F0'],
    'perawat'              => ['icon'=>'fa-user-nurse',     'label'=>'Perawat',              'color'=>'#276749', 'bg'=>'#F0FFF4'],
    'farmasi'              => ['icon'=>'fa-pills',          'label'=>'Farmasi',              'color'=>'#1E40AF', 'bg'=>'#DBEAFE'],
    'kasir'                => ['icon'=>'fa-cash-register',  'label'=>'Kasir',                'color'=>'#6D28D9', 'bg'=>'#F5F3FF'],
    'petugas_pendaftaran'  => ['icon'=>'fa-user-plus',      'label'=>'Petugas Pendaftaran',  'color'=>'#B7791F', 'bg'=>'#FFFBEB'],
    'manajemen'            => ['icon'=>'fa-chart-bar',      'label'=>'Manajemen',            'color'=>'#0E7490', 'bg'=>'#ECFEFF'],
];
?>

<div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:1.25rem;">

    
    <div style="background:#FFFFFF; border-radius:1rem; padding:1.5rem; border:1px solid #F0E8E8; box-shadow:0 2px 12px rgba(123,29,29,0.08); opacity:0.75;">
        <div class="flex items-center gap-3 mb-4">
            <div style="width:3rem; height:3rem; border-radius:0.875rem; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg,#FFF5F5,#FFE8E8); border:1px solid #F0E8E8;">
                <i class="fa-solid fa-crown" style="color:#D4A017; font-size:1.25rem;"></i>
            </div>
            <div>
                <p style="font-size:0.9rem; font-weight:700; color:#1A0A0A; margin:0;">Admin</p>
                <p style="font-size:0.75rem; color:#9B7B7B; margin:0;">Akses penuh</p>
            </div>
        </div>
        <div class="flex items-center justify-between">
            <span style="font-size:0.75rem; padding:0.25rem 0.75rem; border-radius:999px; background:#FFF0F0; color:#7B1D1D; font-weight:600;">
                <?php echo e(\App\Models\User::where('role','admin')->count()); ?> pengguna
            </span>
            <span style="font-size:0.75rem; color:#9B7B7B; display:flex; align-items:center; gap:0.4rem;">
                <i class="fa-solid fa-lock"></i> Terkunci
            </span>
        </div>
    </div>

    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $cfg = $roleConfig[$role] ?? ['icon'=>'fa-user','label'=>ucfirst($role),'color'=>'#6B4C4C','bg'=>'#F9F5F5']; ?>
    <div style="background:#FFFFFF; border-radius:1rem; padding:1.5rem; border:1px solid #F0E8E8; box-shadow:0 2px 12px rgba(123,29,29,0.08); transition:all 0.2s ease;"
         onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 24px rgba(123,29,29,0.15)'"
         onmouseout="this.style.transform='';this.style.boxShadow='0 2px 12px rgba(123,29,29,0.08)'">
        <div class="flex items-center gap-3 mb-4">
            <div style="width:3rem; height:3rem; border-radius:0.875rem; display:flex; align-items:center; justify-content:center; background:<?php echo e($cfg['bg']); ?>; border:1px solid #F0E8E8;">
                <i class="fa-solid <?php echo e($cfg['icon']); ?>" style="color:<?php echo e($cfg['color']); ?>; font-size:1.25rem;"></i>
            </div>
            <div>
                <p style="font-size:0.9rem; font-weight:700; color:#1A0A0A; margin:0;"><?php echo e($cfg['label']); ?></p>
                <p style="font-size:0.75rem; color:#9B7B7B; margin:0;"><?php echo e($roleStats[$role] ?? 0); ?> pengguna</p>
            </div>
        </div>
        <a href="<?php echo e(route('master.permissions.show', $role)); ?>"
           class="flex items-center justify-center gap-2 w-full py-2 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
           style="background:<?php echo e($cfg['color']); ?>;">
            <i class="fa-solid fa-sliders"></i> Atur Hak Akses
        </a>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/master/permissions/index.blade.php ENDPATH**/ ?>