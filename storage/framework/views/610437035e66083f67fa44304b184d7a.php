<?php $__env->startSection('title', 'RME — ' . ($visit->patient?->nama_lengkap ?? '')); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('rme.index')); ?>" class="hover:opacity-70 transition-opacity" style="color: #6B4C4C;">Rawat Jalan</a>
    <span style="color: #E8D5D5;">/</span>
    <span class="font-medium" style="color: #1A0A0A;"><?php echo e($visit->patient?->nama_lengkap ?? 'RME'); ?></span>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="fade-in" style="min-width:0; overflow-x:hidden;">

<?php if(session('success')): ?>
<div class="flex items-center gap-3 rounded-xl border p-4 mb-4" style="background: #F0FFF4; border-color: #9AE6B4;">
    <i class="fa-solid fa-circle-check flex-shrink-0" style="color: #276749;"></i>
    <span class="text-sm font-medium" style="color: #276749;"><?php echo e(session('success')); ?></span>
</div>
<?php endif; ?>


<div class="bg-white rounded-2xl p-4 mb-4" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-lg font-bold flex-shrink-0"
                 style="background: linear-gradient(135deg, #7B1D1D, #9B2C2C);">
                <?php echo e(strtoupper(substr($visit->patient?->nama_lengkap ?? 'P', 0, 1))); ?>

            </div>
            <div>
                <h2 class="text-base font-bold" style="color: #1A0A0A;"><?php echo e($visit->patient?->nama_lengkap ?? '-'); ?></h2>
                <div class="flex items-center gap-3 mt-0.5 flex-wrap text-xs" style="color: #6B4C4C;">
                    <span class="font-mono">RM: <?php echo e($visit->patient?->no_rm ?? '-'); ?></span>
                    <span>|</span>
                    <span class="font-mono">Rawat: <?php echo e($visit->no_rawat); ?></span>
                    <span>|</span>
                    <span><?php echo e($visit->poli?->nama_poli ?? '-'); ?></span>
                    <span>|</span>
                    <span><?php echo e($visit->doctor?->nama_dokter ?? '-'); ?></span>
                    <span>|</span>
                    <?php
                        $pStyle = match($visit->jenis_penjamin) {
                            'bpjs'     => 'background:#EBF8FF;color:#2B6CB0',
                            'asuransi' => 'background:#FAF5FF;color:#6B21A8',
                            default    => 'background:#F9F5F5;color:#6B4C4C',
                        };
                    ?>
                    <span class="px-2 py-0.5 rounded-full font-semibold" style="<?php echo e($pStyle); ?>">
                        <?php echo e(strtoupper($visit->jenis_penjamin)); ?>

                    </span>
                </div>
            </div>
        </div>
        <?php
            $statusStyle = match($visit->status) {
                'dipanggil','dalam_pemeriksaan' => 'background:#FFFBEB;color:#B45309',
                'farmasi','kasir'               => 'background:#F0FFF4;color:#065F46',
                'batal'                         => 'background:#FEE2E2;color:#991B1B',
                'selesai'                       => 'background:#1F2937;color:#F9FAFB',
                default                         => 'background:#F9F5F5;color:#6B4C4C',
            };
        ?>
        <span class="px-3 py-1.5 rounded-full text-xs font-semibold" style="<?php echo e($statusStyle); ?>">
            <?php echo e(ucfirst(str_replace('_', ' ', $visit->status))); ?>

        </span>
    </div>
</div>


