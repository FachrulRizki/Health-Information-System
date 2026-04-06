


<div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4">
        <p class="text-xs text-gray-500 uppercase tracking-wide">Kunjungan Hari Ini</p>
        <p class="text-3xl font-bold text-blue-600 mt-1"><?php echo e($visitStats['today']); ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4">
        <p class="text-xs text-gray-500 uppercase tracking-wide">Kunjungan Bulan Ini</p>
        <p class="text-3xl font-bold text-indigo-600 mt-1"><?php echo e($visitStats['this_month']); ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4">
        <p class="text-xs text-gray-500 uppercase tracking-wide">Pendapatan Bulan Ini</p>
        <p class="text-2xl font-bold text-green-600 mt-1">
            Rp <?php echo e(number_format($financialStats['paid_this_month'], 0, ',', '.')); ?>

        </p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
    <div class="bg-white rounded-lg shadow-sm p-5">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-gray-700">Kunjungan Bulan Ini per Penjamin</h3>
            <a href="<?php echo e(route('report.visits')); ?>" class="text-xs text-blue-600 hover:underline">Laporan lengkap →</a>
        </div>
        <?php $__empty_1 = true; $__currentLoopData = $visitStats['by_penjamin']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $penjamin => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="flex justify-between items-center py-2 border-b border-gray-50 last:border-0">
                <span class="text-sm text-gray-700 capitalize"><?php echo e($penjamin); ?></span>
                <div class="flex items-center gap-3">
                    <div class="w-24 bg-gray-100 rounded-full h-2">
                        <?php $pct = $visitStats['this_month'] > 0 ? ($count / $visitStats['this_month'] * 100) : 0; ?>
                        <div class="bg-blue-500 h-2 rounded-full" style="width: <?php echo e($pct); ?>%"></div>
                    </div>
                    <span class="text-sm font-semibold text-gray-800 w-8 text-right"><?php echo e($count); ?></span>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-sm text-gray-400 text-center py-4">Belum ada data kunjungan bulan ini</p>
        <?php endif; ?>
    </div>

    
    <div class="space-y-4">
        <div class="bg-white rounded-lg shadow-sm p-5">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-gray-700">Ringkasan Keuangan</h3>
                <a href="<?php echo e(route('report.financial')); ?>" class="text-xs text-blue-600 hover:underline">Laporan →</a>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-600">Pendapatan Terbayar (Bulan Ini)</span>
                    <span class="text-sm font-semibold text-green-600">
                        Rp <?php echo e(number_format($financialStats['paid_this_month'], 0, ',', '.')); ?>

                    </span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-600">Tagihan Belum Dibayar</span>
                    <span class="text-sm font-semibold text-yellow-600">
                        Rp <?php echo e(number_format($financialStats['pending_amount'], 0, ',', '.')); ?>

                    </span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-sm text-gray-600">Klaim BPJS Terkirim</span>
                    <span class="text-sm font-semibold text-blue-600">
                        Rp <?php echo e(number_format($financialStats['bpjs_submitted'], 0, ',', '.')); ?>

                    </span>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-lg shadow-sm p-5">
            <h3 class="font-semibold text-gray-700 mb-3">Laporan</h3>
            <div class="grid grid-cols-2 gap-2">
                <a href="<?php echo e(route('report.visits')); ?>"
                   class="p-3 bg-gray-50 hover:bg-blue-50 rounded-lg text-sm text-gray-700 hover:text-blue-700 text-center transition-colors">
                    📊 Kunjungan
                </a>
                <a href="<?php echo e(route('report.diseases')); ?>"
                   class="p-3 bg-gray-50 hover:bg-blue-50 rounded-lg text-sm text-gray-700 hover:text-blue-700 text-center transition-colors">
                    🦠 Penyakit
                </a>
                <a href="<?php echo e(route('report.financial')); ?>"
                   class="p-3 bg-gray-50 hover:bg-blue-50 rounded-lg text-sm text-gray-700 hover:text-blue-700 text-center transition-colors">
                    💰 Keuangan
                </a>
                <a href="<?php echo e(route('billing.claims')); ?>"
                   class="p-3 bg-gray-50 hover:bg-blue-50 rounded-lg text-sm text-gray-700 hover:text-blue-700 text-center transition-colors">
                    🏥 Klaim BPJS
                </a>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/dashboard/partials/manajemen.blade.php ENDPATH**/ ?>