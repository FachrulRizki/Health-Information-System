


<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.5rem;">
    <?php
        $statCards = [
            [
                'label' => 'Total Pasien Hari Ini',
                'value' => $totalToday,
                'icon'  => 'fa-hospital-user',
                'color' => '#7B1D1D',
                'bg'    => '#FFF0F0',
                'ring'  => '#F5C6C6',
            ],
            [
                'label' => 'Total Selesai',
                'value' => $totalSelesai,
                'icon'  => 'fa-circle-check',
                'color' => '#166534',
                'bg'    => '#F0FFF4',
                'ring'  => '#BBF7D0',
            ],
            [
                'label' => 'Belum Selesai',
                'value' => $totalBelumSelesai,
                'icon'  => 'fa-hourglass-half',
                'color' => '#92400E',
                'bg'    => '#FFFBEB',
                'ring'  => '#FDE68A',
            ],
            [
                'label' => 'Failed Jobs',
                'value' => $failed_jobs,
                'icon'  => 'fa-triangle-exclamation',
                'color' => $failed_jobs > 0 ? '#991B1B' : '#6B4C4C',
                'bg'    => $failed_jobs > 0 ? '#FEF2F2' : '#F9F5F5',
                'ring'  => $failed_jobs > 0 ? '#FECACA' : '#E8D5D5',
            ],
        ];
    ?>
    <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div style="background:#FFFFFF; border-radius:1rem; padding:1.25rem; display:flex; align-items:center; justify-content:space-between; border:1px solid #F0E8E8; box-shadow:0 2px 12px rgba(123,29,29,0.08);">
        <div>
            <p style="font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; color:#9B7B7B; margin:0 0 0.4rem;"><?php echo e($card['label']); ?></p>
            <p style="font-size:2rem; font-weight:800; color:<?php echo e($card['color']); ?>; margin:0; line-height:1;"
               data-counter="<?php echo e($card['value']); ?>"><?php echo e($card['value']); ?></p>
        </div>
        <div style="width:3.5rem; height:3.5rem; border-radius:50%; background:<?php echo e($card['bg']); ?>; border:2px solid <?php echo e($card['ring']); ?>; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <i class="fa-solid <?php echo e($card['icon']); ?>" style="font-size:1.25rem; color:<?php echo e($card['color']); ?>;"></i>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:1.5rem;">

    
    <div style="background:#FFFFFF; border-radius:1rem; overflow:hidden; border:1px solid #F0E8E8; box-shadow:0 2px 12px rgba(123,29,29,0.08);">
        <div style="padding:1rem 1.25rem; border-bottom:1px solid #F0E8E8; background:#FDF8F8; display:flex; align-items:center; gap:0.5rem;">
            <i class="fa-solid fa-virus" style="color:#7B1D1D; font-size:0.85rem;"></i>
            <h3 style="margin:0; font-size:0.875rem; font-weight:700; color:#3D1515;">10 Penyakit Terbanyak Hari Ini</h3>
        </div>
        <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
            <?php $__empty_1 = true; $__currentLoopData = $top10Penyakit; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $maxVal = $top10Penyakit->first()->total ?? 1;
                $pct    = $maxVal > 0 ? round(($d->total / $maxVal) * 100) : 0;
            ?>
            <div class="disease-bar-row" style="animation: slideInLeft 0.4s ease both; animation-delay: <?php echo e($idx * 0.06); ?>s;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.25rem;">
                    <div style="display:flex; align-items:center; gap:0.5rem; min-width:0; flex:1;">
                        <span style="font-size:0.7rem; font-weight:700; color:#9B7B7B; width:1.2rem; text-align:center; flex-shrink:0;"><?php echo e($idx + 1); ?></span>
                        <span style="font-size:0.7rem; font-family:monospace; background:#FFF0F0; color:#7B1D1D; padding:0.15rem 0.4rem; border-radius:0.3rem; flex-shrink:0; font-weight:600;"><?php echo e($d->icd10_code); ?></span>
                        <span style="font-size:0.75rem; color:#3D1515; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?php echo e(Str::limit($d->icd10Code?->deskripsi ?? '-', 30)); ?></span>
                    </div>
                    <span style="font-size:0.75rem; font-weight:800; color:#7B1D1D; flex-shrink:0; margin-left:0.5rem;"><?php echo e($d->total); ?></span>
                </div>
                <div style="height:6px; background:#F5ECEC; border-radius:999px; overflow:hidden;">
                    <div class="bar-fill" style="height:100%; width:<?php echo e($pct); ?>%; background:linear-gradient(90deg,#7B1D1D,#C53030); border-radius:999px; transform:scaleX(0); transform-origin:left; animation:barGrow 0.6s ease <?php echo e($idx * 0.06 + 0.2); ?>s both;"></div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p style="text-align:center; color:#9B7B7B; font-size:0.875rem; padding:1.5rem 0;">Belum ada data diagnosa hari ini</p>
            <?php endif; ?>
        </div>
    </div>

    
    <div style="background:#FFFFFF; border-radius:1rem; overflow:hidden; border:1px solid #F0E8E8; box-shadow:0 2px 12px rgba(123,29,29,0.08);">
        <div style="padding:1rem 1.25rem; border-bottom:1px solid #F0E8E8; background:#FDF8F8; display:flex; align-items:center; gap:0.5rem;">
            <i class="fa-solid fa-hospital" style="color:#7B1D1D; font-size:0.85rem;"></i>
            <h3 style="margin:0; font-size:0.875rem; font-weight:700; color:#3D1515;">Pasien per Poliklinik</h3>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:0.875rem;">
                <thead>
                    <tr style="background:#FDF8F8;">
                        <th style="padding:0.75rem 1.25rem; text-align:left; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#9B7B7B;">Poliklinik</th>
                        <th style="padding:0.75rem 1.25rem; text-align:center; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#9B7B7B;">Pasien</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $poliStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $poli): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr style="border-top:1px solid #F5ECEC; transition:background 0.15s;" onmouseover="this.style.background='#FFF8F8'" onmouseout="this.style.background=''">
                        <td style="padding:0.75rem 1.25rem; color:#3D1515; font-weight:500;"><?php echo e($poli->nama_poli); ?></td>
                        <td style="padding:0.75rem 1.25rem; text-align:center;">
                            <span style="display:inline-block; padding:0.2rem 0.75rem; border-radius:999px; font-size:0.75rem; font-weight:700; background:<?php echo e($poli->visits_count > 0 ? '#FFF0F0' : '#F9F5F5'); ?>; color:<?php echo e($poli->visits_count > 0 ? '#7B1D1D' : '#9B7B7B'); ?>;">
                                <?php echo e($poli->visits_count); ?>

                            </span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="2" style="padding:2rem; text-align:center; color:#9B7B7B;">Tidak ada poli aktif</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1.5rem; margin-bottom:1.5rem;">
    <?php
        $rings = [
            ['label' => 'RME CPPT', 'sub' => 'Kelengkapan rekam medis', 'pct' => $rmePercent, 'color' => '#7B1D1D', 'track' => '#F5ECEC', 'icon' => 'fa-file-medical'],
            ['label' => 'Resep',    'sub' => 'Resep telah diserahkan',   'pct' => $resepPercent, 'color' => '#166534', 'track' => '#DCFCE7', 'icon' => 'fa-pills'],
            ['label' => 'Resume',   'sub' => 'Resume medis terisi',      'pct' => $resumePercent, 'color' => '#1E40AF', 'track' => '#DBEAFE', 'icon' => 'fa-clipboard-check'],
        ];
    ?>
    <?php $__currentLoopData = $rings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ring): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div style="background:#FFFFFF; border-radius:1rem; padding:1.5rem; display:flex; flex-direction:column; align-items:center; border:1px solid #F0E8E8; box-shadow:0 2px 12px rgba(123,29,29,0.08);">
        <div style="position:relative; width:7rem; height:7rem; margin-bottom:1rem;">
            <svg viewBox="0 0 36 36" style="width:100%; height:100%; transform:rotate(-90deg);">
                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                    fill="none" stroke="<?php echo e($ring['track']); ?>" stroke-width="3.5"/>
                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                    fill="none" stroke="<?php echo e($ring['color']); ?>" stroke-width="3.5"
                    stroke-dasharray="<?php echo e($ring['pct']); ?>, 100"
                    stroke-linecap="round"
                    style="transition: stroke-dasharray 1.2s ease;"/>
            </svg>
            <div style="position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                <span style="font-size:1.4rem; font-weight:800; color:<?php echo e($ring['color']); ?>; line-height:1;"><?php echo e($ring['pct']); ?></span>
                <span style="font-size:0.65rem; color:#9B7B7B; font-weight:600;">%</span>
            </div>
        </div>
        <div style="text-align:center;">
            <div style="display:flex; align-items:center; justify-content:center; gap:0.4rem; margin-bottom:0.25rem;">
                <i class="fa-solid <?php echo e($ring['icon']); ?>" style="font-size:0.8rem; color:<?php echo e($ring['color']); ?>;"></i>
                <span style="font-size:0.9rem; font-weight:700; color:#3D1515;"><?php echo e($ring['label']); ?></span>
            </div>
            <p style="font-size:0.75rem; color:#9B7B7B; margin:0;"><?php echo e($ring['sub']); ?></p>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div style="background:#FFFFFF; border-radius:1rem; overflow:hidden; border:1px solid #F0E8E8; box-shadow:0 2px 12px rgba(123,29,29,0.08); margin-bottom:1.5rem;">
    <div style="padding:1rem 1.25rem; border-bottom:1px solid #F0E8E8; background:#FDF8F8; display:flex; align-items:center; gap:0.5rem;">
        <i class="fa-solid fa-triangle-exclamation" style="color:#C53030; font-size:0.85rem;"></i>
        <h3 style="margin:0; font-size:0.875rem; font-weight:700; color:#3D1515;">High Alert Obat</h3>
        <?php if(isset($highAlertDrugs) && $highAlertDrugs->count() > 0): ?>
        <span style="margin-left:auto; font-size:0.7rem; padding:0.15rem 0.6rem; border-radius:999px; font-weight:700; background:#FEE2E2; color:#991B1B;"><?php echo e($highAlertDrugs->count()); ?> item</span>
        <?php endif; ?>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.875rem;">
            <thead><tr style="background:#FDF8F8;">
                <th style="padding:0.75rem 1.25rem; text-align:left; font-size:0.7rem; font-weight:700; text-transform:uppercase; color:#9B7B7B;">Nama Obat</th>
                <th style="padding:0.75rem 1.25rem; text-align:center; font-size:0.7rem; font-weight:700; text-transform:uppercase; color:#9B7B7B;">Stok</th>
                <th style="padding:0.75rem 1.25rem; text-align:center; font-size:0.7rem; font-weight:700; text-transform:uppercase; color:#9B7B7B;">Kadaluarsa</th>
                <th style="padding:0.75rem 1.25rem; text-align:center; font-size:0.7rem; font-weight:700; text-transform:uppercase; color:#9B7B7B;">Status</th>
            </tr></thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = isset($highAlertDrugs) ? $highAlertDrugs : []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $drug): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $alertStyle = match($drug['type']) {
                        'expired'     => ['bg'=>'#FEE2E2','color'=>'#991B1B','label'=>'Kadaluarsa'],
                        'near_expiry' => ['bg'=>'#FFFBEB','color'=>'#92400E','label'=>'Hampir Kadaluarsa'],
                        default       => ['bg'=>'#FFF0F0','color'=>'#7B1D1D','label'=>'Stok Menipis'],
                    };
                ?>
                <tr style="border-top:1px solid #F5ECEC;" onmouseover="this.style.background='#FFF8F8'" onmouseout="this.style.background=''">
                    <td style="padding:0.75rem 1.25rem; color:#3D1515; font-weight:500;"><?php echo e($drug['nama']); ?></td>
                    <td style="padding:0.75rem 1.25rem; text-align:center; color:#3D1515;"><?php echo e($drug['qty']); ?> / <?php echo e($drug['min']); ?></td>
                    <td style="padding:0.75rem 1.25rem; text-align:center; color:#6B4C4C; font-size:0.8rem;"><?php echo e($drug['exp'] ?? '-'); ?></td>
                    <td style="padding:0.75rem 1.25rem; text-align:center;">
                        <span style="padding:0.2rem 0.75rem; border-radius:999px; font-size:0.7rem; font-weight:700; background:<?php echo e($alertStyle['bg']); ?>; color:<?php echo e($alertStyle['color']); ?>;"><?php echo e($alertStyle['label']); ?></span>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" style="padding:2rem; text-align:center; color:#9B7B7B; font-size:0.875rem;">
                    <i class="fa-solid fa-circle-check" style="color:#276749; font-size:1.5rem; display:block; margin-bottom:0.5rem;"></i>
                    Tidak ada high alert obat
                </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div style="background:#FFFFFF; border-radius:1rem; overflow:hidden; border:1px solid #F0E8E8; box-shadow:0 2px 12px rgba(123,29,29,0.08);">
    <div style="padding:1rem 1.25rem; border-bottom:1px solid #F0E8E8; background:#FDF8F8; display:flex; align-items:center; gap:0.5rem;">
        <i class="fa-solid fa-chart-pie" style="color:#7B1D1D; font-size:0.85rem;"></i>
        <h3 style="margin:0; font-size:0.875rem; font-weight:700; color:#3D1515;">Progress Pengisian per Poli</h3>
    </div>
    <div style="padding:1.25rem;">
        <?php $__empty_1 = true; $__currentLoopData = isset($poliProgress) ? $poliProgress : []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $poli): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div style="margin-bottom:1.25rem; padding-bottom:1.25rem; border-bottom:1px solid #F5ECEC;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
                <span style="font-size:0.875rem; font-weight:700; color:#3D1515;"><?php echo e($poli['nama']); ?></span>
                <span style="font-size:0.75rem; color:#9B7B7B;"><?php echo e($poli['total']); ?> pasien</span>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:0.75rem;">
                <?php $__currentLoopData = [
                    ['label'=>'SOAP/CPPT','pct'=>$poli['soap_pct'],'done'=>$poli['soap_done'],'color'=>'#7B1D1D','track'=>'#F5ECEC'],
                    ['label'=>'Resep','pct'=>$poli['resep_pct'],'done'=>$poli['resep_done'],'color'=>'#276749','track'=>'#DCFCE7'],
                    ['label'=>'Resume','pct'=>$poli['resume_pct'],'done'=>$poli['resume_done'],'color'=>'#1E40AF','track'=>'#DBEAFE'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:0.3rem;">
                        <span style="font-size:0.7rem; color:#6B4C4C;"><?php echo e($prog['label']); ?></span>
                        <span style="font-size:0.7rem; font-weight:700; color:<?php echo e($prog['color']); ?>;"><?php echo e($prog['pct']); ?>%</span>
                    </div>
                    <div style="height:6px; background:<?php echo e($prog['track']); ?>; border-radius:999px; overflow:hidden;">
                        <div style="height:100%; width:<?php echo e($prog['pct']); ?>%; background:<?php echo e($prog['color']); ?>; border-radius:999px; transition:width 1s ease;"></div>
                    </div>
                    <span style="font-size:0.65rem; color:#9B7B7B;"><?php echo e($prog['done']); ?>/<?php echo e($poli['total']); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p style="text-align:center; color:#9B7B7B; font-size:0.875rem; padding:1.5rem 0;">Belum ada data kunjungan hari ini</p>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
// Counter-up animation
function animateCounter(el, target) {
    let start = 0;
    const duration = 1000;
    const step = target / (duration / 16);
    const timer = setInterval(() => {
        start += step;
        if (start >= target) { el.textContent = target; clearInterval(timer); return; }
        el.textContent = Math.floor(start);
    }, 16);
}
document.querySelectorAll('[data-counter]').forEach(el => {
    animateCounter(el, parseInt(el.dataset.counter));
});
</script>
<style>
@keyframes slideInLeft {
    from { opacity: 0; transform: translateX(-16px); }
    to   { opacity: 1; transform: translateX(0); }
}
@keyframes barGrow {
    from { transform: scaleX(0); }
    to   { transform: scaleX(1); }
}
</style>
<?php $__env->stopPush(); ?>
<?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/dashboard/partials/admin.blade.php ENDPATH**/ ?>