<style>
/* ── RME Sub-menu ── */
.rme-submenu-wrap {
    background: #FFFFFF;
    border-radius: 1rem;
    border: 1px solid #E8D5D5;
    box-shadow: 0 2px 8px rgba(123,29,29,0.08);
    margin-bottom: 1.25rem;
    overflow: hidden;
}
.rme-submenu-bar {
    display: flex;
    gap: 0.25rem;
    padding: 0.5rem;
    overflow-x: auto;
    overflow-y: visible;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
    scrollbar-color: #D4A017 transparent;
    position: relative;
}
.rme-submenu-bar::-webkit-scrollbar { height: 3px; }
.rme-submenu-bar::-webkit-scrollbar-track { background: transparent; }
.rme-submenu-bar::-webkit-scrollbar-thumb { background: #D4A017; border-radius: 2px; }
.rme-tab {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 0.875rem;
    border-radius: 0.625rem;
    font-size: 0.8rem;
    font-weight: 500;
    color: #6B4C4C;
    background: transparent;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    white-space: nowrap;
    flex-shrink: 0;
}
.rme-tab:hover { background: #FFF0F0; color: #7B1D1D; }
.rme-tab.active { background: #7B1D1D; color: #FFFFFF; font-weight: 600; }
.rme-tab.active .dropdown-arrow { color: rgba(255,255,255,0.7); }
.rme-dropdown {
    position: fixed;
    background: #FFFFFF;
    border: 1px solid #E8D5D5;
    border-radius: 0.75rem;
    box-shadow: 0 8px 24px rgba(123,29,29,0.15);
    min-width: 200px;
    z-index: 9999;
    display: none;
    overflow: hidden;
}
.rme-dropdown.open { display: block; }
.rme-dropdown-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1rem;
    font-size: 0.8rem;
    color: #3D1515;
    cursor: pointer;
    transition: background 0.15s;
    border: none;
    background: transparent;
    width: 100%;
    text-align: left;
}
.rme-dropdown-item:hover { background: #FFF0F0; color: #7B1D1D; }
.rme-panel { display: none; }
</style>

<div class="rme-submenu-wrap">
<div class="rme-submenu-bar" id="rme-submenu-bar">
    <button class="rme-tab active" data-panel="soap">
        <i class="fa-solid fa-notes-medical"></i> CPPT/SOAP
    </button>

    <button class="rme-tab" data-panel="riwayat">
        <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Pasien
        <i class="fa-solid fa-chevron-down dropdown-arrow text-xs"></i>
        <div class="rme-dropdown">
            <button class="rme-dropdown-item" data-panel="riwayat">
                <i class="fa-solid fa-history"></i> Data Kunjungan
            </button>
            <button class="rme-dropdown-item" data-panel="satusehat">
                <i class="fa-solid fa-satellite-dish"></i> Monitoring SatuSehat
            </button>
        </div>
    </button>

    <button class="rme-tab" data-panel="penilaian">
        <i class="fa-solid fa-clipboard-list"></i> Penilaian Pasien
        <i class="fa-solid fa-chevron-down dropdown-arrow text-xs"></i>
        <div class="rme-dropdown">
            <button class="rme-dropdown-item" data-panel="asesmen_keperawatan">
                <i class="fa-solid fa-user-nurse"></i> Asesmen Awal Keperawatan
            </button>
            <button class="rme-dropdown-item" data-panel="asesmen_medis">
                <i class="fa-solid fa-stethoscope"></i> Asesmen Awal Medis
            </button>
        </div>
    </button>

    <button class="rme-tab" data-panel="resep">
        <i class="fa-solid fa-prescription-bottle-medical"></i> Resep
        <i class="fa-solid fa-chevron-down dropdown-arrow text-xs"></i>
        <div class="rme-dropdown">
            <button class="rme-dropdown-item" data-panel="resep">
                <i class="fa-solid fa-prescription"></i> Input Resep
            </button>
            <button class="rme-dropdown-item" data-panel="racikan">
                <i class="fa-solid fa-mortar-pestle"></i> Racikan
            </button>
        </div>
    </button>

    <button class="rme-tab" data-panel="penunjang">
        <i class="fa-solid fa-microscope"></i> Permintaan Penunjang
        <i class="fa-solid fa-chevron-down dropdown-arrow text-xs"></i>
        <div class="rme-dropdown">
            <button class="rme-dropdown-item" data-panel="lab_klinik">
                <i class="fa-solid fa-flask"></i> Patologi Klinis
            </button>
            <button class="rme-dropdown-item" data-panel="lab_anatomi">
                <i class="fa-solid fa-vials"></i> Patologi Anatomi
            </button>
            <button class="rme-dropdown-item" data-panel="radiologi">
                <i class="fa-solid fa-x-ray"></i> Radiologi
            </button>
            <button class="rme-dropdown-item" data-panel="ekg">
                <i class="fa-solid fa-heart-pulse"></i> EKG
            </button>
            <button class="rme-dropdown-item" data-panel="usg">
                <i class="fa-solid fa-wave-square"></i> USG
            </button>
            <button class="rme-dropdown-item" data-panel="ctg">
                <i class="fa-solid fa-baby"></i> CTG
            </button>
        </div>
    </button>

    <button class="rme-tab" data-panel="tindakan">
        <i class="fa-solid fa-hand-holding-medical"></i> Tindakan
        <i class="fa-solid fa-chevron-down dropdown-arrow text-xs"></i>
        <div class="rme-dropdown">
            <button class="rme-dropdown-item" data-panel="tagihan_tindakan">
                <i class="fa-solid fa-file-invoice-dollar"></i> Daftar Tindakan/Tagihan
            </button>
            <button class="rme-dropdown-item" data-panel="tindakan_dilakukan">
                <i class="fa-solid fa-check-double"></i> Tindakan Dilakukan
            </button>
        </div>
    </button>

    <button class="rme-tab" data-panel="resume">
        <i class="fa-solid fa-file-medical"></i> Resume
    </button>

    <button class="rme-tab" data-panel="skdp">
        <i class="fa-solid fa-id-card-clip"></i> SKDP BPJS
    </button>
</div>
</div>


<div id="panel-soap" class="rme-panel" style="min-width:0;">
<div class="bg-white rounded-2xl p-5" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <h3 class="text-sm font-bold mb-4 flex items-center gap-2" style="color: #7B1D1D;">
        <i class="fa-solid fa-notes-medical"></i> CPPT / SOAP
    </h3>
    <form method="POST" action="<?php echo e(route('rme.store', $visit->id)); ?>">
        <?php echo csrf_field(); ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color: #6B4C4C;">S — Subjektif</label>
                <textarea name="subjective" rows="4"
                          placeholder="Keluhan utama pasien..."
                          class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 resize-none"
                          style="border-color: #E8D5D5; --tw-ring-color: rgba(123,29,29,0.15);"><?php echo e(old('subjective', $visit->medicalRecord?->subjective)); ?></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color: #6B4C4C;">O — Objektif</label>
                <textarea name="objective" rows="4"
                          placeholder="Pemeriksaan fisik, tanda vital..."
                          class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 resize-none"
                          style="border-color: #E8D5D5; --tw-ring-color: rgba(123,29,29,0.15);"><?php echo e(old('objective', $visit->medicalRecord?->objective)); ?></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color: #6B4C4C;">A — Asesmen</label>
                <textarea name="assessment" rows="4"
                          placeholder="Diagnosis kerja..."
                          class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 resize-none"
                          style="border-color: #E8D5D5; --tw-ring-color: rgba(123,29,29,0.15);"><?php echo e(old('assessment', $visit->medicalRecord?->assessment)); ?></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color: #6B4C4C;">P — Plan</label>
                <textarea name="plan" rows="4"
                          placeholder="Rencana tatalaksana..."
                          class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 resize-none"
                          style="border-color: #E8D5D5; --tw-ring-color: rgba(123,29,29,0.15);"><?php echo e(old('plan', $visit->medicalRecord?->plan)); ?></textarea>
            </div>
        </div>

        
        <div class="mb-4">
            <label class="block text-xs font-semibold mb-1.5" style="color: #6B4C4C;">Diagnosis ICD-10 <span style="color:#7B1D1D;">*</span></label>
            <?php $__errorArgs = ['diagnoses'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs mb-1" style="color:#991B1B;"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <div id="icd10-tags" class="flex flex-wrap gap-2 mb-2">
                <?php $__currentLoopData = $visit->diagnoses ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium"
                      style="background: #FFF5F5; color: #7B1D1D; border: 1px solid #E8D5D5;">
                    <?php echo e($d->icd10Code?->kode); ?> — <?php echo e($d->icd10Code?->nama_penyakit); ?>

                    <input type="hidden" name="diagnoses[]" value="<?php echo e($d->icd10Code?->kode); ?>">
                </span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="relative">
                <input type="text" id="icd10-search" placeholder="Ketik kode atau nama penyakit ICD-10..."
                       class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                       style="border-color: #E8D5D5; --tw-ring-color: rgba(123,29,29,0.15);" autocomplete="off">
                <div id="icd10-results" class="absolute left-0 right-0 mt-1 bg-white rounded-xl border overflow-hidden z-20 hidden"
                     style="border-color: #E8D5D5; box-shadow: 0 8px 24px rgba(123,29,29,0.12); max-height: 200px; overflow-y: auto;"></div>
            </div>
        </div>

        
        <div class="mb-5">
            <label class="block text-xs font-semibold mb-1.5" style="color: #6B4C4C;">Prosedur ICD-9 CM</label>
            <div id="icd9-tags" class="flex flex-wrap gap-2 mb-2">
                <?php $__currentLoopData = $visit->procedures ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium"
                      style="background: #EBF8FF; color: #2B6CB0; border: 1px solid #BEE3F8;">
                    <?php echo e($p->icd9cmCode?->kode); ?> — <?php echo e($p->icd9cmCode?->nama_prosedur); ?>

                    <input type="hidden" name="procedures[]" value="<?php echo e($p->icd9cmCode?->kode); ?>">
                </span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="relative">
                <input type="text" id="icd9-search" placeholder="Ketik kode atau nama prosedur ICD-9 CM..."
                       class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                       style="border-color: #E8D5D5; --tw-ring-color: rgba(123,29,29,0.15);" autocomplete="off">
                <div id="icd9-results" class="absolute left-0 right-0 mt-1 bg-white rounded-xl border overflow-hidden z-20 hidden"
                     style="border-color: #E8D5D5; box-shadow: 0 8px 24px rgba(123,29,29,0.12); max-height: 200px; overflow-y: auto;"></div>
            </div>
        </div>

        <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
                style="background: linear-gradient(135deg, #7B1D1D, #5C1414); box-shadow: 0 2px 8px rgba(123,29,29,0.3);">
            <i class="fa-solid fa-floppy-disk"></i> Simpan SOAP
        </button>
    </form>
</div>
</div>


<div id="panel-riwayat" class="rme-panel" style="min-width:0;">
<div class="bg-white rounded-2xl overflow-hidden" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <div class="px-5 py-3.5 border-b" style="background: #F9F5F5; border-color: #E8D5D5;">
        <span class="text-sm font-semibold flex items-center gap-2" style="color: #1A0A0A;">
            <i class="fa-solid fa-clock-rotate-left" style="color: #7B1D1D;"></i>
            Riwayat Kunjungan Sebelumnya
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead style="background: #F9F5F5;">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">No. Rawat</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Tanggal</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Poli</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Dokter</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Diagnosis</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: #E8D5D5;">
                <?php $__empty_1 = true; $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-red-50 transition-colors">
                    <td class="px-5 py-3.5 font-mono text-xs" style="color: #6B4C4C;"><?php echo e($h->no_rawat); ?></td>
                    <td class="px-5 py-3.5 text-sm" style="color: #1A0A0A;"><?php echo e($h->tanggal_kunjungan?->format('d/m/Y') ?? '-'); ?></td>
                    <td class="px-5 py-3.5 text-sm" style="color: #1A0A0A;"><?php echo e($h->poli?->nama_poli ?? '-'); ?></td>
                    <td class="px-5 py-3.5 text-sm" style="color: #1A0A0A;"><?php echo e($h->doctor?->nama_dokter ?? '-'); ?></td>
                    <td class="px-5 py-3.5 text-xs" style="color: #6B4C4C;">
                        <?php echo e($h->diagnoses->pluck('icd10Code.kode')->filter()->join(', ') ?: '-'); ?>

                    </td>
                    <td class="px-5 py-3.5">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium" style="background: #F9F5F5; color: #6B4C4C;">
                            <?php echo e(ucfirst(str_replace('_', ' ', $h->status))); ?>

                        </span>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="px-5 py-10 text-center text-sm" style="color: #6B4C4C;">
                        Belum ada riwayat kunjungan sebelumnya
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div>


<div id="panel-satusehat" class="rme-panel" style="min-width:0;">
<div class="bg-white rounded-2xl p-8 text-center" style="border: 1px solid #E8D5D5;">
    <i class="fa-solid fa-satellite-dish text-4xl mb-3 block" style="color: #E8D5D5;"></i>
    <p class="text-sm font-semibold" style="color: #1A0A0A;">Monitoring SatuSehat</p>
    <p class="text-xs mt-1" style="color: #6B4C4C;">Fitur monitoring integrasi SatuSehat akan tersedia di sini</p>
</div>
</div>


<div id="panel-penilaian" class="rme-panel" style="min-width:0;">
<div class="bg-white rounded-2xl p-8 text-center" style="border: 1px solid #E8D5D5;">
    <i class="fa-solid fa-clipboard-list text-4xl mb-3 block" style="color: #E8D5D5;"></i>
    <p class="text-sm font-semibold" style="color: #1A0A0A;">Penilaian Pasien</p>
    <p class="text-xs mt-1" style="color: #6B4C4C;">Form asesmen akan tersedia di sini</p>
</div>
</div>


<div id="panel-asesmen_keperawatan" class="rme-panel" style="min-width:0;">
<div class="bg-white rounded-2xl p-5" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <h3 class="text-sm font-bold mb-4 flex items-center gap-2" style="color: #7B1D1D;">
        <i class="fa-solid fa-user-nurse"></i> Asesmen Awal Keperawatan
    </h3>
    <form method="POST" action="<?php echo e(route('rme.store', $visit->id)); ?>#asesmen_keperawatan">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="_panel" value="asesmen_keperawatan">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">Keluhan Utama</label>
                <textarea name="keluhan_utama" rows="3" placeholder="Keluhan utama pasien..."
                          class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 resize-none"
                          style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);"><?php echo e(old('keluhan_utama', $visit->medicalRecord?->subjective)); ?></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">Riwayat Penyakit</label>
                <textarea name="riwayat_penyakit" rows="3" placeholder="Riwayat penyakit sekarang dan dahulu..."
                          class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 resize-none"
                          style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);"></textarea>
            </div>
        </div>
        <p class="text-xs font-semibold mb-3" style="color:#7B1D1D;"><i class="fa-solid fa-heart-pulse mr-1"></i> Tanda Vital</p>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-4">
            <?php $__currentLoopData = [
                ['td','Tekanan Darah','mmHg','120/80'],
                ['nadi','Nadi','x/mnt','80'],
                ['suhu','Suhu','°C','36.5'],
                ['rr','Respirasi','x/mnt','20'],
                ['spo2','SpO2','%','98'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$name,$label,$unit,$ph]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;"><?php echo e($label); ?> <span class="font-normal">(<?php echo e($unit); ?>)</span></label>
                <input type="text" name="vital_<?php echo e($name); ?>" placeholder="<?php echo e($ph); ?>"
                       class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                       style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);">
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">Skala Nyeri (0–10)</label>
                <input type="number" name="skala_nyeri" min="0" max="10" placeholder="0"
                       class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                       style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">Kondisi Umum</label>
                <select name="kondisi_umum" class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                        style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);">
                    <option value="">-- Pilih --</option>
                    <option value="baik">Baik</option>
                    <option value="sedang">Sedang</option>
                    <option value="lemah">Lemah</option>
                    <option value="kritis">Kritis</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">Pemeriksaan Fisik</label>
                <textarea name="pemeriksaan_fisik" rows="3" placeholder="Hasil pemeriksaan fisik..."
                          class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 resize-none"
                          style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);"></textarea>
            </div>
        </div>
        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white hover:opacity-90"
                style="background:linear-gradient(135deg,#7B1D1D,#5C1414); box-shadow:0 2px 8px rgba(123,29,29,0.3);">
            <i class="fa-solid fa-floppy-disk"></i> Simpan Asesmen Keperawatan
        </button>
    </form>
