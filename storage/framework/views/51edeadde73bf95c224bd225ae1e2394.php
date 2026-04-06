<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setting API — <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
<div class="max-w-6xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Setting API</h1>
        <a href="<?php echo e(route('dashboard')); ?>" class="text-sm text-gray-500 hover:text-gray-700">← Dashboard</a>
    </div>

    <?php if(session('success')): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4 text-sm"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="grid gap-4">
        <?php $__empty_1 = true; $__currentLoopData = $settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-white rounded shadow p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-1">
                        <span class="font-semibold text-gray-800"><?php echo e(strtoupper(str_replace('_', ' ', $setting->integration_name))); ?></span>
                        <?php if($setting->isTestingMode()): ?>
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">⚠ Testing</span>
                        <?php else: ?>
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">✓ Production</span>
                        <?php endif; ?>
                        <?php if($setting->is_active): ?>
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">Aktif</span>
                        <?php else: ?>
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">Nonaktif</span>
                        <?php endif; ?>
                    </div>
                    <p class="text-sm text-gray-500 truncate">Endpoint: <?php echo e($setting->endpoint_url); ?></p>
                </div>
                <div class="flex flex-wrap gap-2 shrink-0">
                    <button type="button" onclick="testConnection('<?php echo e($setting->integration_name); ?>')"
                        class="text-sm px-3 py-1.5 rounded border border-gray-300 text-gray-600 hover:bg-gray-50">Test Koneksi</button>
                    <form method="POST" action="<?php echo e(route('master.api-settings.toggle', $setting->integration_name)); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="text-sm px-3 py-1.5 rounded border <?php echo e($setting->is_active ? 'border-orange-300 text-orange-600' : 'border-green-300 text-green-600'); ?>">
                            <?php echo e($setting->is_active ? 'Nonaktifkan' : 'Aktifkan'); ?>

                        </button>
                    </form>
                    <a href="<?php echo e(route('master.api-settings.edit', $setting->integration_name)); ?>"
                        class="text-sm px-3 py-1.5 rounded bg-blue-600 text-white hover:bg-blue-700">Edit</a>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="bg-white rounded shadow p-8 text-center text-gray-400">Belum ada konfigurasi API.</div>
        <?php endif; ?>
    </div>
</div>

<div id="testModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded shadow-lg p-6 max-w-md w-full mx-4">
        <h2 class="font-semibold text-gray-800 mb-3">Hasil Uji Koneksi</h2>
        <div id="testResult" class="text-sm text-gray-600 mb-4"></div>
        <button onclick="document.getElementById('testModal').classList.add('hidden')"
            class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm">Tutup</button>
    </div>
</div>

<script>
function testConnection(name) {
    document.getElementById('testModal').classList.remove('hidden');
    document.getElementById('testResult').innerHTML = '<span class="text-gray-400">Menguji...</span>';
    fetch(`/master/api-settings/${name}/test-connection`, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json'},
    }).then(r => r.json()).then(d => {
        document.getElementById('testResult').innerHTML = d.success
            ? `<span class="text-green-600 font-medium">✓ Berhasil</span><br>${d.message ?? ''}`
            : `<span class="text-red-600 font-medium">✗ Gagal</span><br>${d.message ?? ''}`;
    }).catch(() => {
        document.getElementById('testResult').innerHTML = '<span class="text-red-600">Gagal menghubungi server.</span>';
    });
}
</script>
</body>
</html>
<?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/settings/api-settings/index.blade.php ENDPATH**/ ?>