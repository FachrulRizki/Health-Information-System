<?php $__env->startSection('title', 'Rawat Inap — ' . ($visit->patient?->nama_lengkap ?? '')); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('inpatient.index')); ?>" class="hover:opacity-70 transition-opacity" style="color: #6B4C4C;">Rawat Inap</a>
    <span style="color: #E8D5D5;">/</span>
    <span class="font-medium" style="color: #1A0A0A;"><?php echo e($visit->patient?->nama_lengkap ?? 'Detail'); ?></span>
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
                <div class="flex items-center gap-3 mt-1.5 flex-wrap text-xs" style="color: #6B4C4C;">
                    <span><i class="fa-solid fa-door-open mr-1" style="color:#7B1D1D;"></i>Kamar: <strong><?php echo e($visit->inpatientRecord?->bed?->room?->nama_kamar ?? '-'); ?></strong></span>
                    <span>|</span>
                    <span><i class="fa-solid fa-bed mr-1" style="color:#7B1D1D;"></i>Bed: <strong><?php echo e($visit->inpatientRecord?->bed?->kode_bed ?? '-'); ?></strong></span>
                    <span>|</span>
                    <span><i class="fa-solid fa-calendar-check mr-1" style="color:#7B1D1D;"></i>Tgl Masuk: <strong><?php echo e($visit->inpatientRecord?->tanggal_masuk?->format('d/m/Y') ?? '-'); ?></strong></span>
                </div>
            </div>
        </div>
        <span class="px-3 py-1.5 rounded-full text-xs font-semibold"
              style="background: #FFF5F5; color: #7B1D1D; border: 1px solid #E8D5D5;">
            RAWAT INAP
        </span>
    </div>
</div>


<style>
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
                <i class="fa-solid fa-user-nurse"></i> Asesmen Keperawatan
            </button>
            <button class="rme-dropdown-item" data-panel="asesmen_medis">
                <i class="fa-solid fa-stethoscope"></i> Asesmen Medis
            </button>
        </div>
    </button>

    <button class="rme-tab" data-panel="resep">
        <i class="fa-solid fa-prescription-bottle-medical"></i> Resep
        <i class="fa-solid fa-chevron-down dropdown-arrow text-xs"></i>
        <div class="rme-dropdown">
            <button class="rme-dropdown-item" data-panel="resep_dokter">
                <i class="fa-solid fa-prescription"></i> Resep Dokter
            </button>
            <button class="rme-dropdown-item" data-panel="resep_harian">
                <i class="fa-solid fa-calendar-day"></i> Resep Harian
            </button>
            <button class="rme-dropdown-item" data-panel="resep_pulang">
                <i class="fa-solid fa-house-medical"></i> Resep Pulang
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

    <button class="rme-tab" data-panel="resume_pulang">
        <i class="fa-solid fa-file-medical"></i> Resume Pulang
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
    <form method="POST" action="<?php echo e(route('inpatient.store', $visit->id)); ?>">
        <?php echo csrf_field(); ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color: #6B4C4C;">S — Subjektif</label>
                <textarea name="subjective" rows="4" placeholder="Keluhan utama pasien..."
                          class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 resize-none"
                          style="border-color: #E8D5D5; --tw-ring-color: rgba(123,29,29,0.15);"><?php echo e(old('subjective', $visit->medicalRecord?->subjective)); ?></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color: #6B4C4C;">O — Objektif</label>
                <textarea name="objective" rows="4" placeholder="Pemeriksaan fisik, tanda vital..."
                          class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 resize-none"
                          style="border-color: #E8D5D5; --tw-ring-color: rgba(123,29,29,0.15);"><?php echo e(old('objective', $visit->medicalRecord?->objective)); ?></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color: #6B4C4C;">A — Asesmen</label>
                <textarea name="assessment" rows="4" placeholder="Diagnosis kerja..."
                          class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 resize-none"
                          style="border-color: #E8D5D5; --tw-ring-color: rgba(123,29,29,0.15);"><?php echo e(old('assessment', $visit->medicalRecord?->assessment)); ?></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color: #6B4C4C;">P — Plan</label>
                <textarea name="plan" rows="4" placeholder="Rencana tatalaksana..."
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
                    <button type="button" onclick="this.parentElement.remove()" style="color:#7B1D1D;">&times;</button>
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
                      style="background: #FFF5F5; color: #7B1D1D; border: 1px solid #E8D5D5;">
                    <?php echo e($p->icd9cmCode?->kode); ?> — <?php echo e($p->icd9cmCode?->nama_prosedur); ?>

                    <input type="hidden" name="procedures[]" value="<?php echo e($p->icd9cmCode?->kode); ?>">
                    <button type="button" onclick="this.parentElement.remove()" style="color:#7B1D1D;">&times;</button>
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
    <p class="text-xs mt-1" style="color: #6B4C4C;">Pilih jenis penilaian dari menu dropdown</p>
