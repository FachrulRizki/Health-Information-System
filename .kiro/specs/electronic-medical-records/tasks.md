# Rencana Implementasi: Aplikasi Rekam Medis Elektronik (RME)

## Ikhtisar

Implementasi dilakukan dalam 4 fase bertahap. Setiap fase membangun di atas fase sebelumnya, dimulai dari fondasi autentikasi dan master data, kemudian modul klinis inti, lalu integrasi eksternal, dan diakhiri dengan laporan serta dashboard.

**Stack Teknologi:** PHP/Laravel, MySQL, Redis, Blade + Vue.js + Tailwind CSS, Laravel Reverb (WebSocket), Eris (property-based testing)

---

## Fase 1: Auth, Master Data, dan RME Dasar

- [x] 1. Setup proyek dan infrastruktur dasar
  - Inisialisasi proyek Laravel dengan konfigurasi Docker Compose (Nginx, PHP-FPM, MySQL, Redis, Queue Worker, Laravel Reverb)
  - Buat struktur direktori sesuai desain: `app/Http/Controllers/`, `app/Services/`, `app/Repositories/`, `app/Jobs/`, `app/Events/`
  - Konfigurasi koneksi database MySQL, Redis queue, dan Redis cache di `.env`
  - Install dependensi: Laravel Sanctum, Laravel Reverb, Eris (property-based testing), Laravel Excel, DomPDF
  - _Requirements: 1.1, 2.1_

- [x] 2. Implementasi sistem autentikasi dan otorisasi
  - [x] 2.1 Buat migrasi tabel `users`, `permissions`, `user_permissions`
    - Field `users`: id, username, password, role, is_active, locked_until, failed_login_count, timestamps
    - Field `permissions`: id, menu_key, menu_label, parent_key, sort_order
    - Field `user_permissions`: id, user_id, permission_id, is_granted
    - _Requirements: 1.1, 1.6_

  - [x] 2.2 Implementasi `AuthService` dan `AuthController`
    - Method `login(username, password)`: validasi kredensial, cek is_active, cek locked_until
    - Method `logout()`: invalidasi sesi
    - Logika lockout: increment failed_login_count, set locked_until setelah 5 kali gagal
    - Session timeout 30 menit via `SessionTimeoutMiddleware`
    - _Requirements: 1.1, 1.3, 1.4, 1.5, 1.8_

  - [ ]* 2.3 Tulis property test untuk autentikasi (Property 1)
    - **Property 1: Autentikasi hanya menerima kredensial valid**
    - **Validates: Requirements 1.1, 1.4**

  - [ ]* 2.4 Tulis property test untuk lockout akun (Property 2)
    - **Property 2: Akun terkunci setelah 5 kali gagal login**
    - **Validates: Requirements 1.5**

  - [x] 2.5 Implementasi `PermissionService` dan `PermissionMiddleware`
    - Method `getUserPermissions(userId)`: ambil permission dari DB, cache per user (TTL 5 menit)
    - Method `hasPermission(userId, menuKey)`: cek izin akses
    - Admin otomatis mendapat full akses tanpa cek tabel `user_permissions`
    - Menu tanpa izin disembunyikan (hidden) — bukan disabled
    - _Requirements: 1.6, 1.7_

  - [ ]* 2.6 Tulis property test untuk visibilitas menu (Property 3)
    - **Property 3: Permission granular menentukan visibilitas menu**
    - **Validates: Requirements 1.6, 1.7**

  - [x] 2.7 Implementasi `AuditTrailMiddleware` dan model `AuditTrail`
    - Buat migrasi tabel `audit_trails`: user_id, action, model_type, model_id, old_values, new_values, ip_address
    - Middleware mencatat setiap request yang mengubah data (POST/PUT/PATCH/DELETE)
    - Catat akses baca rekam medis meskipun tidak ada perubahan
    - _Requirements: 1.9, 20.4, 20.6_

  - [ ]* 2.8 Tulis property test untuk audit trail (Property 4)
    - **Property 4: Setiap aksi pengguna tercatat di audit trail**
    - **Validates: Requirements 1.9, 20.4**

  - [x] 2.9 Buat seeder akun default untuk semua peran dan halaman login Blade
    - Seed 7 akun: Admin, Dokter, Perawat, Farmasi, Kasir, Petugas Pendaftaran, Manajemen
    - Halaman login dengan form username/password, tampilkan pesan error jika gagal
    - Redirect ke dashboard sesuai peran setelah login berhasil
    - _Requirements: 1.2, 1.3_

