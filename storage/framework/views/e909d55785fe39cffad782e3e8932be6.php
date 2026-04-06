<?php $__env->startSection('title', 'Konfirmasi Admisi'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('admisi.index')); ?>" class="hover:opacity-70 transition-opacity" style="color: #6B4C4C;">Admisi</a>
    <span style="color: #E8D5D5;">/</span>
    <span class="font-medium" style="color: #1A0A0A;">Konfirmasi Admisi</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="fade-in" style="max-width: 900px;">


<div class="bg-white rounded-2xl p-5 mb-5" style="border: 1px solid #E8D5D5; box-shadow: 0 4px 16px rgba(123,29,29,0.08);">
    <h3 class="text-sm font-bold mb-4 flex items-center gap-2" style="color: #7B1D1D;">
        <i class="fa-solid fa-user-injured"></i> Informasi Pasien
    </h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem;">
        <?php $__currentLoopData = [
            ['label' => 'Nama Pasien',  'value' => $visit->patient?->nama_lengkap ?? '-', 'icon' => 'fa-user'],
            ['label' => 'No. RM',       'value' => $visit->patient?->no_rm ?? '-',         'icon' => 'fa-hashtag'],
            ['label' => 'No. Rawat',    'value' => $visit->no_rawat,                        'icon' => 'fa-file-medical'],
            ['label' => 'Poli',         'value' => $visit->poli?->nama_poli ?? '-',         'icon' => 'fa-hospital'],
            ['label' => 'Dokter',       'value' => $visit->doctor?->nama_dokter ?? '-',     'icon' => 'fa-user-doctor'],
            ['label' => 'Penjamin',     'value' => strtoupper($visit->jenis_penjamin),      'icon' => 'fa-shield-halved'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="flex items-start gap-3 p-3 rounded-xl" style="background: #F9F5F5; border: 1px solid #F0E8E8;">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0"
                 style="background: #FFF5F5; color: #7B1D1D;">
                <i class="fa-solid <?php echo e($f['icon']); ?> text-xs"></i>
            </div>
            <div>
                <p class="text-xs" style="color: #6B4C4C;"><?php echo e($f['label']); ?></p>
                <p class="text-sm font-semibold" style="color: #1A0A0A;"><?php echo e($f['value']); ?></p>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>


<form method="POST" action="<?php echo e(route('admisi.store', $visit->id)); ?>">
    <?php echo csrf_field(); ?>

    
    <div class="bg-white rounded-2xl p-5 mb-5" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
        <h3 class="text-sm font-bold mb-4 flex items-center gap-2" style="color: #7B1D1D;">
            <i class="fa-solid fa-bed"></i> Pilih Kamar & Tempat Tidur
        </h3>

        <?php $__errorArgs = ['bed_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <div class="flex items-center gap-2 p-3 rounded-xl mb-4" style="background: #FEE2E2; border: 1px solid #FECACA;">
            <i class="fa-solid fa-circle-exclamation text-sm" style="color: #991B1B;"></i>
            <span class="text-sm" style="color: #991B1B;"><?php echo e($message); ?></span>
        </div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

        <input type="hidden" name="bed_id" id="selected_bed_id" value="<?php echo e(old('bed_id')); ?>">

        <?php $__empty_1 = true; $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php $availBeds = $room->beds->whereIn('status', ['tersedia', 'available']); ?>
        <?php if($availBeds->count() > 0): ?>
        <div class="mb-5">
            <div class="flex items-center gap-2 mb-3">
                <h4 class="text-sm font-semibold" style="color: #1A0A0A;"><?php echo e($room->nama_kamar); ?></h4>
                <?php if($room->kelas): ?>
                <span class="text-xs px-2 py-0.5 rounded-full" style="background: #EBF8FF; color: #2B6CB0;">
                    Kelas <?php echo e($room->kelas); ?>

                </span>
                <?php endif; ?>
                <span class="text-xs px-2 py-0.5 rounded-full" style="background: #DCFCE7; color: #166534;">
                    <?php echo e($availBeds->count()); ?> tersedia
                </span>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 0.75rem;">
                <?php $__currentLoopData = $availBeds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bed): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button type="button"
                        onclick="selectBed(<?php echo e($bed->id); ?>, this)"
                        data-bed-id="<?php echo e($bed->id); ?>"
                        class="bed-card p-3 rounded-xl border-2 text-left transition-all"
                        style="border-color: #E8D5D5; background: #FFFFFF; cursor: pointer;">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fa-solid fa-bed text-sm" style="color: #7B1D1D;"></i>
                        <span class="text-sm font-bold" style="color: #1A0A0A;"><?php echo e($bed->kode_bed); ?></span>
                    </div>
                    <?php if($room->kelas): ?>
                    <p class="text-xs" style="color: #6B4C4C;">Kelas <?php echo e($room->kelas); ?></p>
                    <?php endif; ?>
                    <p class="text-xs mt-1 font-medium" style="color: #276749;">Tersedia</p>
                </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="py-8 text-center">
            <i class="fa-solid fa-bed-pulse text-3xl mb-3 block" style="color: #E8D5D5;"></i>
            <p class="text-sm font-medium mb-1" style="color: #6B4C4C;">Tidak ada tempat tidur tersedia saat ini</p>
            <p class="text-xs mb-3" style="color: #9B7B7B;">Pastikan data kamar dan bed sudah diinput di Master Data.</p>
            <a href="<?php echo e(route('master.rooms.index')); ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white" style="background:#7B1D1D;">
                <i class="fa-solid fa-bed"></i> Kelola Master Kamar
            </a>
        </div>
        <?php endif; ?>
    </div>

    
    <div class="bg-white rounded-2xl p-5 mb-5" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
        <h3 class="text-sm font-bold mb-3 flex items-center gap-2" style="color: #7B1D1D;">
            <i class="fa-solid fa-notes-medical"></i> Catatan Admisi
        </h3>
        <textarea name="catatan_admisi" rows="3"
                  placeholder="Catatan tambahan untuk admisi (opsional)..."
                  class="w-full border rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 transition-all resize-none"
                  style="border-color: #E8D5D5; color: #1A0A0A; --tw-ring-color: rgba(123,29,29,0.15);"><?php echo e(old('catatan_admisi')); ?></textarea>
    </div>

    
    <div class="flex items-center gap-3">
        <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
                style="background: linear-gradient(135deg, #7B1D1D, #5C1414); box-shadow: 0 2px 8px rgba(123,29,29,0.3);">
            <i class="fa-solid fa-check-circle"></i>
            Konfirmasi Admisi
        </button>
        <a href="<?php echo e(route('admisi.index')); ?>"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium border transition-all hover:opacity-80"
           style="color: #6B4C4C; border-color: #E8D5D5;">
            <i class="fa-solid fa-xmark"></i>
            Batal
        </a>
    </div>
</form>

</div>

<?php $__env->startPush('scripts'); ?>
<script>
function selectBed(bedId, el) {
    document.getElementById('selected_bed_id').value = bedId;
    document.querySelectorAll('.bed-card').forEach(function(card) {
        card.style.borderColor = '#E8D5D5';
        card.style.background  = '#FFFFFF';
    });
    el.style.borderColor = '#7B1D1D';
    el.style.background  = '#FFF5F5';
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/admisi/confirm.blade.php ENDPATH**/ ?>