</div>
</div>


<div id="panel-asesmen_medis" class="rme-panel" style="min-width:0;">
<div class="bg-white rounded-2xl p-5" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <h3 class="text-sm font-bold mb-4 flex items-center gap-2" style="color: #7B1D1D;">
        <i class="fa-solid fa-stethoscope"></i> Asesmen Awal Medis
    </h3>
    <form method="POST" action="<?php echo e(route('rme.store', $visit->id)); ?>#asesmen_medis">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="_panel" value="asesmen_medis">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">Anamnesis</label>
                <textarea name="anamnesis" rows="4" placeholder="Riwayat penyakit sekarang, keluhan utama..."
                          class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 resize-none"
                          style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);"><?php echo e(old('anamnesis', $visit->medicalRecord?->subjective)); ?></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">Pemeriksaan Fisik</label>
                <textarea name="pemeriksaan_fisik_medis" rows="4" placeholder="Hasil pemeriksaan fisik lengkap..."
                          class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 resize-none"
                          style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);"><?php echo e(old('pemeriksaan_fisik_medis', $visit->medicalRecord?->objective)); ?></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">Riwayat Alergi</label>
                <textarea name="riwayat_alergi" rows="3" placeholder="Alergi obat, makanan, dll..."
                          class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 resize-none"
                          style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">Riwayat Penyakit Dahulu</label>
                <textarea name="riwayat_penyakit_dahulu" rows="3" placeholder="Penyakit yang pernah diderita..."
                          class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 resize-none"
                          style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);"></textarea>
            </div>
        </div>
        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white hover:opacity-90"
                style="background:linear-gradient(135deg,#7B1D1D,#5C1414); box-shadow:0 2px 8px rgba(123,29,29,0.3);">
            <i class="fa-solid fa-floppy-disk"></i> Simpan Asesmen Medis
        </button>
    </form>
