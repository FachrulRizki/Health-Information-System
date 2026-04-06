<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Failed Jobs — <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    
    <nav class="bg-white shadow-sm px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <a href="<?php echo e(route('dashboard')); ?>" class="text-lg font-semibold text-gray-800 hover:text-blue-600">
                <?php echo e(config('app.name')); ?>

            </a>
            <span class="text-gray-400">/</span>
            <span class="text-sm text-gray-600">Failed Jobs</span>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-600"><?php echo e(auth()->user()->username); ?></span>
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="text-sm text-red-500 hover:text-red-700">Keluar</button>
            </form>
        </div>
    </nav>

    <main class="p-6 max-w-7xl mx-auto">

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-700">Monitoring Failed Jobs</h2>
            <div class="flex gap-2">
                <?php if($failedJobs->isNotEmpty()): ?>
                    <form method="POST" action="<?php echo e(route('admin.failed-jobs.retry-all')); ?>"
                          onsubmit="return confirm('Retry semua failed jobs?')">
                        <?php echo csrf_field(); ?>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                            ↺ Retry Semua
                        </button>
                    </form>
                    <form method="POST" action="<?php echo e(route('admin.failed-jobs.clear')); ?>"
                          onsubmit="return confirm('Hapus semua failed jobs? Tindakan ini tidak dapat dibatalkan.')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">
                            🗑 Hapus Semua
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        
        <?php if($failedJobs->count() > $threshold): ?>
            <div class="mb-6 flex items-start gap-3 bg-red-50 border border-red-300 text-red-800 rounded-lg p-4">
                <span class="text-xl">⚠️</span>
                <div>
                    <p class="font-semibold">Peringatan: Jumlah failed jobs melebihi batas!</p>
                    <p class="text-sm mt-1">
                        Terdapat <strong><?php echo e($failedJobs->count()); ?></strong> failed jobs (batas: <?php echo e($threshold); ?>).
                        Segera periksa dan tangani job yang gagal.
                    </p>
                </div>
            </div>
        <?php endif; ?>

        
        <?php if(session('success')): ?>
            <div class="mb-4 bg-green-50 border border-green-300 text-green-800 rounded-lg p-3 text-sm">
                ✅ <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="mb-4 bg-red-50 border border-red-300 text-red-800 rounded-lg p-3 text-sm">
                ❌ <?php echo e($errors->first()); ?>

            </div>
        <?php endif; ?>

        <?php if($failedJobs->isEmpty()): ?>
            <div class="bg-white rounded-lg shadow-sm p-10 text-center text-gray-400">
                <p class="text-4xl mb-3">✅</p>
                <p class="text-lg font-medium">Tidak ada failed jobs</p>
                <p class="text-sm mt-1">Semua queue jobs berjalan normal.</p>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Job Class</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Queue</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Gagal Pada</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Error</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__currentLoopData = $failedJobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <span class="font-mono text-xs text-gray-800">
                                        <?php echo e(class_basename($job->job_class)); ?>

                                    </span>
                                    <p class="text-xs text-gray-400 mt-0.5 font-mono"><?php echo e($job->uuid); ?></p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-0.5 rounded">
                                        <?php echo e($job->queue); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600 whitespace-nowrap">
                                    <?php echo e(\Carbon\Carbon::parse($job->failed_at)->format('d/m/Y H:i:s')); ?>

                                </td>
                                <td class="px-4 py-3 max-w-xs">
                                    <p class="text-xs text-red-600 truncate" title="<?php echo e($job->exception); ?>">
                                        <?php echo e(Str::limit($job->exception, 120)); ?>

                                    </p>
                                    <button type="button"
                                            onclick="toggleException('exc-<?php echo e($job->uuid); ?>')"
                                            class="text-xs text-blue-500 hover:underline mt-1">
                                        Lihat detail
                                    </button>
                                    <pre id="exc-<?php echo e($job->uuid); ?>"
                                         class="hidden mt-2 text-xs bg-gray-900 text-green-300 p-3 rounded overflow-auto max-h-48 whitespace-pre-wrap"><?php echo e($job->exception); ?></pre>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <form method="POST"
                                          action="<?php echo e(route('admin.failed-jobs.retry', $job->uuid)); ?>"
                                          class="inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit"
                                                class="px-3 py-1 bg-blue-100 text-blue-700 text-xs rounded hover:bg-blue-200 transition-colors">
                                            ↺ Retry
                                        </button>
                                    </form>
                                    <form method="POST"
                                          action="<?php echo e(route('admin.failed-jobs.destroy', $job->uuid)); ?>"
                                          class="inline ml-1"
                                          onsubmit="return confirm('Hapus job ini?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit"
                                                class="px-3 py-1 bg-red-100 text-red-700 text-xs rounded hover:bg-red-200 transition-colors">
                                            🗑 Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <p class="text-xs text-gray-400 mt-3">
                Total: <?php echo e($failedJobs->count()); ?> failed job(s)
            </p>
        <?php endif; ?>
    </main>

    <script>
        function toggleException(id) {
            const el = document.getElementById(id);
            el.classList.toggle('hidden');
        }
    </script>

</body>
</html>
<?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/admin/failed-jobs.blade.php ENDPATH**/ ?>