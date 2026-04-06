<?php
    $specializations    = \App\Models\Specialization::orderBy('nama')->get();
    $subSpecializations = \App\Models\SubSpecialization::orderBy('nama')->get();
?>

<?php if($record?->kode_dokter): ?>
<div>
    <label class="block text-sm font-medium mb-1" style="color:#6B4C4C;">Kode Dokter</label>
    <div class="w-full border rounded px-3 py-2 text-sm font-mono" style="background:#F9F5F5; border-color:#E8D5D5; color:#9B7B7B;">
        <?php echo e($record->kode_dokter); ?>

    </div>
    <p class="text-xs mt-1" style="color:#9B7B7B;">Kode dokter digenerate otomatis oleh sistem</p>
</div>
<?php else: ?>
<div>
    <label class="block text-sm font-medium mb-1" style="color:#6B4C4C;">Kode Dokter</label>
    <div class="w-full border rounded px-3 py-2 text-sm font-mono" style="background:#F9F5F5; border-color:#E8D5D5; color:#9B7B7B;">
        Akan digenerate otomatis (DOK-XXXX)
    </div>
</div>
<?php endif; ?>

<div>
    <label class="block text-sm font-medium mb-1" style="color:#6B4C4C;">Nama Dokter <span style="color:#C53030;">*</span></label>
    <input type="text" name="nama_dokter" value="<?php echo e(old('nama_dokter', $record?->nama_dokter)); ?>"
           class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
           style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);" required>
</div>
<div>
    <label class="block text-sm font-medium mb-1" style="color:#6B4C4C;">Spesialisasi <span class="font-normal text-xs" style="color:#9B7B7B;">(opsional)</span></label>
    <input type="text" name="spesialisasi_text"
           value="<?php echo e(old('spesialisasi_text', $record?->specialization?->nama)); ?>"
           placeholder="Contoh: Dokter Umum, Spesialis Penyakit Dalam..."
           class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
           style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);">
    <input type="hidden" name="specialization_id" value="<?php echo e(old('specialization_id', $record?->specialization_id)); ?>">
</div>
<div>
    <label class="block text-sm font-medium mb-1" style="color:#6B4C4C;">Sub Spesialisasi <span class="font-normal text-xs" style="color:#9B7B7B;">(opsional)</span></label>
    <input type="text" name="sub_spesialisasi_text"
           value="<?php echo e(old('sub_spesialisasi_text', $record?->subSpecialization?->nama)); ?>"
           placeholder="Contoh: Kardiologi Intervensi..."
           class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
           style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);">
    <input type="hidden" name="sub_specialization_id" value="<?php echo e(old('sub_specialization_id', $record?->sub_specialization_id)); ?>">
</div>
<div>
    <label class="block text-sm font-medium mb-1" style="color:#6B4C4C;">No. SIP</label>
    <input type="text" name="no_sip" value="<?php echo e(old('no_sip', $record?->no_sip)); ?>"
           class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
           style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);">
</div>
<div>
    <label class="block text-sm font-medium mb-1" style="color:#6B4C4C;">No. Telepon</label>
    <input type="text" name="no_telepon" value="<?php echo e(old('no_telepon', $record?->no_telepon)); ?>"
           class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
           style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);">
</div>
<div class="flex items-center gap-2">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" id="is_active"
           <?php echo e(old('is_active', $record?->is_active ?? true) ? 'checked' : ''); ?>>
    <label for="is_active" class="text-sm" style="color:#6B4C4C;">Aktif</label>
</div>
<?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/master/fields/doctors.blade.php ENDPATH**/ ?>