</div>
</div>


<div id="panel-resep" class="rme-panel" style="min-width:0;">
<div class="bg-white rounded-2xl p-5" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <h3 class="text-sm font-bold mb-4 flex items-center gap-2" style="color: #7B1D1D;">
        <i class="fa-solid fa-prescription-bottle-medical"></i> Input Resep
    </h3>
    <form method="POST" action="<?php echo e(route('rme.store', $visit->id)); ?>#resep" id="form-resep">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="_panel" value="resep">
        
        <div class="mb-4">
            <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">Cari Obat</label>
            <div class="relative">
                <input type="text" id="drug-search" placeholder="Ketik nama obat..."
                       class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                       style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);" autocomplete="off">
                <div id="drug-results" class="absolute left-0 right-0 mt-1 bg-white rounded-xl border overflow-hidden z-20 hidden"
                     style="border-color:#E8D5D5; box-shadow:0 8px 24px rgba(123,29,29,0.12); max-height:200px; overflow-y:auto;"></div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4 p-4 rounded-xl" style="background:#F9F5F5; border:1px solid #E8D5D5;">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">Obat</label>
                <input type="text" id="drug-name-display" readonly placeholder="Pilih obat di atas..."
                       class="w-full border rounded-xl px-3 py-2.5 text-sm"
                       style="border-color:#E8D5D5; background:#FFFFFF; color:#1A0A0A;">
                <input type="hidden" id="drug-id-input">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">Jumlah</label>
                <input type="number" id="drug-qty" min="1" value="1"
                       class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                       style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">Dosis</label>
                <input type="text" id="drug-dosis" placeholder="3x1"
                       class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                       style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">Instruksi</label>
                <input type="text" id="drug-instruksi" placeholder="Sesudah makan"
                       class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                       style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);">
            </div>
            <div class="lg:col-span-4 flex justify-end">
                <button type="button" onclick="addDrugItem()"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white"
                        style="background:#7B1D1D;">
                    <i class="fa-solid fa-plus"></i> Tambah Item
                </button>
            </div>
        </div>
        
        <div id="resep-items" class="mb-4 space-y-2"></div>
        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white hover:opacity-90"
                style="background:linear-gradient(135deg,#7B1D1D,#5C1414); box-shadow:0 2px 8px rgba(123,29,29,0.3);">
            <i class="fa-solid fa-floppy-disk"></i> Simpan Resep
        </button>
    </form>
