<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Poli</label>
    <input type="text" name="kode_poli" value="<?php echo e(old('kode_poli', $record?->kode_poli)); ?>" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Poli</label>
    <input type="text" name="nama_poli" value="<?php echo e(old('nama_poli', $record?->nama_poli)); ?>" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
</div>
<div class="flex items-center gap-2">
    <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $record?->is_active ?? true) ? 'checked' : ''); ?>>
    <label class="text-sm text-gray-700">Aktif</label>
</div>
<?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/master/fields/polis.blade.php ENDPATH**/ ?>