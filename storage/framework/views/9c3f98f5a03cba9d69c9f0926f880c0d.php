<?php $__env->startSection('title', 'Pendaftaran Pasien Baru'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('registration.index')); ?>" class="hover:text-blue-600" style="color: #64748B;">Pendaftaran</a>
    <i class="fa-solid fa-chevron-right text-xs mx-2" style="color: #cbd5e1;"></i>
    <span class="font-medium" style="color: #0F172A;">Pasien Baru</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto">

<?php if(session('success')): ?>

<div id="success-modal" class="fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl p-8 max-w-sm w-full mx-4 text-center" style="box-shadow:0 20px 60px rgba(0,0,0,0.3);">
        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background:#F0FFF4;">
            <i class="fa-solid fa-circle-check text-3xl" style="color:#276749;"></i>
        </div>
        <h3 class="text-lg font-bold mb-2" style="color:#1A0A0A;">Pendaftaran Berhasil!</h3>
        <p class="text-sm mb-6" style="color:#6B4C4C;"><?php echo e(session('success')); ?></p>
        <div class="flex gap-3 justify-center">
            <a href="<?php echo e(route('registration.create')); ?>"
               class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white"
               style="background:#7B1D1D;">
                <i class="fa-solid fa-user-plus mr-1.5"></i> Daftar Lagi
            </a>
            <a href="<?php echo e(route('registration.index')); ?>"
               class="px-5 py-2.5 rounded-xl text-sm font-medium border"
               style="color:#6B4C4C; border-color:#E8D5D5;">
                Lihat Daftar
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

    <div class="flex items-center gap-3 mb-6">
        <a href="<?php echo e(route('registration.index')); ?>"
           class="w-9 h-9 rounded-xl border border-slate-200 flex items-center justify-center hover:bg-slate-50 transition-colors"
           style="color: #64748B;">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold" style="color: #0F172A;">Pendaftaran Pasien Baru</h2>
            <p class="text-sm mt-0.5" style="color: #64748B;">Isi data pasien, penjamin, dan kunjungan</p>
        </div>
    </div>

    
    <div class="bg-white rounded-xl border border-slate-200 p-4 mb-5 flex items-center gap-0" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
        <?php $__currentLoopData = [['1','Data Pasien','fa-user'], ['2','Penjamin','fa-shield-halved'], ['3','Kunjungan','fa-calendar-check']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-center <?php echo e($i < 2 ? 'flex-1' : ''); ?>">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                         style="background: #7B1D1D;">
                        <i class="fa-solid <?php echo e($step[2]); ?> text-xs"></i>
                    </div>
                    <span class="text-sm font-medium hidden sm:block" style="color: #0F172A;"><?php echo e($step[1]); ?></span>
                </div>
                <?php if($i < 2): ?>
                    <div class="flex-1 h-0.5 mx-3" style="background: #E2E8F0;"></div>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php if($errors->any()): ?>
        <div class="flex items-start gap-3 rounded-xl border p-4 mb-5" style="background: #FEF2F2; border-color: #FECACA;">
            <i class="fa-solid fa-circle-exclamation flex-shrink-0 mt-0.5" style="color: #EF4444;"></i>
            <div>
                <p class="text-sm font-semibold mb-1" style="color: #B91C1C;">Terdapat kesalahan pada form:</p>
                <ul class="text-sm space-y-0.5" style="color: #DC2626;">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li class="flex items-center gap-1"><i class="fa-solid fa-minus text-xs"></i> <?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <?php if(session('bpjs_inactive') || $errors->has('bpjs')): ?>
        <div class="flex items-start gap-3 rounded-xl border p-4 mb-5" style="background: #FFFBEB; border-color: #FDE68A;">
            <i class="fa-solid fa-triangle-exclamation flex-shrink-0 mt-0.5" style="color: #F59E0B;"></i>
            <div class="flex-1">
                <p class="text-sm font-semibold mb-1" style="color: #92400E;">Konfirmasi Status BPJS</p>
                <p class="text-sm mb-3" style="color: #B45309;"><?php echo e($errors->first('bpjs')); ?></p>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="bpjs_confirmed" value="1" id="bpjs_confirmed_check"
                        <?php echo e(old('bpjs_confirmed') === '1' ? 'checked' : ''); ?>

                        class="w-4 h-4 rounded" style="accent-color: #F59E0B;">
                    <span class="text-sm" style="color: #92400E;">Saya memahami status peserta tidak aktif dan tetap melanjutkan pendaftaran</span>
                </label>
            </div>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('registration.store')); ?>" class="space-y-5">
        <?php echo csrf_field(); ?>

        
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center gap-2" style="background: #F8FAFC;">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: #EFF6FF;">
                    <i class="fa-solid fa-user text-xs" style="color: #2563EB;"></i>
                </div>
                <h3 class="text-sm font-semibold" style="color: #0F172A;">Data Pasien</h3>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1.5" style="color: #374151;">Nama Lengkap <span style="color: #EF4444;">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2" style="color: #94a3b8;"><i class="fa-solid fa-user text-xs"></i></span>
                        <input type="text" name="nama_lengkap" value="<?php echo e(old('nama_lengkap')); ?>" required
                               placeholder="Nama lengkap pasien"
                               class="w-full border rounded-xl pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:border-blue-400 <?php $__errorArgs = ['nama_lengkap'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php else: ?> border-slate-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               style="color: #0F172A;">
                    </div>
                    <?php $__errorArgs = ['nama_lengkap'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs mt-1 flex items-center gap-1" style="color: #EF4444;"><i class="fa-solid fa-circle-exclamation"></i> <?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: #374151;">Tanggal Lahir <span style="color: #EF4444;">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2" style="color: #94a3b8;"><i class="fa-solid fa-calendar text-xs"></i></span>
                        <input type="date" name="tanggal_lahir" value="<?php echo e(old('tanggal_lahir')); ?>" required
                               max="<?php echo e(date('Y-m-d', strtotime('-1 day'))); ?>"
                               class="w-full border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:border-blue-400"
                               style="color: #0F172A;">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: #374151;">Jenis Kelamin <span style="color: #EF4444;">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2" style="color: #94a3b8;"><i class="fa-solid fa-venus-mars text-xs"></i></span>
                        <select name="jenis_kelamin" required
                                class="w-full border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:border-blue-400 appearance-none"
                                style="color: #0F172A;">
                            <option value="">-- Pilih --</option>
                            <option value="L" <?php echo e(old('jenis_kelamin') === 'L' ? 'selected' : ''); ?>>Laki-laki</option>
                            <option value="P" <?php echo e(old('jenis_kelamin') === 'P' ? 'selected' : ''); ?>>Perempuan</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: #374151;">NIK</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2" style="color: #94a3b8;"><i class="fa-solid fa-id-card text-xs"></i></span>
                        <input type="text" name="nik" value="<?php echo e(old('nik')); ?>" maxlength="16"
                               placeholder="16 digit NIK"
                               class="w-full border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:border-blue-400"
                               style="color: #0F172A;">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: #374151;">No. Telepon</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2" style="color: #94a3b8;"><i class="fa-solid fa-phone text-xs"></i></span>
                        <input type="text" name="no_telepon" value="<?php echo e(old('no_telepon')); ?>" maxlength="20"
                               placeholder="08xx-xxxx-xxxx"
                               class="w-full border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:border-blue-400"
                               style="color: #0F172A;">
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold mb-1.5" style="color: #374151;">Alamat</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-3" style="color: #94a3b8;"><i class="fa-solid fa-location-dot text-xs"></i></span>
                        <textarea name="alamat" rows="2"
                                  placeholder="Alamat lengkap pasien"
                                  class="w-full border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:border-blue-400"
                                  style="color: #0F172A;"><?php echo e(old('alamat')); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center gap-2" style="background: #F8FAFC;">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: #ECFDF5;">
                    <i class="fa-solid fa-shield-halved text-xs" style="color: #10B981;"></i>
                </div>
                <h3 class="text-sm font-semibold" style="color: #0F172A;">Jenis Penjamin</h3>
            </div>
            <div class="p-5">
                <div class="flex gap-3 mb-4">
                    <?php $__currentLoopData = ['umum' => ['Umum','fa-wallet','#64748B'], 'bpjs' => ['BPJS','fa-shield-halved','#2563EB'], 'asuransi' => ['Asuransi','fa-building-shield','#7C3AED']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => [$label, $icon, $color]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="flex-1 flex items-center gap-2.5 p-3 rounded-xl border-2 cursor-pointer transition-all
                                      <?php echo e(old('jenis_penjamin', 'umum') === $val ? 'border-blue-500 bg-blue-50' : 'border-slate-200 hover:border-slate-300'); ?>">
                            <input type="radio" name="jenis_penjamin" value="<?php echo e($val); ?>"
                                <?php echo e(old('jenis_penjamin', 'umum') === $val ? 'checked' : ''); ?>

                                onchange="togglePenjamin(this.value)" class="sr-only">
                            <i class="fa-solid <?php echo e($icon); ?>" style="color: <?php echo e($color); ?>;"></i>
                            <span class="text-sm font-medium" style="color: #0F172A;"><?php echo e($label); ?></span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div id="bpjs-fields" class="<?php echo e(old('jenis_penjamin', 'umum') === 'bpjs' ? '' : 'hidden'); ?>">
                    <label class="block text-xs font-semibold mb-1.5" style="color: #374151;">No. BPJS <span style="color: #EF4444;">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2" style="color: #94a3b8;"><i class="fa-solid fa-id-badge text-xs"></i></span>
                        <input type="text" name="no_bpjs" value="<?php echo e(old('no_bpjs')); ?>" maxlength="20"
                               placeholder="Nomor kartu BPJS"
                               class="w-full border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:border-blue-400"
                               style="color: #0F172A;">
                    </div>
                </div>
                <div id="asuransi-fields" class="<?php echo e(old('jenis_penjamin') === 'asuransi' ? 'grid' : 'hidden'); ?> grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: #374151;">No. Polis <span style="color: #EF4444;">*</span></label>
                        <input type="text" name="no_polis_asuransi" value="<?php echo e(old('no_polis_asuransi')); ?>"
                               placeholder="Nomor polis asuransi"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:border-blue-400"
                               style="color: #0F172A;">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: #374151;">Nama Asuransi <span style="color: #EF4444;">*</span></label>
                        <input type="text" name="nama_asuransi" value="<?php echo e(old('nama_asuransi')); ?>"
                               placeholder="Nama perusahaan asuransi"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:border-blue-400"
                               style="color: #0F172A;">
                    </div>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center gap-2" style="background: #F8FAFC;">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: #F0F9FF;">
                    <i class="fa-solid fa-calendar-check text-xs" style="color: #0EA5E9;"></i>
                </div>
                <h3 class="text-sm font-semibold" style="color: #0F172A;">Data Kunjungan</h3>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: #374151;">Tanggal Kunjungan <span style="color: #EF4444;">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2" style="color: #94a3b8;"><i class="fa-solid fa-calendar text-xs"></i></span>
                        <input type="date" name="tanggal_kunjungan" value="<?php echo e(old('tanggal_kunjungan', date('Y-m-d'))); ?>" required
                               class="w-full border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:border-blue-400"
                               style="color: #0F172A;">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: #374151;">Poli <span style="color: #EF4444;">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2" style="color: #94a3b8;"><i class="fa-solid fa-hospital text-xs"></i></span>
                        <select name="poli_id" required
                                class="w-full border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:border-blue-400 appearance-none"
                                style="color: #0F172A;">
                            <option value="">-- Pilih Poli --</option>
                            <?php $__currentLoopData = $polis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $poli): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($poli->id); ?>" <?php echo e(old('poli_id') == $poli->id ? 'selected' : ''); ?>><?php echo e($poli->nama_poli); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: #374151;">Dokter <span class="font-normal" style="color: #94a3b8;">(opsional)</span></label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2" style="color: #94a3b8;"><i class="fa-solid fa-user-doctor text-xs"></i></span>
                        <select name="doctor_id"
                                class="w-full border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:border-blue-400 appearance-none"
                                style="color: #0F172A;">
                            <option value="">-- Pilih Dokter --</option>
                            <?php $__currentLoopData = $doctors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doctor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($doctor->id); ?>" <?php echo e(old('doctor_id') == $doctor->id ? 'selected' : ''); ?>><?php echo e($doctor->nama_dokter); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="flex gap-3">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:scale-[1.02]"
                    style="background: linear-gradient(135deg, #7B1D1D, #5C1414);">
                <i class="fa-solid fa-user-check"></i>
                Daftarkan Pasien
            </button>
            <a href="<?php echo e(route('registration.index')); ?>"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium border border-slate-200 hover:bg-slate-50 transition-colors"
               style="color: #64748B;">
                <i class="fa-solid fa-xmark"></i>
                Batal
            </a>
        </div>
    </form>
</div>

<script>
function togglePenjamin(v) {
    document.getElementById('bpjs-fields').classList.toggle('hidden', v !== 'bpjs');
    const asuransiEl = document.getElementById('asuransi-fields');
    if (v === 'asuransi') {
        asuransiEl.classList.remove('hidden');
        asuransiEl.classList.add('grid');
    } else {
        asuransiEl.classList.add('hidden');
        asuransiEl.classList.remove('grid');
    }
    // Update radio label styles
    document.querySelectorAll('input[name="jenis_penjamin"]').forEach(r => {
        const label = r.closest('label');
        if (r.value === v) {
            label.classList.add('border-blue-500', 'bg-blue-50');
            label.classList.remove('border-slate-200');
        } else {
            label.classList.remove('border-blue-500', 'bg-blue-50');
            label.classList.add('border-slate-200');
        }
    });
}
document.addEventListener('DOMContentLoaded', () => {
    const checked = document.querySelector('input[name="jenis_penjamin"]:checked');
    if (checked) togglePenjamin(checked.value);
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/registration/create.blade.php ENDPATH**/ ?>