</div>
</div>


<div id="panel-racikan" class="rme-panel" style="min-width:0;">
<div class="bg-white rounded-2xl p-5" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <h3 class="text-sm font-bold mb-4 flex items-center gap-2" style="color: #7B1D1D;">
        <i class="fa-solid fa-mortar-pestle"></i> Racikan Obat
    </h3>
    <form method="POST" action="<?php echo e(route('rme.store', $visit->id)); ?>#racikan">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="_panel" value="racikan">
        <div class="mb-4">
            <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">Nama Racikan <span style="color:#C53030;">*</span></label>
            <input type="text" name="nama_racikan" placeholder="Contoh: Puyer Batuk No.1"
                   class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                   style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);">
        </div>
        <div class="mb-4">
            <label class="block text-xs font-semibold mb-2" style="color:#6B4C4C;">Bahan Racikan</label>
            <div id="racikan-items" class="space-y-2 mb-3">
                <div class="racikan-row grid grid-cols-3 gap-2">
                    <select name="racikan_drug_id[]" class="border rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                            style="border-color:#E8D5D5;">
                        <option value="">-- Pilih Obat --</option>
                        <?php $__currentLoopData = $drugs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $drug): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($drug->id); ?>"><?php echo e($drug->nama); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <input type="number" name="racikan_qty[]" placeholder="Jumlah" min="0" step="0.01"
                           class="border rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                           style="border-color:#E8D5D5;">
                    <input type="text" name="racikan_satuan[]" placeholder="Satuan (mg, ml...)"
                           class="border rounded-xl px-3 py-2.5 text-sm focus:outline-none"
                           style="border-color:#E8D5D5;">
                </div>
            </div>
            <button type="button" onclick="addRacikanRow()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border"
                    style="color:#7B1D1D; border-color:#E8D5D5;">
                <i class="fa-solid fa-plus"></i> Tambah Bahan
            </button>
        </div>
        <div class="mb-4">
            <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">Instruksi Penggunaan</label>
            <textarea name="instruksi_racikan" rows="2" placeholder="Contoh: 3x sehari 1 bungkus sesudah makan"
                      class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 resize-none"
                      style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);"></textarea>
        </div>
        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white hover:opacity-90"
                style="background:linear-gradient(135deg,#7B1D1D,#5C1414); box-shadow:0 2px 8px rgba(123,29,29,0.3);">
            <i class="fa-solid fa-floppy-disk"></i> Simpan Racikan
        </button>
    </form>
</div>
</div>


<?php $__currentLoopData = ['penunjang' => ['fa-microscope','Permintaan Penunjang','Pilih jenis penunjang dari menu dropdown'],
         ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $panelId => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div id="panel-<?php echo e($panelId); ?>" class="rme-panel" style="min-width:0;">
<div class="bg-white rounded-2xl p-8 text-center" style="border: 1px solid #E8D5D5;">
    <i class="fa-solid <?php echo e($info[0]); ?> text-4xl mb-3 block" style="color: #E8D5D5;"></i>
    <p class="text-sm font-semibold" style="color: #1A0A0A;"><?php echo e($info[1]); ?></p>
    <p class="text-xs mt-1" style="color: #6B4C4C;"><?php echo e($info[2]); ?></p>
</div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


<?php $__currentLoopData = [
    'lab_klinik'  => ['fa-flask',       'Patologi Klinis',   'lab_klinik'],
    'lab_anatomi' => ['fa-vials',        'Patologi Anatomi',  'lab_anatomi'],
    'radiologi'   => ['fa-x-ray',        'Radiologi',         'radiologi'],
    'ekg'         => ['fa-heart-pulse',  'EKG',               'ekg'],
    'usg'         => ['fa-wave-square',  'USG',               'usg'],
    'ctg'         => ['fa-baby',         'CTG',               'ctg'],
]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $panelId => [$icon, $title, $type]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div id="panel-<?php echo e($panelId); ?>" class="rme-panel" style="min-width:0;">
<div class="bg-white rounded-2xl p-5" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <h3 class="text-sm font-bold mb-4 flex items-center gap-2" style="color: #7B1D1D;">
        <i class="fa-solid <?php echo e($icon); ?>"></i> Permintaan <?php echo e($title); ?>

    </h3>
    <form method="POST" action="<?php echo e(route('rme.store', $visit->id)); ?>#<?php echo e($panelId); ?>">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="_panel" value="<?php echo e($panelId); ?>">
        <input type="hidden" name="penunjang_type" value="<?php echo e($type); ?>">
        <div class="mb-4">
            <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">Jenis Pemeriksaan</label>
            <select name="examination_type_id" class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                    style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);">
                <option value="">-- Pilih Jenis Pemeriksaan --</option>
                <?php $__currentLoopData = $examinationTypes->where('kategori', $type); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $et): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($et->id); ?>"><?php echo e($et->nama); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($examinationTypes->where('kategori', $type)->isEmpty()): ?>
                <?php $__currentLoopData = $examinationTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $et): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($et->id); ?>"><?php echo e($et->nama); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">Catatan / Instruksi Khusus</label>
            <textarea name="catatan_penunjang" rows="3" placeholder="Instruksi atau keterangan tambahan..."
                      class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 resize-none"
                      style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);"></textarea>
        </div>
        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white hover:opacity-90"
                style="background:linear-gradient(135deg,#7B1D1D,#5C1414); box-shadow:0 2px 8px rgba(123,29,29,0.3);">
            <i class="fa-solid fa-paper-plane"></i> Kirim Permintaan <?php echo e($title); ?>

        </button>
    </form>
</div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


<div id="panel-tindakan" class="rme-panel" style="min-width:0;">
<div class="bg-white rounded-2xl p-8 text-center" style="border: 1px solid #E8D5D5;">
    <i class="fa-solid fa-hand-holding-medical text-4xl mb-3 block" style="color: #E8D5D5;"></i>
    <p class="text-sm font-semibold" style="color: #1A0A0A;">Tindakan</p>
    <p class="text-xs mt-1" style="color: #6B4C4C;">Pilih jenis tindakan dari menu dropdown</p>
