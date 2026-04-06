<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <span style="color: #7B1D1D; font-weight: 600;">Dashboard</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="fade-in">
    <?php if($role === 'admin'): ?>
        <?php echo $__env->make('dashboard.partials.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php elseif($role === 'dokter'): ?>
        <?php echo $__env->make('dashboard.partials.dokter', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php elseif($role === 'perawat'): ?>
        <?php echo $__env->make('dashboard.partials.perawat', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php elseif($role === 'farmasi'): ?>
        <?php echo $__env->make('dashboard.partials.farmasi', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php elseif($role === 'kasir'): ?>
        <?php echo $__env->make('dashboard.partials.kasir', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php elseif($role === 'petugas_pendaftaran'): ?>
        <?php echo $__env->make('dashboard.partials.petugas_pendaftaran', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php elseif($role === 'manajemen'): ?>
        <?php echo $__env->make('dashboard.partials.manajemen', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php else: ?>
        <p style="color:#6B4C4C;">Dashboard belum tersedia untuk peran ini.</p>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/dashboard.blade.php ENDPATH**/ ?>