- [x] 3. Checkpoint Fase 1A — Pastikan semua test autentikasi lulus
  - Pastikan semua test lulus, tanyakan kepada pengguna jika ada pertanyaan.

- [x] 4. Implementasi Master Data
  - [x] 4.1 Buat migrasi semua tabel master data
    - Tabel: `polis`, `sub_polis`, `doctors`, `specializations`, `sub_specializations`, `icd10_codes`, `icd9cm_codes`, `drugs`, `drug_units`, `drug_categories`, `suppliers`, `rooms`, `beds`, `action_masters`, `action_tariffs`, `doctor_schedules`, `examination_types`
    - _Requirements: 2.1_

  - [x] 4.2 Implementasi CRUD master data dengan validasi
    - Buat `MasterDataController` dan resource routes untuk setiap entitas master
    - Validasi keunikan dan kelengkapan field wajib via Form Request classes
    - Cegah penghapusan data yang masih direferensikan oleh transaksi aktif
    - Fitur pencarian dan filter pada setiap halaman master data
    - _Requirements: 2.2, 2.3, 2.4_

  - [ ]* 4.3 Tulis property test untuk validasi master data (Property 23)
    - **Property 23: Validasi data master menolak input tidak lengkap atau duplikat**
    - **Validates: Requirements 2.2**

  - [ ]* 4.4 Tulis property test untuk proteksi penghapusan master data (Property 24)
    - **Property 24: Data master yang direferensikan tidak dapat dihapus**
    - **Validates: Requirements 2.3**

  - [x] 4.5 Buat seed data untuk semua tabel master
    - Seed data ICD-10, ICD-9 CM, obat contoh, poli, dokter, kamar, bed
    - _Requirements: 2.6_

  - [x] 4.6 Implementasi `ApiSettingsController` dan tabel `api_settings`
    - Migrasi tabel `api_settings`: integration_name, endpoint_url, sandbox_url, consumer_key_encrypted, consumer_secret_encrypted, mode, additional_params, is_active
    - CRUD konfigurasi API dengan enkripsi kredensial via Laravel `Crypt`
    - Toggle aktif/nonaktif per integrasi
    - Indikator visual mode testing/production
    - Submenu "Setting API" di menu Master Data
    - _Requirements: 2.7, 21.1, 21.2, 21.3, 21.7, 21.11_

  - [ ]* 4.7 Tulis property test untuk enkripsi kredensial API (Property 22)
    - **Property 22: Kredensial API eksternal selalu tersimpan terenkripsi**
    - **Validates: Requirements 21.3**

- [x] 5. Implementasi Pendaftaran Pasien
  - [x] 5.1 Buat migrasi tabel `patients` dan `visits`
    - Field `patients`: id, no_rm, nama_lengkap, tanggal_lahir, jenis_kelamin, alamat, nik_encrypted, no_telepon_encrypted, jenis_penjamin, no_bpjs, no_polis_asuransi, nama_asuransi
    - Field `visits`: id, no_rawat, patient_id, poli_id, doctor_id, user_id, jenis_penjamin, no_sep, status, tanggal_kunjungan
    - _Requirements: 3.1, 3.2_

  - [x] 5.2 Implementasi `PatientService` dengan generator NoRM dan enkripsi data sensitif
    - Method `generateNoRM()`: format NoRM unik (contoh: RM-YYYYMMDD-XXXXX), gunakan database transaction + unique constraint
    - Method `generateNoRawat()`: format NoRawat unik per kunjungan
    - Enkripsi NIK dan nomor telepon menggunakan Laravel `Crypt` sebelum disimpan
    - _Requirements: 3.3, 3.9, 20.1_

  - [ ]* 5.3 Tulis property test untuk keunikan NoRM (Property 5)
    - **Property 5: NoRM yang dihasilkan selalu unik**
    - **Validates: Requirements 3.3**

  - [ ]* 5.4 Tulis property test untuk keunikan NoRawat (Property 6)
    - **Property 6: NoRawat yang dihasilkan selalu unik**
    - **Validates: Requirements 3.9**

  - [ ]* 5.5 Tulis property test untuk enkripsi data sensitif pasien (Property 21)
    - **Property 21: Data sensitif pasien selalu tersimpan terenkripsi**
    - **Validates: Requirements 20.1**

  - [x] 5.6 Implementasi `RegistrationController` dan form pendaftaran Blade
    - Form pendaftaran pasien baru: nama, tanggal lahir, jenis kelamin, alamat, NIK, telepon, jenis penjamin
    - Pencarian pasien lama berdasarkan nama/NoRM/NIK (response < 2 detik)
    - Dukungan tiga jenis penjamin: Umum, BPJS, Asuransi
    - Field nomor polis dan nama asuransi untuk penjamin Asuransi
    - _Requirements: 3.1, 3.2, 3.4, 3.8_