</div>
</div>


<div id="panel-tagihan_tindakan" class="rme-panel" style="min-width:0;">
<div class="bg-white rounded-2xl p-8 text-center" style="border: 1px solid #E8D5D5;">
    <i class="fa-solid fa-file-invoice-dollar text-4xl mb-3 block" style="color: #E8D5D5;"></i>
    <p class="text-sm font-semibold" style="color: #1A0A0A;">Daftar Tindakan/Tagihan</p>
    <p class="text-xs mt-1" style="color: #6B4C4C;">Daftar tindakan dan tagihan akan tersedia di sini</p>
</div>
</div>


<div id="panel-tindakan_dilakukan" class="rme-panel" style="min-width:0;">
<div class="bg-white rounded-2xl p-5" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <h3 class="text-sm font-bold mb-4 flex items-center gap-2" style="color: #7B1D1D;">
        <i class="fa-solid fa-check-double"></i> Tindakan Dilakukan
    </h3>
    <form method="POST" action="<?php echo e(route('rme.store', $visit->id)); ?>#tindakan_dilakukan">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="_panel" value="tindakan_dilakukan">
        <?php if($actionMasters->isEmpty()): ?>
        <div class="py-6 text-center">
            <p class="text-sm" style="color:#6B4C4C;">Belum ada data tindakan. Tambahkan di
                <a href="<?php echo e(route('master.action-masters.index')); ?>" style="color:#7B1D1D;" class="font-semibold">Master Tindakan</a>.
            </p>
        </div>
        <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 mb-4">
            <?php $__currentLoopData = $actionMasters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer hover:bg-red-50 transition-colors"
                   style="border-color:#E8D5D5;">
                <input type="checkbox" name="action_ids[]" value="<?php echo e($action->id); ?>"
                       class="rounded" style="accent-color:#7B1D1D;">
                <div>
                    <p class="text-sm font-medium" style="color:#1A0A0A;"><?php echo e($action->nama); ?></p>
                    <?php if($action->icd9cm_code): ?>
                    <p class="text-xs font-mono" style="color:#6B4C4C;"><?php echo e($action->icd9cm_code); ?></p>
                    <?php endif; ?>
                </div>
            </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white hover:opacity-90"
                style="background:linear-gradient(135deg,#7B1D1D,#5C1414); box-shadow:0 2px 8px rgba(123,29,29,0.3);">
            <i class="fa-solid fa-floppy-disk"></i> Simpan Tindakan
        </button>
        <?php endif; ?>
    </form>
</div>
</div>


<div id="panel-resume" class="rme-panel" style="min-width:0;">
<div class="bg-white rounded-2xl p-5" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <h3 class="text-sm font-bold mb-4 flex items-center gap-2" style="color: #7B1D1D;">
        <i class="fa-solid fa-file-medical"></i> Resume Medis
    </h3>
    <form method="POST" action="<?php echo e(route('rme.store', $visit->id)); ?>#resume">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="_panel" value="resume">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">Diagnosis Akhir</label>
                <textarea name="diagnosis_akhir" rows="3" placeholder="Diagnosis akhir pasien..."
                          class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 resize-none"
                          style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);"><?php echo e(old('diagnosis_akhir', $visit->medicalRecord?->assessment)); ?></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">Tindakan yang Dilakukan</label>
                <textarea name="tindakan_dilakukan_resume" rows="3" placeholder="Ringkasan tindakan yang telah dilakukan..."
                          class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 resize-none"
                          style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);"><?php echo e(old('tindakan_dilakukan_resume', $visit->medicalRecord?->plan)); ?></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">Kondisi Pulang</label>
                <select name="kondisi_pulang" class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                        style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);">
                    <option value="">-- Pilih --</option>
                    <option value="sembuh">Sembuh</option>
                    <option value="membaik">Membaik</option>
                    <option value="belum_sembuh">Belum Sembuh</option>
                    <option value="meninggal">Meninggal</option>
                    <option value="dirujuk">Dirujuk</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">Instruksi Follow-up</label>
                <input type="text" name="instruksi_followup" placeholder="Kontrol ulang, anjuran..."
                       class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                       style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);">
            </div>
        </div>
        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white hover:opacity-90"
                style="background:linear-gradient(135deg,#7B1D1D,#5C1414); box-shadow:0 2px 8px rgba(123,29,29,0.3);">
            <i class="fa-solid fa-floppy-disk"></i> Simpan Resume
        </button>
    </form>
</div>
</div>


<div id="panel-skdp" class="rme-panel" style="min-width:0;">
<div class="bg-white rounded-2xl p-5" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <h3 class="text-sm font-bold mb-4 flex items-center gap-2" style="color: #7B1D1D;">
        <i class="fa-solid fa-id-card-clip"></i> SKDP BPJS
    </h3>
    <div id="skdp-result" class="hidden mb-4 p-4 rounded-xl" style="background: #F0FFF4; border: 1px solid #9AE6B4;">
        <p class="text-sm font-medium" style="color: #276749;">SKDP berhasil dibuat.</p>
    </div>
    <div id="skdp-error" class="hidden mb-4 p-4 rounded-xl" style="background: #FEE2E2; border: 1px solid #FECACA;">
        <p class="text-sm font-medium" style="color: #991B1B;" id="skdp-error-msg"></p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-xs font-semibold mb-1.5" style="color: #6B4C4C;">Tanggal Rencana Kontrol</label>
            <input type="date" id="skdp_tanggal"
                   class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                   style="border-color: #E8D5D5; --tw-ring-color: rgba(123,29,29,0.15);">
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1.5" style="color: #6B4C4C;">Spesialisasi Tujuan</label>
            <input type="text" id="skdp_spesialisasi" placeholder="ID Spesialisasi..."
                   class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                   style="border-color: #E8D5D5; --tw-ring-color: rgba(123,29,29,0.15);">
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1.5" style="color: #6B4C4C;">DPJP Dokter</label>
            <input type="text" id="skdp_dokter" placeholder="ID Dokter DPJP..."
                   class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                   style="border-color: #E8D5D5; --tw-ring-color: rgba(123,29,29,0.15);">
        </div>
    </div>
    <button type="button" id="skdp-submit"
            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
            style="background: linear-gradient(135deg, #7B1D1D, #5C1414); box-shadow: 0 2px 8px rgba(123,29,29,0.3);">
        <i class="fa-solid fa-paper-plane"></i> Kirim SKDP
    </button>
