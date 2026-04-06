<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Kamar</label>
    <input type="text" name="kode_kamar" value="<?php echo e(old('kode_kamar', $record?->kode_kamar)); ?>" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kamar</label>
    <input type="text" name="nama_kamar" value="<?php echo e(old('nama_kamar', $record?->nama_kamar)); ?>" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
    <select name="kelas" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
        <?php $__currentLoopData = ['1','2','3','VIP']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($k); ?>" <?php echo e(old('kelas', $record?->kelas) == $k ? 'selected' : ''); ?>>Kelas <?php echo e($k); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Kapasitas</label>
    <input type="number" name="kapasitas" value="<?php echo e(old('kapasitas', $record?->kapasitas ?? 1)); ?>" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" min="1">
</div>
<?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/master/fields/rooms.blade.php ENDPATH**/ ?>