- [x] 6. Implementasi Modul Antrian
  - [x] 6.1 Buat migrasi tabel `queue_entries` dan implementasi `QueueService`
    - Field: id, visit_id, poli_id, queue_number, status, timestamps
    - Method `assignQueue(visitId, poliId)`: buat entri antrian dengan status "menunggu"
    - Method `updateStatus(queueId, status)`: update status dan dispatch event
    - _Requirements: 3.10, 4.5_

  - [x] 6.2 Implementasi Event `QueueStatusUpdated` dan broadcast WebSocket
    - Event membawa data: queue_id, visit_id, poli_id, queue_number, status, patient_name
    - Broadcast ke channel `poli.{poli_id}` via Laravel Reverb
    - _Requirements: 4.1, 4.2_

  - [ ]* 6.3 Tulis property test untuk event WebSocket antrian (Property 9)
    - **Property 9: Perubahan status antrian selalu memicu event WebSocket**
    - **Validates: Requirements 4.2**

  - [ ]* 6.4 Tulis property test untuk entri antrian otomatis (Property 8)
    - **Property 8: Pendaftaran selesai selalu menghasilkan entri antrian**
    - **Validates: Requirements 3.10**

  - [x] 6.5 Implementasi `QueueController` dan halaman antrian Blade
    - Halaman manajemen antrian per poli dengan update real-time via Vue.js + WebSocket
    - Display publik antrian (tanpa auth) yang subscribe channel WebSocket
    - Tampilkan nama dan nomor antrian saat pasien dipanggil
    - _Requirements: 4.1, 4.3, 4.4_

- [x] 7. Implementasi Rekam Medis Rawat Jalan
  - [x] 7.1 Buat migrasi tabel `medical_records`, `diagnoses`, `procedures`
    - Field `medical_records`: id, visit_id, subjective, objective, assessment, plan, created_by
    - Field `diagnoses`: id, visit_id, icd10_code, is_primary
    - Field `procedures`: id, visit_id, icd9cm_code, performed_by
    - _Requirements: 5.1, 5.2, 5.3_

  - [x] 7.2 Implementasi `RMEService` dan `RMEController`
    - Method `saveSOAP(visitId, data)`: validasi minimal 1 diagnosa ICD-10, simpan SOAP
    - Method `getPatientHistory(patientId)`: ambil riwayat kunjungan sebelumnya
    - Pencarian diagnosa ICD-10 dan tindakan ICD-9 CM
    - Sub-form: penilaian keperawatan, asesmen awal, formulir medis umum
    - Sub-form IGD: asesmen awal medis IGD, penilaian keperawatan IGD, observasi IGD
    - Fitur permintaan penunjang (Lab, Radiologi, EKG, USG, CTG)
    - Daftar tindakan dokter dalam bentuk checkbox dari master tindakan
    - Fitur unggah dokumen/gambar pendukung
    - Formulir resume kunjungan rawat jalan
    - _Requirements: 5.1–5.12_

  - [ ]* 7.3 Tulis property test untuk validasi diagnosa ICD-10 (Property 10)
    - **Property 10: Rekam medis tanpa diagnosa ICD-10 ditolak**
    - **Validates: Requirements 5.5**

  - [x] 7.4 Implementasi validasi SKDP
    - Validasi: SEP sudah terbit, tanggal rencana kontrol diisi, spesialis/sub spesialis dipilih, DPJP dipilih
    - _Requirements: 5.12_