</div>
</div>

</div>

<?php $__env->startPush('scripts'); ?>
<script>
// ── Tab switching ──
document.getElementById('panel-soap').style.display = 'block';
document.querySelectorAll('.rme-tab').forEach(function(tab) {
    tab.addEventListener('click', function(e) {
        var panel = this.dataset.panel;
        var dropdown = this.querySelector('.rme-dropdown');
        if (dropdown) {
            e.stopPropagation();
            // Close all other dropdowns
            document.querySelectorAll('.rme-dropdown').forEach(function(d) {
                if (d !== dropdown) d.classList.remove('open');
            });
            // Position dropdown using fixed coordinates
            if (!dropdown.classList.contains('open')) {
                var rect = tab.getBoundingClientRect();
                dropdown.style.top  = (rect.bottom + 6) + 'px';
                dropdown.style.left = rect.left + 'px';
            }
            dropdown.classList.toggle('open');
            return;
        }
        document.querySelectorAll('.rme-tab').forEach(function(t) { t.classList.remove('active'); });
        this.classList.add('active');
        document.querySelectorAll('.rme-panel').forEach(function(p) { p.style.display = 'none'; });
        var target = document.getElementById('panel-' + panel);
        if (target) target.style.display = 'block';
    });
});

// ── Dropdown item click ──
document.querySelectorAll('.rme-dropdown-item').forEach(function(item) {
    item.addEventListener('click', function(e) {
        e.stopPropagation();
        var panel = this.dataset.panel;
        var parentTab = this.closest('.rme-tab');
        document.querySelectorAll('.rme-tab').forEach(function(t) { t.classList.remove('active'); });
        if (parentTab) parentTab.classList.add('active');
        document.querySelectorAll('.rme-dropdown').forEach(function(d) { d.classList.remove('open'); });
        document.querySelectorAll('.rme-panel').forEach(function(p) { p.style.display = 'none'; });
        var target = document.getElementById('panel-' + panel);
        if (target) target.style.display = 'block';
    });
});

document.addEventListener('click', function() {
    document.querySelectorAll('.rme-dropdown').forEach(function(d) { d.classList.remove('open'); });
});

// ── ICD-10 search ──
(function() {
    var input = document.getElementById('icd10-search');
    var results = document.getElementById('icd10-results');
    var tags = document.getElementById('icd10-tags');
    if (!input) return;
    var timer;
    input.addEventListener('input', function() {
        clearTimeout(timer);
        var q = this.value.trim();
        if (q.length < 2) { results.classList.add('hidden'); return; }
        timer = setTimeout(function() {
            fetch('/rme/search/icd10?q=' + encodeURIComponent(q), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function(r) { return r.json(); }).then(function(data) {
                if (!data.length) { results.classList.add('hidden'); return; }
                results.innerHTML = data.map(function(item) {
                    return '<button type="button" class="w-full text-left px-4 py-2.5 text-sm hover:bg-red-50 transition-colors border-b last:border-0" style="border-color:#F0E8E8;color:#1A0A0A;" data-kode="' + item.kode + '" data-nama="' + item.nama_penyakit + '">' +
                        '<span class="font-mono font-semibold" style="color:#7B1D1D;">' + item.kode + '</span> — ' + item.nama_penyakit + '</button>';
                }).join('');
                results.classList.remove('hidden');
                results.querySelectorAll('button').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        var kode = this.dataset.kode;
                        var nama = this.dataset.nama;
                        var tag = document.createElement('span');
                        tag.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium';
                        tag.style.cssText = 'background:#FFF5F5;color:#7B1D1D;border:1px solid #E8D5D5;';
                        tag.innerHTML = kode + ' — ' + nama + '<input type="hidden" name="diagnoses[]" value="' + kode + '"><button type="button" onclick="this.parentElement.remove()" style="color:#7B1D1D;font-size:0.9em;margin-left:2px;">&times;</button>';
                        tags.appendChild(tag);
                        input.value = '';
                        results.classList.add('hidden');
                    });
                });
            });
        }, 300);
    });
    document.addEventListener('click', function(e) { if (!input.contains(e.target)) results.classList.add('hidden'); });
})();

// ── ICD-9 search ──
(function() {
    var input = document.getElementById('icd9-search');
    var results = document.getElementById('icd9-results');
    var tags = document.getElementById('icd9-tags');
    if (!input) return;
    var timer;
    input.addEventListener('input', function() {
        clearTimeout(timer);
        var q = this.value.trim();
        if (q.length < 2) { results.classList.add('hidden'); return; }
        timer = setTimeout(function() {
            fetch('/rme/search/icd9cm?q=' + encodeURIComponent(q), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function(r) { return r.json(); }).then(function(data) {
                if (!data.length) { results.classList.add('hidden'); return; }
                results.innerHTML = data.map(function(item) {
                    return '<button type="button" class="w-full text-left px-4 py-2.5 text-sm hover:bg-blue-50 transition-colors border-b last:border-0" style="border-color:#F0E8E8;color:#1A0A0A;" data-kode="' + item.kode + '" data-nama="' + item.nama_prosedur + '">' +
                        '<span class="font-mono font-semibold" style="color:#2B6CB0;">' + item.kode + '</span> — ' + item.nama_prosedur + '</button>';
                }).join('');
                results.classList.remove('hidden');
                results.querySelectorAll('button').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        var kode = this.dataset.kode;
                        var nama = this.dataset.nama;
                        var tag = document.createElement('span');
                        tag.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium';
                        tag.style.cssText = 'background:#EBF8FF;color:#2B6CB0;border:1px solid #BEE3F8;';
                        tag.innerHTML = kode + ' — ' + nama + '<input type="hidden" name="procedures[]" value="' + kode + '"><button type="button" onclick="this.parentElement.remove()" style="color:#2B6CB0;font-size:0.9em;margin-left:2px;">&times;</button>';
                        tags.appendChild(tag);
                        input.value = '';
                        results.classList.add('hidden');
                    });
                });
            });
        }, 300);
    });
    document.addEventListener('click', function(e) { if (!input.contains(e.target)) results.classList.add('hidden'); });
})();

