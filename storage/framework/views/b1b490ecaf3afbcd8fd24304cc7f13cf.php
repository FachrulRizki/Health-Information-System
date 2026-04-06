<div>
    <label class="block text-sm font-medium mb-1" style="color:#6B4C4C;">Nama Tindakan <span style="color:#C53030;">*</span></label>
    <input type="text" name="nama" value="<?php echo e(old('nama', $record?->nama)); ?>" required
           class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
           style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);">
</div>
<div>
    <label class="block text-sm font-medium mb-1" style="color:#6B4C4C;">Kode ICD-9 CM <span class="font-normal text-xs" style="color:#9B7B7B;">(opsional)</span></label>
    <input type="text" name="icd9cm_code" value="<?php echo e(old('icd9cm_code', $record?->icd9cm_code)); ?>"
           placeholder="Contoh: 99.04"
           class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
           style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);">
</div>
<?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/master/fields/action-masters.blade.php ENDPATH**/ ?>