- [x] 8. Implementasi Rekam Medis Rawat Inap
  - [x] 8.1 Buat migrasi tabel rawat inap dan implementasi `InpatientController`
    - Daftar pasien rawat inap: NoRawat, NoRM, Kode Dokter, Dokter PJ, Nama Poli, Nama Pasien, Nama Kamar, Jenis Bayar, Status Pulang
    - Formulir SOAP rawat inap dengan diagnosa ICD-10 dan tindakan ICD-9 CM
    - Formulir penilaian keperawatan rawat inap, penilaian medis, asesmen awal rawat inap
    - Tiga jenis resep: resep dokter, obat terjadwal, resep pulang
    - Formulir resume pulang pasien rawat inap
    - _Requirements: 6.1–6.7_

  - [x] 8.2 Implementasi `BedManagementService` dan Event `BedStatusUpdated`
    - Method `assignBed(visitId, bedId)`: update status bed, dispatch `SyncAplicareJob`
    - Method `releaseBed(bedId)`: bebaskan bed saat pasien pulang
    - Event `BedStatusUpdated` broadcast via WebSocket ke channel `beds`
    - Tampilan peta bed real-time dengan status: tersedia, terisi, dalam perawatan, tidak aktif
    - _Requirements: 7.1, 7.2, 7.5_

  - [ ]* 8.3 Tulis property test untuk dispatch job Aplicare (Property 11)
    - **Property 11: Penempatan pasien di bed selalu mendispatch job sinkronisasi Aplicare**
    - **Validates: Requirements 7.3**

- [x] 9. Checkpoint Fase 1 — Pastikan semua test Fase 1 lulus
  - Pastikan semua test lulus, tanyakan kepada pengguna jika ada pertanyaan.


---

## Fase 2: BPJS Integration dan Farmasi

- [x] 10. Implementasi integrasi BPJS VClaim
  - [x] 10.1 Implementasi `ExternalApiServiceInterface` dan `BPJSService`
    - Interface: `send(payload)`, `testConnection()`, `isTestingMode()`
    - `BPJSService::validatePeserta(noKartu)`: GET `/Peserta/nokartu/{noKartu}`, cache hasil 1 jam
    - `BPJSService::insertSEP(data)`: POST `/SEP/insert`
    - `BPJSService::updateSEP(data)`: PUT endpoint VClaim yang sesuai
    - `BPJSService::monitorKlaim()`: endpoint monitoring klaim
    - Enkripsi kredensial BPJS, baca dari `api_settings` via `Crypt::decrypt()`
    - Resolusi mode: jika `isTestingMode()` → gunakan `MockApiService`, jika tidak → kirim ke URL produksi
    - _Requirements: 13.1–13.10_

  - [ ]* 10.2 Tulis property test untuk caching data peserta BPJS (Property 18)
    - **Property 18: Data peserta BPJS yang berhasil diambil selalu di-cache**
    - **Validates: Requirements 13.2**

  - [x] 10.3 Implementasi `RetryPolicy` dengan exponential backoff dan `MockApiService`
    - `RetryPolicy`: retry maksimal 3 kali, interval: 1 menit, 5 menit, 15 menit
    - Catat setiap kegagalan ke log sistem
    - `MockApiService`: kembalikan mock response realistis per integrasi (BPJS, SATUSEHAT, Aplicare)
    - _Requirements: 13.6, 13.7_

  - [ ]* 10.4 Tulis property test untuk retry policy API eksternal (Property 12)
    - **Property 12: Retry policy diterapkan konsisten untuk semua kegagalan API eksternal**
    - **Validates: Requirements 7.4, 13.6**

  - [ ]* 10.5 Tulis property test untuk mode testing mencegah request produksi (Property 25)
    - **Property 25: Mode testing mencegah request ke API produksi**
    - **Validates: Requirements 13.10, 14.9, 15.5, 21.9**

  - [x] 10.6 Integrasi BPJS ke alur pendaftaran pasien
    - Panggil `BPJSService::validatePeserta()` saat penjamin BPJS dipilih
    - Tampilkan status peserta tidak aktif dan minta konfirmasi petugas
    - Panggil `BPJSService::insertSEP()` setelah validasi berhasil
    - _Requirements: 3.5, 3.6, 3.7_

  - [ ]* 10.7 Tulis property test untuk SEP pada pendaftaran BPJS valid (Property 7)
    - **Property 7: Pendaftaran BPJS valid selalu menghasilkan SEP**
    - **Validates: Requirements 3.7**

  - [x] 10.8 Implementasi `ConnectionTestService` dan tombol "Test Koneksi"
    - Method `testConnection(integrationName)`: kirim request uji ke API, tampilkan kode status dan pesan
    - Respons dalam waktu < 10 detik
    - Tampilkan pesan error informatif beserta saran penanganan jika gagal
    - _Requirements: 21.4, 21.5, 21.6_

