<?php $__env->startSection('title', 'Manajemen Antrian'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <span class="font-medium" style="color: #1A0A0A;">Antrian</span>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('head'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<style>
    @keyframes pulse-ring {
        0%   { box-shadow: 0 0 0 0 rgba(123,29,29,0.4); }
        70%  { box-shadow: 0 0 0 8px rgba(123,29,29,0); }
        100% { box-shadow: 0 0 0 0 rgba(123,29,29,0); }
    }
    .pulse-called { animation: pulse-ring 1.5s infinite; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="fade-in">

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold" style="color: #1A0A0A;">Manajemen Antrian</h2>
        <p class="text-sm mt-0.5" style="color: #6B4C4C;">Kelola antrian pasien per poli</p>
    </div>
</div>


<div class="bg-white rounded-xl p-4 mb-5" style="border:1px solid #E8D5D5; box-shadow:0 1px 3px rgba(123,29,29,0.06);">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-semibold mb-1.5" style="color: #6B4C4C;">Pilih Poli</label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2" style="color:#9B7B7B;"><i class="fa-solid fa-hospital text-xs"></i></span>
                <select name="poli_id"
                        class="w-full border rounded-xl pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 appearance-none"
                        style="border-color:#E8D5D5; color:#1A0A0A; --tw-ring-color:rgba(123,29,29,0.15);">
                    <?php $__currentLoopData = $polis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($p->id); ?>" <?php echo e($p->id == $poliId ? 'selected' : ''); ?>><?php echo e($p->nama_poli); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
        <div class="min-w-40">
            <label class="block text-xs font-semibold mb-1.5" style="color: #6B4C4C;">Tanggal</label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2" style="color:#9B7B7B;"><i class="fa-solid fa-calendar text-xs"></i></span>
                <input type="date" name="tanggal" value="<?php echo e(request('tanggal', date('Y-m-d'))); ?>"
                       class="w-full border rounded-xl pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2"
                       style="border-color:#E8D5D5; color:#1A0A0A; --tw-ring-color:rgba(123,29,29,0.15);">
            </div>
        </div>
        <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white"
                style="background:#7B1D1D;">
            <i class="fa-solid fa-filter"></i> Tampilkan
        </button>
        <?php if($poli): ?>
            <a href="<?php echo e(route('queue.display', $poliId)); ?>" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium border transition-colors"
               style="color:#6B4C4C; border-color:#E8D5D5;"
               onmouseover="this.style.background='#FFF0F0'" onmouseout="this.style.background='transparent'">
                <i class="fa-solid fa-display"></i> Display Publik
            </a>
        <?php endif; ?>
    </form>
</div>

<?php if($poli): ?>


<?php
    $statCounts = [
        'menunggu'          => $queue->where('status','menunggu')->count(),
        'dipanggil'         => $queue->where('status','dipanggil')->count(),
        'dalam_pemeriksaan' => $queue->where('status','dalam_pemeriksaan')->count(),
        'selesai'           => $queue->where('status','selesai')->count(),
    ];
?>
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
    <?php $__currentLoopData = [
        ['Menunggu',          'menunggu',          'fa-hourglass-half', '#92400E','#FFFBEB','#FDE68A'],
        ['Dipanggil',         'dipanggil',         'fa-bullhorn',       '#7B1D1D','#FFF0F0','#F5C6C6'],
        ['Dalam Pemeriksaan', 'dalam_pemeriksaan', 'fa-stethoscope',    '#6D28D9','#F5F3FF','#DDD6FE'],
        ['Selesai',           'selesai',           'fa-circle-check',   '#166534','#F0FFF4','#BBF7D0'],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $key, $icon, $color, $bg, $ring]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div style="background:#FFFFFF; border-radius:1rem; padding:1rem 1.25rem; display:flex; align-items:center; justify-content:space-between; border:1px solid #F0E8E8; box-shadow:0 2px 8px rgba(123,29,29,0.06);">
        <div>
            <p style="font-size:0.7rem; font-weight:600; text-transform:uppercase; color:#9B7B7B; margin:0 0 0.3rem;"><?php echo e($label); ?></p>
            <p style="font-size:1.75rem; font-weight:800; color:<?php echo e($color); ?>; margin:0; line-height:1;"><?php echo e($statCounts[$key]); ?></p>
        </div>
        <div style="width:2.75rem; height:2.75rem; border-radius:50%; background:<?php echo e($bg); ?>; border:2px solid <?php echo e($ring); ?>; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <i class="fa-solid <?php echo e($icon); ?>" style="font-size:1rem; color:<?php echo e($color); ?>;"></i>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div class="bg-white rounded-xl overflow-hidden" style="border:1px solid #E8D5D5; box-shadow:0 2px 8px rgba(123,29,29,0.06);">
    <div class="px-5 py-3.5 border-b flex items-center justify-between" style="background:#FDF8F8; border-color:#E8D5D5;">
        <span class="text-sm font-semibold flex items-center gap-2" style="color:#1A0A0A;">
            <i class="fa-solid fa-list-ol" style="color:#7B1D1D;"></i>
            Antrian Poli: <span style="color:#7B1D1D;"><?php echo e($poli->nama_poli); ?></span>
        </span>
        <span class="text-xs" style="color:#9B7B7B;" id="last-updated">
            <i class="fa-regular fa-clock mr-1"></i>Diperbarui: baru saja
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead style="background:#FDF8F8;">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide w-24" style="color:#6B4C4C;">No.</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:#6B4C4C;">Nama Pasien</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:#6B4C4C;">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:#6B4C4C;">Aksi</th>
                </tr>
            </thead>
            <tbody id="queue-tbody" class="divide-y" style="border-color:#F0E8E8;">
                <?php $__empty_1 = true; $__currentLoopData = $queue; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr id="row-<?php echo e($entry->id); ?>" style="transition:background 0.15s;" onmouseover="this.style.background='#FFF8F8'" onmouseout="this.style.background=''">
                    <td class="px-5 py-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center font-bold text-lg <?php echo e($entry->status === 'dipanggil' ? 'pulse-called' : ''); ?>"
                             style="background:<?php echo e($entry->status === 'dipanggil' ? '#FFF0F0' : '#F9F5F5'); ?>; color:#7B1D1D; border:2px solid <?php echo e($entry->status === 'dipanggil' ? '#F5C6C6' : '#F0E8E8'); ?>;">
                            <?php echo e($entry->queue_number); ?>

                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                 style="background:linear-gradient(135deg,#7B1D1D,#9B2C2C);">
                                <?php echo e(strtoupper(substr($entry->visit?->patient?->nama_lengkap ?? 'P', 0, 1))); ?>

                            </div>
                            <span class="font-medium" style="color:#1A0A0A;"><?php echo e($entry->visit?->patient?->nama_lengkap ?? '-'); ?></span>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <?php
                            $statusStyle = match($entry->status) {
                                'menunggu'          => 'background:#FFFBEB;color:#92400E',
                                'dipanggil'         => 'background:#FFF0F0;color:#7B1D1D',
                                'dalam_pemeriksaan' => 'background:#F5F3FF;color:#6D28D9',
                                'selesai'           => 'background:#F0FFF4;color:#065F46',
                                default             => 'background:#F9F5F5;color:#6B4C4C',
                            };
                            $statusLabel = match($entry->status) {
                                'menunggu'          => 'Menunggu',
                                'dipanggil'         => 'Dipanggil',
                                'dalam_pemeriksaan' => 'Dalam Pemeriksaan',
                                'selesai'           => 'Selesai',
                                default             => $entry->status,
                            };
                        ?>
                        <span class="px-2.5 py-1.5 rounded-full text-xs font-semibold status-badge" style="<?php echo e($statusStyle); ?>">
                            <?php echo e($statusLabel); ?>

                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex gap-2 flex-wrap">
                            <?php if($entry->status === 'menunggu'): ?>
                                <button onclick="updateStatus(<?php echo e($entry->id); ?>, 'dipanggil')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-all hover:opacity-90"
                                        style="background:#7B1D1D;">
                                    <i class="fa-solid fa-bullhorn"></i> Panggil
                                </button>
                            <?php endif; ?>
                            <?php if($entry->status === 'dipanggil'): ?>
                                <button onclick="updateStatus(<?php echo e($entry->id); ?>, 'dalam_pemeriksaan')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-all hover:opacity-90"
                                        style="background:#6D28D9;">
                                    <i class="fa-solid fa-stethoscope"></i> Periksa
                                </button>
                            <?php endif; ?>
                            <?php if(in_array($entry->status, ['dipanggil','dalam_pemeriksaan'])): ?>
                                <button onclick="updateStatus(<?php echo e($entry->id); ?>, 'selesai')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-all hover:opacity-90"
                                        style="background:#166534;">
                                    <i class="fa-solid fa-circle-check"></i> Selesai
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="4" class="px-5 py-12 text-center">
                        <i class="fa-solid fa-list-ol text-3xl mb-3 block" style="color:#E8D5D5;"></i>
                        <p class="text-sm font-medium" style="color:#6B4C4C;">Belum ada antrian hari ini</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

</div>

<?php $__env->startPush('scripts'); ?>
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const statusMap = {
    menunggu:          { style: 'background:#FFFBEB;color:#92400E',  label: 'Menunggu' },
    dipanggil:         { style: 'background:#FFF0F0;color:#7B1D1D',  label: 'Dipanggil' },
    dalam_pemeriksaan: { style: 'background:#F5F3FF;color:#6D28D9',  label: 'Dalam Pemeriksaan' },
    selesai:           { style: 'background:#F0FFF4;color:#065F46',  label: 'Selesai' },
};

async function updateStatus(id, status) {
    const res  = await fetch(`/queue/${id}/status`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ status })
    });
    const data = await res.json();
    if (data.success) {
        const row = document.getElementById(`row-${data.queue_id}`);
        if (!row) return;
        const info = statusMap[data.status] ?? { style: 'background:#F9F5F5;color:#6B4C4C', label: data.status };
        const badge = row.querySelector('.status-badge');
        badge.style.cssText = info.style;
        badge.textContent = info.label;
        document.getElementById('last-updated').innerHTML = '<i class="fa-regular fa-clock mr-1"></i>Diperbarui: ' + new Date().toLocaleTimeString('id-ID');
        setTimeout(() => location.reload(), 800);
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/queue/index.blade.php ENDPATH**/ ?>