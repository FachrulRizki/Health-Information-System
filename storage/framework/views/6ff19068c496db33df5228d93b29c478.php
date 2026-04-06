<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Praktik Dokter — <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
<div class="max-w-7xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Jadwal Praktik Dokter</h1>
        <a href="<?php echo e(route('master.schedules.create')); ?>" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">+ Tambah Jadwal</a>
    </div>

    <?php if(session('success')): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4 text-sm"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <form method="GET" class="mb-4 flex flex-wrap gap-2">
        <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Cari dokter / poli..." class="border border-gray-300 rounded px-3 py-2 text-sm flex-1 min-w-[180px]">
        <select name="doctor_id" class="border border-gray-300 rounded px-3 py-2 text-sm">
            <option value="">Semua Dokter</option>
            <?php $__currentLoopData = $doctors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doctor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($doctor->id); ?>" <?php echo e(request('doctor_id') == $doctor->id ? 'selected' : ''); ?>><?php echo e($doctor->nama_dokter); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <select name="poli_id" class="border border-gray-300 rounded px-3 py-2 text-sm">
            <option value="">Semua Poli</option>
            <?php $__currentLoopData = $polis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $poli): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($poli->id); ?>" <?php echo e(request('poli_id') == $poli->id ? 'selected' : ''); ?>><?php echo e($poli->nama_poli); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded text-sm">Filter</button>
        <a href="<?php echo e(route('master.schedules.index')); ?>" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm">Reset</a>
    </form>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-600">#</th>
                    <th class="px-4 py-3 text-left text-gray-600">Dokter</th>
                    <th class="px-4 py-3 text-left text-gray-600">Poli</th>
                    <th class="px-4 py-3 text-left text-gray-600">Hari</th>
                    <th class="px-4 py-3 text-left text-gray-600">Jam Mulai</th>
                    <th class="px-4 py-3 text-left text-gray-600">Jam Selesai</th>
                    <th class="px-4 py-3 text-left text-gray-600">Kuota</th>
                    <th class="px-4 py-3 text-left text-gray-600">Status</th>
                    <th class="px-4 py-3 text-left text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php $__empty_1 = true; $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-500"><?php echo e($schedule->id); ?></td>
                        <td class="px-4 py-3 font-medium"><?php echo e($schedule->doctor->nama_dokter); ?></td>
                        <td class="px-4 py-3"><?php echo e($schedule->poli->nama_poli); ?></td>
                        <td class="px-4 py-3 capitalize"><?php echo e($schedule->hari); ?></td>
                        <td class="px-4 py-3"><?php echo e(substr($schedule->jam_mulai, 0, 5)); ?></td>
                        <td class="px-4 py-3"><?php echo e(substr($schedule->jam_selesai, 0, 5)); ?></td>
                        <td class="px-4 py-3"><?php echo e($schedule->kuota); ?></td>
                        <td class="px-4 py-3">
                            <?php if($schedule->is_active): ?>
                                <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs">Aktif</span>
                            <?php else: ?>
                                <span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded text-xs">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="<?php echo e(route('master.schedules.edit', $schedule->id)); ?>" class="text-blue-600 hover:underline">Edit</a>
                            <form method="POST" action="<?php echo e(route('master.schedules.destroy', $schedule->id)); ?>" onsubmit="return confirm('Hapus jadwal ini?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="9" class="px-4 py-6 text-center text-gray-400">Tidak ada jadwal praktik.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4"><?php echo e($schedules->links()); ?></div>
</div>
</body>
</html>
<?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/master/schedules/index.blade.php ENDPATH**/ ?>