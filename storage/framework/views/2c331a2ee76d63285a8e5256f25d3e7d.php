<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($schedule ? 'Edit' : 'Tambah'); ?> Jadwal Praktik — <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
<div class="max-w-2xl mx-auto p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6"><?php echo e($schedule ? 'Edit' : 'Tambah'); ?> Jadwal Praktik Dokter</h1>

    <?php if($errors->any()): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4 text-sm">
            <ul class="list-disc list-inside"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
        </div>
    <?php endif; ?>

    <form method="POST"
          action="<?php echo e($schedule ? route('master.schedules.update', $schedule->id) : route('master.schedules.store')); ?>"
          class="bg-white rounded shadow p-6 space-y-4">
        <?php echo csrf_field(); ?>
        <?php if($schedule): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Dokter <span class="text-red-500">*</span></label>
            <select name="doctor_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                <option value="">-- Pilih Dokter --</option>
                <?php $__currentLoopData = $doctors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doctor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($doctor->id); ?>" <?php echo e(old('doctor_id', $schedule?->doctor_id) == $doctor->id ? 'selected' : ''); ?>>
                        <?php echo e($doctor->nama_dokter); ?>

                        <?php if($doctor->specialization): ?> — <?php echo e($doctor->specialization->nama); ?> <?php endif; ?>
                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Poli <span class="text-red-500">*</span></label>
            <select name="poli_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                <option value="">-- Pilih Poli --</option>
                <?php $__currentLoopData = $polis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $poli): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($poli->id); ?>" <?php echo e(old('poli_id', $schedule?->poli_id) == $poli->id ? 'selected' : ''); ?>>
                        <?php echo e($poli->nama_poli); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Hari <span class="text-red-500">*</span></label>
            <select name="hari" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                <option value="">-- Pilih Hari --</option>
                <?php $__currentLoopData = ['senin','selasa','rabu','kamis','jumat','sabtu','minggu']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hari): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($hari); ?>" <?php echo e(old('hari', $schedule?->hari) === $hari ? 'selected' : ''); ?>>
                        <?php echo e(ucfirst($hari)); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai <span class="text-red-500">*</span></label>
                <input type="time" name="jam_mulai"
                       value="<?php echo e(old('jam_mulai', $schedule ? substr($schedule->jam_mulai, 0, 5) : '')); ?>"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai <span class="text-red-500">*</span></label>
                <input type="time" name="jam_selesai"
                       value="<?php echo e(old('jam_selesai', $schedule ? substr($schedule->jam_selesai, 0, 5) : '')); ?>"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kuota Pasien per Sesi <span class="text-red-500">*</span></label>
            <input type="number" name="kuota" min="1" max="999"
                   value="<?php echo e(old('kuota', $schedule?->kuota ?? 20)); ?>"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
        </div>

        <div class="flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" id="is_active"
                   <?php echo e(old('is_active', $schedule?->is_active ?? true) ? 'checked' : ''); ?>>
            <label for="is_active" class="text-sm text-gray-700">Jadwal Aktif</label>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 text-sm">Simpan</button>
            <a href="<?php echo e(route('master.schedules.index')); ?>" class="bg-gray-200 text-gray-700 px-6 py-2 rounded hover:bg-gray-300 text-sm">Batal</a>
        </div>
    </form>
</div>
</body>
</html>
<?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/master/schedules/form.blade.php ENDPATH**/ ?>