- [x] 11. Implementasi `SendBPJSClaimJob` dan billing BPJS
  - [x] 11.1 Buat migrasi tabel `bills` dan `bill_items`, implementasi `BillingService`
    - Field `bills`: id, visit_id, total_amount, payment_method, status, bpjs_claim_status
    - Field `bill_items`: id, bill_id, item_type, item_id, item_name, unit_price, quantity, subtotal
    - Method `generateBill(visitId)`: kalkulasi total dari semua item layanan
    - Method `processPayment(billId, method, data)`: proses pembayaran sesuai metode
    - _Requirements: 11.1, 11.2, 11.3, 11.4, 11.7_

  - [ ]* 11.2 Tulis property test untuk kalkulasi total tagihan (Property 17)
    - **Property 17: Total tagihan sama dengan jumlah semua item layanan**
    - **Validates: Requirements 11.1**

  - [x] 11.3 Implementasi `SendBPJSClaimJob` dan monitoring klaim
    - Job dispatch secara asinkron via Queue saat pembayaran BPJS diproses
    - Retry maksimal 3 kali jika gagal, catat ke `failed_jobs`
    - Halaman monitoring status klaim BPJS yang telah dikirimkan
    - _Requirements: 11.5, 11.6, 11.8_

- [x] 12. Implementasi Modul Farmasi
  - [x] 12.1 Buat migrasi tabel `prescriptions`, `prescription_items`, `drug_stocks`
    - Field `prescriptions`: id, visit_id, type (dokter/terjadwal/pulang), status, prescribed_by
    - Field `prescription_items`: id, prescription_id, drug_id, quantity, dosage, instructions
    - Field `drug_stocks`: id, drug_id, quantity, expiry_date, batch_number, minimum_stock
    - _Requirements: 10.1, 10.5_

  - [x] 12.2 Implementasi `PharmacyService` dengan validasi stok dan kadaluarsa
    - Method `validatePrescription(prescriptionId)`: cek stok setiap item sebelum konfirmasi
    - Method `dispenseDrug(prescriptionItemId, quantity)`: kurangi stok tepat sejumlah yang diserahkan
    - Method `checkExpiry(drugStockId)`: cek tanggal kadaluarsa, blokir pengeluaran jika sudah lewat
    - Alert stok menipis: notifikasi suara + indikator merah jika stok ≤ minimum_stock
    - Alert mendekati kadaluarsa (< 30 hari): notifikasi suara + indikator kuning
    - Alert kadaluarsa: indikator merah + blokir pengeluaran
    - Fitur SOAP farmasi untuk dokumentasi konseling obat
    - _Requirements: 10.2, 10.3, 10.4, 10.6, 10.7, 10.8, 10.9_

  - [ ]* 12.3 Tulis property test untuk validasi stok sebelum konfirmasi resep (Property 13)
    - **Property 13: Validasi resep selalu memeriksa stok sebelum konfirmasi**
    - **Validates: Requirements 10.2**

  - [ ]* 12.4 Tulis property test untuk akurasi pengurangan stok (Property 14)
    - **Property 14: Penyerahan obat selalu mengurangi stok secara akurat**
    - **Validates: Requirements 10.4**

  - [ ]* 12.5 Tulis property test untuk peringatan stok minimum (Property 15)
    - **Property 15: Obat dengan stok di bawah minimum selalu ditandai peringatan**
    - **Validates: Requirements 10.6**

  - [ ]* 12.6 Tulis property test untuk blokir obat kadaluarsa (Property 16)
    - **Property 16: Obat kadaluarsa tidak dapat dikeluarkan**
    - **Validates: Requirements 10.8**

  - [x] 12.7 Implementasi `PharmacyController` dan halaman farmasi Blade
    - Daftar resep masuk dengan status validasi
    - Tampilan stok obat dengan indikator visual peringatan
    - _Requirements: 10.1_

