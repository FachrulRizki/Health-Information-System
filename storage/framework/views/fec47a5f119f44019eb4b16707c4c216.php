<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kunjungan — <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
<div class="max-w-3xl mx-auto p-6">
    <a href="<?php echo e(route('registration.index')); ?>" class="text-sm text-gray-500 hover:text-gray-700">← Kembali</a>
    <h1 class="text-2xl font-bold text-gray-800 mt-2 mb-6">Daftar Kunjungan Pasien Lama</h1>

    <div class="bg-blue-50 border border-blue-200 rounded p-4 mb-6 text-sm text-blue-700">
        <p><span class="font-medium">NoRM:</span> <?php echo e($patient->no_rm); ?> &nbsp;|&nbsp;
           <span class="font-medium">Nama:</span> <?php echo e($patient->nama_lengkap); ?></p>
    </div>

    <?php if($errors->any()): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4 text-sm">
            <ul class="list-disc list-inside"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
        </div>
    <?php endif; ?>

    <?php if(session('bpjs_inactive') || $errors->has('bpjs')): ?>
        <div class="bg-yellow-50 border border-yellow-400 text-yellow-800 px-4 py-3 rounded mb-4 text-sm">
            <p class="font-semibold mb-1">⚠ Konfirmasi Status BPJS</p>
            <p><?php echo e($errors->first('bpjs')); ?></p>
            <label class="flex items-center gap-2 mt-3 cursor-pointer">
                <input type="checkbox" name="bpjs_confirmed" value="1"
                    <?php echo e(old('bpjs_confirmed') === '1' ? 'checked' : ''); ?>

                    class="w-4 h-4">
                <span>Saya memahami status peserta tidak aktif dan tetap melanjutkan pendaftaran</span>
            </label>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('registration.store-visit', $patient->id)); ?>" class="space-y-6">
        <?php echo csrf_field(); ?>

        <div class="bg-white rounded shadow p-6">
            <h3 class="text-base font-semibold text-gray-700 mb-4 pb-2 border-b">Jenis Penjamin</h3>
            <div class="flex gap-4 mb-4">
                <?php $__currentLoopData = ['umum' => 'Umum', 'bpjs' => 'BPJS', 'asuransi' => 'Asuransi']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="jenis_penjamin" value="<?php echo e($val); ?>"
                            <?php echo e(old('jenis_penjamin', $patient->jenis_penjamin) === $val ? 'checked' : ''); ?>

                            onchange="toggleBpjs(this.value)">
                        <span class="text-sm"><?php echo e($label); ?></span>
                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div id="bpjs-fields" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">No. BPJS *</label>
                <input type="text" name="no_bpjs" value="<?php echo e(old('no_bpjs', $patient->no_bpjs)); ?>"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            </div>
        </div>

        <div class="bg-white rounded shadow p-6">
            <h3 class="text-base font-semibold text-gray-700 mb-4 pb-2 border-b">Data Kunjungan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kunjungan *</label>
                    <input type="date" name="tanggal_kunjungan" value="<?php echo e(old('tanggal_kunjungan', date('Y-m-d'))); ?>" required
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Poli *</label>
                    <select name="poli_id" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="">-- Pilih Poli --</option>
                        <?php $__currentLoopData = $polis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $poli): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($poli->id); ?>" <?php echo e(old('poli_id') == $poli->id ? 'selected' : ''); ?>><?php echo e($poli->nama_poli); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dokter</label>
                    <select name="doctor_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="">-- Pilih Dokter (opsional) --</option>
                        <?php $__currentLoopData = $doctors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doctor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($doctor->id); ?>" <?php echo e(old('doctor_id') == $doctor->id ? 'selected' : ''); ?>><?php echo e($doctor->nama_dokter); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 text-sm">Daftarkan Kunjungan</button>
            <a href="<?php echo e(route('registration.index')); ?>" class="bg-gray-200 text-gray-700 px-6 py-2 rounded text-sm">Batal</a>
        </div>
    </form>
</div>
<script>
function toggleBpjs(v) {
    document.getElementById('bpjs-fields').classList.toggle('hidden', v !== 'bpjs');
}
document.addEventListener('DOMContentLoaded', () => {
    const checked = document.querySelector('input[name="jenis_penjamin"]:checked');
    if (checked) toggleBpjs(checked.value);
});
</script>
</body>
</html>
<?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/registration/create-visit.blade.php ENDPATH**/ ?>