</div>
</div>


<div id="panel-asesmen_keperawatan" class="rme-panel" style="min-width:0;">
<div class="bg-white rounded-2xl p-5" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <h3 class="text-sm font-bold mb-4 flex items-center gap-2" style="color: #7B1D1D;">
        <i class="fa-solid fa-user-nurse"></i> Asesmen Keperawatan
    </h3>
    <form method="POST" action="<?php echo e(route('inpatient.store', $visit->id)); ?>">
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
        <i class="fa-solid fa-stethoscope"></i> Asesmen Medis
    </h3>
    <form method="POST" action="<?php echo e(route('inpatient.store', $visit->id)); ?>">
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
<div class="bg-white rounded-2xl p-8 text-center" style="border: 1px solid #E8D5D5;">
    <i class="fa-solid fa-prescription-bottle-medical text-4xl mb-3 block" style="color: #E8D5D5;"></i>
    <p class="text-sm font-semibold" style="color: #1A0A0A;">Resep</p>
    <p class="text-xs mt-1" style="color: #6B4C4C;">Pilih jenis resep dari menu dropdown</p>
</div>
</div>

<div id="panel-resep_dokter" class="rme-panel" style="min-width:0;">
<div class="bg-white rounded-2xl p-5" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <h3 class="text-sm font-bold mb-4 flex items-center gap-2" style="color: #7B1D1D;">
        <i class="fa-solid fa-prescription"></i> Resep Dokter
    </h3>
    <form method="POST" action="<?php echo e(route('inpatient.store', $visit->id)); ?>" id="form-resep-dokter">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="_panel" value="resep_dokter">
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
            <i class="fa-solid fa-floppy-disk"></i> Simpan Resep Dokter
        </button>
    </form>
</div>
</div>


<div id="panel-resep_harian" class="rme-panel" style="min-width:0;">
<div class="bg-white rounded-2xl p-8 text-center" style="border: 1px solid #E8D5D5;">
    <i class="fa-solid fa-calendar-day text-4xl mb-3 block" style="color: #E8D5D5;"></i>
    <p class="text-sm font-semibold" style="color: #1A0A0A;">Resep Harian</p>
    <p class="text-xs mt-1" style="color: #6B4C4C;">Fitur resep harian rawat inap akan tersedia di sini</p>
</div>
</div>


<div id="panel-resep_pulang" class="rme-panel" style="min-width:0;">
<div class="bg-white rounded-2xl p-8 text-center" style="border: 1px solid #E8D5D5;">
    <i class="fa-solid fa-house-medical text-4xl mb-3 block" style="color: #E8D5D5;"></i>
    <p class="text-sm font-semibold" style="color: #1A0A0A;">Resep Pulang</p>
    <p class="text-xs mt-1" style="color: #6B4C4C;">Fitur resep pulang akan tersedia di sini</p>
</div>
</div>


<div id="panel-penunjang" class="rme-panel" style="min-width:0;">
<div class="bg-white rounded-2xl p-8 text-center" style="border: 1px solid #E8D5D5;">
    <i class="fa-solid fa-microscope text-4xl mb-3 block" style="color: #E8D5D5;"></i>
    <p class="text-sm font-semibold" style="color: #1A0A0A;">Permintaan Penunjang</p>
    <p class="text-xs mt-1" style="color: #6B4C4C;">Pilih jenis penunjang dari menu dropdown</p>
</div>
</div>


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
    <form method="POST" action="<?php echo e(route('inpatient.store', $visit->id)); ?>">
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
    <form method="POST" action="<?php echo e(route('inpatient.store', $visit->id)); ?>">
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/kiro/nesthis/resources/views/inpatient/show.blade.php ENDPATH**/ ?>