- [x] 13. Implementasi Modul Laboratorium dan Radiologi
  - [x] 13.1 Buat migrasi tabel lab dan radiologi, implementasi `LabService` dan `RadiologyService`
    - Tabel permintaan lab: id, visit_id, examination_type_id, status, requested_by, sample_taken_at, sample_taken_by
    - Tabel hasil lab: id, lab_request_id, result_data, created_by
    - Tabel permintaan radiologi: id, visit_id, examination_type_id, status, requested_by
    - Tabel hasil radiologi: id, radiology_request_id, result_notes, file_path, created_by
    - _Requirements: 8.1–8.5, 9.1–9.4_

  - [x] 13.2 Implementasi Event `LabResultReady` dan `RadiologyResultReady`
    - Event broadcast notifikasi ke dokter yang meminta pemeriksaan via WebSocket
    - _Requirements: 8.5, 9.4_

- [x] 14. Checkpoint Fase 2 — Pastikan semua test Fase 2 lulus
  - Pastikan semua test lulus, tanyakan kepada pengguna jika ada pertanyaan.


---

## Fase 3: SATUSEHAT, Aplicare, dan Pendaftaran Online

- [x] 15. Implementasi integrasi SATUSEHAT (FHIR)
  - [x] 15.1 Implementasi `SatuSehatService` dengan FHIR Resource mapping
    - Method `syncPatient(patientId)`: kirim FHIR Resource Patient
    - Method `sendEncounter(visitId)`: kirim FHIR Resource Encounter (dispatch via Queue)
    - Method `sendCondition(diagnosisId)`: kirim FHIR Resource Condition
    - Method `sendObservation(data)`: kirim FHIR Resource Observation
    - Method `sendMedication(prescriptionId)`: kirim FHIR Resource Medication
    - Mapping kode internal ke kode standar FHIR
    - Resolusi mode testing: gunakan `MockApiService` atau URL sandbox
    - _Requirements: 14.1–14.9_

  - [x] 15.2 Implementasi validasi struktur FHIR sebelum pengiriman
    - Validasi struktur setiap FHIR Resource sebelum HTTP request
    - Jika tidak valid: tolak pengiriman, catat error ke log, tidak ada request ke API SATUSEHAT
    - _Requirements: 14.8_

  - [ ]* 15.3 Tulis property test untuk validasi FHIR Resource (Property 19)
    - **Property 19: FHIR Resource invalid tidak dikirim ke SATUSEHAT**
    - **Validates: Requirements 14.8**

  - [x] 15.4 Implementasi `SyncSatuSehatJob` dengan retry policy
    - Job dispatch asinkron via Queue setelah encounter selesai
    - Retry maksimal 3 kali, catat kegagalan ke log
    - _Requirements: 14.2, 14.7_

- [x] 16. Implementasi integrasi Aplicare
  - [x] 16.1 Implementasi `AplicareService` dan `SyncAplicareJob`
    - Method `updateBedAvailability(bedId, status)`: kirim pembaruan status bed ke Aplicare
    - Method `syncRoomsAndBeds()`: sinkronisasi data kamar/bed dari Aplicare ke RME secara berkala
    - Job `SyncAplicareJob`: proses asinkron via Queue, retry sesuai kebijakan yang dikonfigurasi
    - Catat kegagalan ke log, retry maksimal 3 kali dengan interval 5 menit
    - Resolusi mode testing: gunakan `MockApiService` atau URL sandbox
    - _Requirements: 15.1–15.5_