// ── SKDP ──
document.getElementById('skdp-submit')?.addEventListener('click', function() {
    var visitId = <?php echo e($visit->id); ?>;
    var data = {
        tanggal_rencana_kontrol: document.getElementById('skdp_tanggal').value,
        specialization_id: document.getElementById('skdp_spesialisasi').value,
        dpjp_doctor_id: document.getElementById('skdp_dokter').value,
    };
    fetch('/rme/' + visitId + '/skdp', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify(data),
    }).then(function(r) { return r.json(); }).then(function(res) {
        if (res.success) {
            document.getElementById('skdp-result').classList.remove('hidden');
            document.getElementById('skdp-error').classList.add('hidden');
        } else {
            document.getElementById('skdp-error-msg').textContent = JSON.stringify(res.errors || res.message || 'Terjadi kesalahan');
            document.getElementById('skdp-error').classList.remove('hidden');
            document.getElementById('skdp-result').classList.add('hidden');
        }
    });
});

// ── Drug search for Resep ──
(function() {
    var input = document.getElementById('drug-search');
    var results = document.getElementById('drug-results');
    if (!input) return;
    var drugs = <?php echo json_encode($drugs->map(fn($d) => ['id' => $d->id, 'nama' => $d->nama, 'kode' => $d->kode])) ?>;
    input.addEventListener('input', function() {
        var q = this.value.trim().toLowerCase();
        if (q.length < 1) { results.classList.add('hidden'); return; }
        var filtered = drugs.filter(function(d) { return d.nama.toLowerCase().includes(q); }).slice(0, 10);
        if (!filtered.length) { results.classList.add('hidden'); return; }
        results.innerHTML = filtered.map(function(d) {
            return '<button type="button" class="w-full text-left px-4 py-2.5 text-sm hover:bg-red-50 transition-colors border-b last:border-0" style="border-color:#F0E8E8;color:#1A0A0A;" data-id="' + d.id + '" data-nama="' + d.nama + '">' +
                '<span class="font-semibold" style="color:#7B1D1D;">' + d.nama + '</span>' +
                (d.kode ? ' <span class="text-xs font-mono" style="color:#9B7B7B;">(' + d.kode + ')</span>' : '') + '</button>';
        }).join('');
        results.classList.remove('hidden');
        results.querySelectorAll('button').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('drug-id-input').value = this.dataset.id;
                document.getElementById('drug-name-display').value = this.dataset.nama;
                input.value = '';
                results.classList.add('hidden');
            });
        });
    });
    document.addEventListener('click', function(e) { if (!input.contains(e.target) && !results.contains(e.target)) results.classList.add('hidden'); });
})();

var resepItemCount = 0;
function addDrugItem() {
    var drugId = document.getElementById('drug-id-input').value;
    var drugName = document.getElementById('drug-name-display').value;
    var qty = document.getElementById('drug-qty').value;
    var dosis = document.getElementById('drug-dosis').value;
    var instruksi = document.getElementById('drug-instruksi').value;
    if (!drugId || !drugName) { alert('Pilih obat terlebih dahulu'); return; }
    resepItemCount++;
    var container = document.getElementById('resep-items');
    var div = document.createElement('div');
    div.className = 'flex items-center gap-3 p-3 rounded-xl';
    div.style.cssText = 'background:#F9F5F5; border:1px solid #E8D5D5;';
    div.innerHTML = '<input type="hidden" name="resep_drug_id[]" value="' + drugId + '">' +
        '<input type="hidden" name="resep_qty[]" value="' + qty + '">' +
        '<input type="hidden" name="resep_dosis[]" value="' + dosis + '">' +
        '<input type="hidden" name="resep_instruksi[]" value="' + instruksi + '">' +
        '<div class="flex-1"><p class="text-sm font-semibold" style="color:#1A0A0A;">' + drugName + '</p>' +
        '<p class="text-xs" style="color:#6B4C4C;">' + qty + ' — ' + dosis + (instruksi ? ' — ' + instruksi : '') + '</p></div>' +
        '<button type="button" onclick="this.parentElement.remove()" class="text-xs px-2 py-1 rounded-lg" style="color:#991B1B; background:#FEE2E2;">' +
        '<i class="fa-solid fa-xmark"></i></button>';
    container.appendChild(div);
    document.getElementById('drug-id-input').value = '';
    document.getElementById('drug-name-display').value = '';
    document.getElementById('drug-qty').value = '1';
    document.getElementById('drug-dosis').value = '';
    document.getElementById('drug-instruksi').value = '';
}

// ── Racikan row ──
function addRacikanRow() {
    var drugs = <?php echo json_encode($drugs->map(fn($d) => ['id' => $d->id, 'nama' => $d->nama]), 512) ?>;
    var container = document.getElementById('racikan-items');
    var div = document.createElement('div');
    div.className = 'racikan-row grid grid-cols-3 gap-2';
    var opts = drugs.map(function(d) { return '<option value="' + d.id + '">' + d.nama + '</option>'; }).join('');
    div.innerHTML = '<select name="racikan_drug_id[]" class="border rounded-xl px-3 py-2.5 text-sm focus:outline-none" style="border-color:#E8D5D5;"><option value="">-- Pilih Obat --</option>' + opts + '</select>' +
        '<input type="number" name="racikan_qty[]" placeholder="Jumlah" min="0" step="0.01" class="border rounded-xl px-3 py-2.5 text-sm focus:outline-none" style="border-color:#E8D5D5;">' +
        '<input type="text" name="racikan_satuan[]" placeholder="Satuan (mg, ml...)" class="border rounded-xl px-3 py-2.5 text-sm focus:outline-none" style="border-color:#E8D5D5;">';
    container.appendChild(div);
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/rme/show.blade.php ENDPATH**/ ?>