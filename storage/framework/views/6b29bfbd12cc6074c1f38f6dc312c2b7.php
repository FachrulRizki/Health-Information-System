<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Klaim BPJS — <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
<div class="max-w-7xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Monitoring Klaim BPJS</h1>
        <a href="<?php echo e(route('billing.index')); ?>" class="text-sm text-gray-500 hover:text-gray-700">← Billing</a>
    </div>

    <?php
        $submitted = $bills->where('bpjs_claim_status', 'submitted')->count();
        $pending   = $bills->where('bpjs_claim_status', 'pending')->count();
        $failed    = $bills->whereIn('bpjs_claim_status', ['failed', 'error'])->count();
    ?>
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded shadow p-4 text-center">
            <p class="text-2xl font-bold text-blue-600"><?php echo e($submitted); ?></p>
            <p class="text-sm text-gray-500 mt-1">Terkirim</p>
        </div>
        <div class="bg-white rounded shadow p-4 text-center">
            <p class="text-2xl font-bold text-yellow-600"><?php echo e($pending); ?></p>
            <p class="text-sm text-gray-500 mt-1">Pending</p>
        </div>
        <div class="bg-white rounded shadow p-4 text-center">
            <p class="text-2xl font-bold text-red-600"><?php echo e($failed); ?></p>
            <p class="text-sm text-gray-500 mt-1">Gagal</p>
        </div>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-600">No. Rawat</th>
                    <th class="px-4 py-3 text-left text-gray-600">Nama Pasien</th>
                    <th class="px-4 py-3 text-right text-gray-600">Total</th>
                    <th class="px-4 py-3 text-left text-gray-600">Status Klaim</th>
                    <th class="px-4 py-3 text-left text-gray-600">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php $__empty_1 = true; $__currentLoopData = $bills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-gray-700"><?php echo e($bill->visit?->no_rawat ?? '-'); ?></td>
                        <td class="px-4 py-3 font-medium"><?php echo e($bill->visit?->patient?->nama_lengkap ?? '-'); ?></td>
                        <td class="px-4 py-3 text-right font-mono">Rp <?php echo e(number_format($bill->total_amount, 0, ',', '.')); ?></td>
                        <td class="px-4 py-3">
                            <?php
                                $color = match($bill->bpjs_claim_status) {
                                    'submitted' => 'bg-blue-100 text-blue-700',
                                    'pending'   => 'bg-yellow-100 text-yellow-700',
                                    'failed', 'error' => 'bg-red-100 text-red-700',
                                    default     => 'bg-gray-100 text-gray-600',
                                };
                            ?>
                            <span class="px-2 py-1 rounded text-xs font-medium <?php echo e($color); ?>"><?php echo e(ucfirst($bill->bpjs_claim_status)); ?></span>
                        </td>
                        <td class="px-4 py-3 text-gray-500"><?php echo e($bill->updated_at?->format('d/m/Y H:i') ?? '-'); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="px-4 py-10 text-center text-gray-400">Belum ada klaim BPJS yang dikirimkan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
<?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/billing/claims.blade.php ENDPATH**/ ?>