- [x] 17. Implementasi integrasi Kemenkes (Pelaporan RL)
  - [x] 17.1 Implementasi `KemenkesService` dan `SendKemenkesReportJob`
    - Method `generateRL(period)`: hasilkan laporan RL berdasarkan data kunjungan dan pelayanan
    - Method `sendReport(reportData)`: kirim laporan ke sistem Kemenkes
    - Validasi kelengkapan data laporan sebelum pengiriman
    - Mekanisme pengiriman ulang manual jika pengiriman gagal
    - _Requirements: 16.1–16.4_

- [x] 18. Implementasi Berkas Digital dan Klaim BPJS
  - [x] 18.1 Implementasi repositori berkas digital dan fitur klaim
    - Repositori berkas digital per pasien: tampilkan semua dokumen terkait kunjungan
    - Fitur pembuatan draft klaim BPJS berdasarkan data kunjungan, diagnosa, tindakan, SEP
    - Fitur bundling dokumen klaim untuk diekspor sebagai satu paket
    - Ekspor dokumen dalam format PDF menggunakan DomPDF
    - _Requirements: 12.1–12.4_

- [x] 19. Implementasi Pendaftaran Online
  - [x] 19.1 Buat `OnlineRegistrationController` dan halaman pendaftaran online
    - Form pendaftaran online: nama, tanggal lahir, NIK, telepon, jenis penjamin, pilih poli/dokter/jadwal
    - Tampilkan daftar poli, dokter, dan jadwal praktik yang tersedia
    - Validasi ketersediaan slot antrian sebelum konfirmasi
    - Generate nomor antrian otomatis setelah pendaftaran berhasil
    - Field nomor polis dan nama asuransi untuk penjamin Asuransi
    - Tampilkan jadwal alternatif jika slot penuh
    - _Requirements: 18.1–18.8_

  - [x] 19.2 Implementasi `NotificationService` untuk konfirmasi pendaftaran online
    - Kirim konfirmasi beserta nomor antrian via WhatsApp/SMS/Email sesuai preferensi pasien
    - _Requirements: 18.7_

  - [x] 19.3 Integrasi validasi BPJS ke pendaftaran online
    - Panggil `BPJSService::validatePeserta()` untuk penjamin BPJS sebelum konfirmasi
    - _Requirements: 18.5_

- [x] 20. Implementasi Manajemen Jadwal Dokter
  - [x] 20.1 Implementasi CRUD jadwal praktik dokter
    - Manajemen data dokter: nama, spesialis, sub spesialis, nomor SIP, kontak
    - Manajemen data petugas non-dokter: peran dan unit kerja
    - Jadwal praktik: hari, jam mulai, jam selesai, kuota pasien per sesi
    - Update ketersediaan slot pendaftaran online secara langsung saat jadwal diubah
    - Tandai jadwal sebagai penuh jika kuota terpenuhi
    - _Requirements: 19.1–19.5_

- [x] 21. Checkpoint Fase 3 — Pastikan semua test Fase 3 lulus
  - Pastikan semua test lulus, tanyakan kepada pengguna jika ada pertanyaan.


---

## Fase 4: Reporting, Dashboard, dan Finalisasi

- [x] 22. Implementasi Modul Laporan
  - [x] 22.1 Implementasi `ReportService` dan `ReportController`
    - Laporan kunjungan pasien: filter berdasarkan periode, poli, dokter, jenis penjamin
    - Laporan penyakit berdasarkan kode ICD-10: filter berdasarkan periode
    - Laporan keuangan: pendapatan tunai dan klaim BPJS per periode
    - _Requirements: 17.1, 17.2, 17.3_

  - [x] 22.2 Implementasi ekspor laporan PDF dan Excel
    - Ekspor PDF menggunakan DomPDF, ekspor Excel menggunakan Laravel Excel
    - _Requirements: 17.4_

  - [x] 22.3 Implementasi `ExportReportJob` untuk ekspor asinkron
    - Jika data > 1000 baris: dispatch `ExportReportJob` ke Queue, kembalikan HTTP response segera
    - Kirim notifikasi ke pengguna ketika file siap diunduh
    - _Requirements: 17.5_

  - [ ]* 22.4 Tulis property test untuk ekspor laporan asinkron (Property 20)
    - **Property 20: Ekspor laporan besar selalu diproses secara asinkron**
    - **Validates: Requirements 17.5**

- [x] 23. Implementasi dashboard per peran
  - [x] 23.1 Buat dashboard Blade untuk setiap peran pengguna
    - Dashboard Admin: statistik sistem, failed jobs, manajemen user dan permission
    - Dashboard Dokter: daftar pasien hari ini, antrian poli, notifikasi hasil lab/radiologi
    - Dashboard Perawat: antrian poli, status bed rawat inap
    - Dashboard Farmasi: daftar resep masuk, alert stok menipis dan kadaluarsa
    - Dashboard Kasir: daftar tagihan pending, monitoring klaim BPJS
    - Dashboard Petugas Pendaftaran: form pendaftaran cepat, antrian hari ini
    - Dashboard Manajemen: ringkasan laporan kunjungan dan keuangan
    - _Requirements: 1.3_

- [x] 24. Implementasi keamanan dan penguatan sistem
  - [x] 24.1 Implementasi kebijakan password dan keamanan tambahan
    - Validasi password minimum 8 karakter: huruf besar, huruf kecil, angka, karakter khusus
    - Enforce HTTPS di semua route via middleware
    - Rate limiting pada endpoint login (maksimal 5 request per menit per IP)
    - _Requirements: 20.2, 20.5_

  - [x] 24.2 Implementasi circuit breaker untuk API BPJS dan SATUSEHAT
    - Setelah 10 kegagalan dalam 5 menit: circuit "terbuka" selama 30 menit
    - Selama circuit terbuka: request langsung gagal tanpa mencoba API
    - Setelah 30 menit: circuit "setengah terbuka" untuk mencoba satu request
    - _Requirements: 13.6_

  - [x] 24.3 Implementasi dashboard monitoring failed jobs untuk Admin
    - Tampilkan daftar failed jobs dengan detail error
    - Tombol retry manual per job
    - Alert otomatis ke admin jika jumlah failed jobs melebihi threshold
    - _Requirements: 11.6_

- [x] 25. Integrasi akhir dan wiring semua komponen
  - [x] 25.1 Hubungkan semua modul ke dalam alur pelayanan pasien end-to-end
    - Alur lengkap: Pendaftaran → Antrian → Pemeriksaan → Lab/Radiologi → Farmasi → Kasir → Selesai
    - Pastikan status pasien diperbarui di setiap tahap dan tercermin di antrian real-time
    - _Requirements: 4.5_

  - [x] 25.2 Implementasi navigasi dan layout Blade utama
    - Layout utama dengan sidebar navigasi yang menerapkan permission (menu hidden jika tidak ada izin)
    - Breadcrumb, notifikasi real-time (hasil lab, stok menipis), dan indikator mode API
    - _Requirements: 1.7_

  - [ ]* 25.3 Tulis integration test untuk alur pendaftaran pasien end-to-end
    - Test alur lengkap dengan mock BPJS via `MockApiService`
    - Verifikasi mode testing: tidak ada request ke API produksi
    - _Requirements: 3.1–3.10_

  - [ ]* 25.4 Tulis integration test untuk permission system
    - Verifikasi menu tersembunyi untuk user tanpa izin
    - Verifikasi Admin mendapat full akses
    - _Requirements: 1.6, 1.7_

- [x] 26. Checkpoint Final — Pastikan semua test lulus dan sistem siap
  - Pastikan semua test lulus, tanyakan kepada pengguna jika ada pertanyaan.

---

## Catatan

- Task bertanda `*` bersifat opsional dan dapat dilewati untuk MVP yang lebih cepat
- Setiap task mereferensikan requirements spesifik untuk keterlacakan
- Property test menggunakan library **Eris** (PHP) dengan minimum 100 iterasi per properti
- Format tag property test: `Feature: electronic-medical-records, Property {N}: {teks properti}`
- Semua integrasi API eksternal harus diuji dengan `MockApiService` — tidak ada request ke produksi saat testing
- Checkpoint memastikan validasi inkremental di setiap akhir fase
