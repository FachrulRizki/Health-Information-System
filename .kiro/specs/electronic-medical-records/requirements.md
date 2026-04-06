# Dokumen Requirements

## Pendahuluan

Aplikasi Rekam Medis Elektronik (RME) adalah sistem informasi kesehatan berbasis web yang dibangun menggunakan Laravel dengan tampilan frontend Laravel Blade yang modern dan user-friendly. Database menggunakan **MySQL**. Sistem ini mengelola seluruh siklus pelayanan pasien mulai dari pendaftaran, pemeriksaan, farmasi, billing, hingga pelaporan ke instansi pemerintah. Sistem terintegrasi dengan BPJS VClaim, INA-CBGs, SATUSEHAT (FHIR), Aplicare, dan Kemenkes untuk memenuhi regulasi nasional.

Sistem melayani tujuh peran pengguna: Admin, Dokter, Perawat, Farmasi, Kasir, Petugas Pendaftaran, dan Manajemen. Arsitektur menggunakan Laravel MVC dengan Service Layer, Repository Pattern, dan Queue untuk integrasi asinkron.

---

## Glosarium

- **RME**: Rekam Medis Elektronik — sistem digital pengelola data medis pasien
- **BPJS**: Badan Penyelenggara Jaminan Sosial Kesehatan
- **SEP**: Surat Eligibilitas Peserta — dokumen yang diterbitkan untuk peserta BPJS
- **SATUSEHAT**: Platform interoperabilitas data kesehatan nasional milik Kemenkes berbasis FHIR
- **FHIR**: Fast Healthcare Interoperability Resources — standar pertukaran data kesehatan
- **VClaim**: Layanan web service BPJS untuk verifikasi dan klaim
- **Aplicare**: Sistem manajemen tempat tidur (bed) rumah sakit
- **SOAP**: Subjective, Objective, Assessment, Plan — format dokumentasi medis
- **ICD-10**: International Classification of Diseases edisi ke-10 untuk diagnosa
- **ICD-9 CM**: International Classification of Diseases edisi ke-9 Clinical Modification untuk tindakan
- **INA-CBGs**: Indonesia Case Base Groups — sistem pengelompokan diagnosis untuk klaim BPJS rawat inap
- **SKDP**: Surat Kontrol Dalam Poli — surat rujukan kontrol ulang pasien BPJS
- **DPJP**: Dokter Penanggung Jawab Pelayanan
- **NoRM**: Nomor Rekam Medis — identifikasi unik pasien
- **NoRawat**: Nomor Rawat — identifikasi unik kunjungan pasien
- **RL**: Laporan Rumah Sakit — pelaporan data ke Kemenkes
- **IGD**: Instalasi Gawat Darurat
- **Queue**: Antrian proses asinkron menggunakan Redis
- **Audit_Trail**: Catatan perubahan data yang mencatat siapa, kapan, dan apa yang diubah
- **System**: Sistem RME secara keseluruhan
- **Auth_Module**: Modul autentikasi dan otorisasi pengguna
- **Registration_Module**: Modul pendaftaran pasien
- **Queue_Module**: Modul manajemen antrian
- **RME_Module**: Modul inti rekam medis
- **Pharmacy_Module**: Modul farmasi dan manajemen obat
- **Billing_Module**: Modul billing dan kasir
- **Inpatient_Module**: Modul rawat inap dan manajemen bed
- **Lab_Module**: Modul laboratorium
- **Radiology_Module**: Modul radiologi
- **Report_Module**: Modul laporan
- **BPJS_Service**: Service layer integrasi BPJS VClaim
- **SATUSEHAT_Service**: Service layer integrasi SATUSEHAT FHIR
- **Aplicare_Service**: Service layer integrasi Aplicare
- **Kemenkes_Service**: Service layer integrasi Kemenkes
- **Notification_Service**: Service pengiriman notifikasi (WhatsApp/SMS/Email)
- **Online_Registration_Module**: Modul pendaftaran pasien secara online
- **API_Settings_Module**: Modul konfigurasi dan manajemen pengaturan koneksi API eksternal

---

## Requirements

### Requirement 1: Autentikasi dan Otorisasi Pengguna

**User Story:** Sebagai pengguna sistem, saya ingin dapat masuk ke sistem dengan kredensial yang aman dan hanya mengakses fitur sesuai peran saya, sehingga keamanan data pasien terjaga.

#### Acceptance Criteria

1. THE Auth_Module SHALL mengautentikasi pengguna menggunakan kombinasi username dan password dengan mekanisme session atau JWT
2. THE System SHALL menyediakan akun default untuk setiap peran pengguna (Admin, Dokter, Perawat, Farmasi, Kasir, Petugas Pendaftaran, Manajemen) yang dapat digunakan untuk pengujian setelah instalasi
3. WHEN pengguna berhasil login, THE Auth_Module SHALL membuat sesi aktif dan mengarahkan pengguna ke dashboard sesuai perannya
4. IF kredensial yang dimasukkan tidak valid, THEN THE Auth_Module SHALL menampilkan pesan kesalahan dan menolak akses
5. IF pengguna gagal login sebanyak 5 kali berturut-turut, THEN THE Auth_Module SHALL mengunci akun selama 15 menit
6. THE Auth_Module SHALL menerapkan sistem permission granular per menu/fitur — Admin memiliki full akses ke semua fitur, sedangkan hak akses untuk role lainnya dikonfigurasi oleh Admin melalui menu Manajemen User di dalam aplikasi
7. WHEN pengguna tidak memiliki izin akses ke suatu menu atau fitur, THE System SHALL menyembunyikan menu tersebut sepenuhnya (hidden) — bukan menonaktifkan (disabled)
8. WHEN sesi pengguna tidak aktif selama 30 menit, THE Auth_Module SHALL mengakhiri sesi dan mengarahkan ke halaman login
9. THE Audit_Trail SHALL mencatat setiap aktivitas login, logout, dan perubahan data beserta identitas pengguna dan waktu kejadian

---

### Requirement 2: Manajemen Master Data

**User Story:** Sebagai Admin, saya ingin mengelola seluruh data referensi sistem, sehingga data yang digunakan di seluruh modul konsisten dan akurat.

#### Acceptance Criteria

1. THE System SHALL menyediakan manajemen master data untuk: Pasien, Poli, Sub Poli, Dokter, Spesialis, Sub Spesialis, Tindakan, Tarif Tindakan, Diagnosa ICD-10, Diagnosa ICD-9 CM, Obat, Satuan Obat, Kategori Obat, Supplier, Kamar, Bed, Kelas Kamar, dan Jenis Pemeriksaan Penunjang
2. WHEN Admin menambahkan data master baru, THE System SHALL memvalidasi kelengkapan dan keunikan data sebelum menyimpan
3. IF data master yang akan dihapus masih digunakan oleh data transaksi aktif, THEN THE System SHALL menolak penghapusan dan menampilkan pesan informasi
4. THE System SHALL menyediakan fitur pencarian dan filter pada setiap halaman master data
5. WHEN data master diubah, THE Audit_Trail SHALL mencatat perubahan beserta nilai sebelum dan sesudah perubahan
6. THE System SHALL menyediakan data awal (seed data) untuk seluruh tabel master data sehingga sistem dapat langsung digunakan untuk pengujian setelah instalasi
7. THE System SHALL menyediakan submenu "Setting API" di dalam menu Master Data sebagai titik konfigurasi terpusat untuk semua integrasi API eksternal

---

### Requirement 3: Pendaftaran Pasien

**User Story:** Sebagai Petugas Pendaftaran, saya ingin mendaftarkan pasien baru maupun lama dengan cepat dan akurat, sehingga proses pelayanan dapat dimulai tanpa hambatan.

#### Acceptance Criteria

1. THE Registration_Module SHALL menyediakan formulir registrasi pasien baru dengan field: nama lengkap, tanggal lahir, jenis kelamin, alamat, nomor identitas (NIK), nomor telepon, dan jenis penjamin
2. THE Registration_Module SHALL mendukung tiga jenis penjamin: Umum (bayar mandiri), BPJS, dan Asuransi swasta
3. THE Registration_Module SHALL menghasilkan NoRM unik secara otomatis untuk setiap pasien baru
4. WHEN Petugas Pendaftaran mencari pasien lama, THE Registration_Module SHALL menampilkan hasil pencarian berdasarkan nama, NoRM, atau NIK dalam waktu kurang dari 2 detik
5. WHEN pasien memilih jenis penjamin BPJS, THE BPJS_Service SHALL memvalidasi keaktifan peserta melalui API VClaim sebelum pendaftaran diselesaikan
6. IF validasi BPJS mengembalikan status peserta tidak aktif, THEN THE Registration_Module SHALL menampilkan informasi status dan meminta konfirmasi petugas untuk melanjutkan sebagai pasien umum
7. WHEN pendaftaran pasien BPJS berhasil divalidasi, THE BPJS_Service SHALL menghasilkan SEP melalui endpoint VClaim /SEP/insert
8. WHEN pasien memilih jenis penjamin Asuransi, THE Registration_Module SHALL menyediakan field untuk nomor polis dan nama perusahaan asuransi
9. THE Registration_Module SHALL menghasilkan NoRawat unik untuk setiap kunjungan pasien
10. WHEN pendaftaran selesai, THE Queue_Module SHALL secara otomatis memasukkan pasien ke antrian poli yang dipilih

---

### Requirement 4: Manajemen Antrian dan Poli

**User Story:** Sebagai Petugas Pendaftaran dan Perawat, saya ingin memantau dan mengelola antrian pasien secara real-time, sehingga alur pelayanan berjalan tertib dan efisien.

#### Acceptance Criteria

1. THE Queue_Module SHALL menampilkan daftar antrian per poli secara real-time menggunakan WebSocket
2. WHEN status pasien diperbarui (menunggu, dipanggil, dalam pemeriksaan, selesai), THE Queue_Module SHALL memperbarui tampilan antrian secara langsung tanpa perlu refresh halaman
3. THE Queue_Module SHALL menampilkan informasi antrian pada display publik yang dapat diakses tanpa login
4. WHEN pasien dipanggil, THE Queue_Module SHALL menampilkan nama dan nomor antrian pasien pada display antrian
5. THE Queue_Module SHALL melacak status pasien di setiap tahap pelayanan: Pendaftaran → Antrian → Pemeriksaan → Farmasi → Kasir → Selesai

---

### Requirement 5: Rekam Medis Rawat Jalan

**User Story:** Sebagai Dokter, saya ingin mendokumentasikan pemeriksaan pasien rawat jalan secara lengkap dan terstruktur, sehingga rekam medis pasien akurat dan dapat digunakan sebagai referensi klinis.

#### Acceptance Criteria

1. THE RME_Module SHALL menyediakan formulir SOAP (Subjective, Objective, Assessment, Plan) untuk setiap kunjungan rawat jalan
2. THE RME_Module SHALL menyediakan pencarian dan pemilihan diagnosa berdasarkan kode dan deskripsi ICD-10
3. THE RME_Module SHALL menyediakan pencarian dan pemilihan tindakan berdasarkan kode dan deskripsi ICD-9 CM
4. THE RME_Module SHALL menampilkan riwayat kunjungan dan rekam medis sebelumnya milik pasien yang sedang diperiksa
5. WHEN Dokter menyimpan rekam medis, THE RME_Module SHALL memvalidasi bahwa minimal satu diagnosa ICD-10 telah dipilih
6. THE RME_Module SHALL menyediakan formulir penilaian keperawatan umum, formulir medis umum, dan formulir asesmen awal pasien
7. THE RME_Module SHALL menyediakan formulir khusus IGD: asesmen awal medis IGD, penilaian awal keperawatan IGD, dan observasi IGD
8. THE RME_Module SHALL menyediakan fitur permintaan penunjang untuk: Patologi Klinis, Patologi Anatomi, Radiologi, EKG, USG, dan CTG
9. THE RME_Module SHALL menyediakan daftar tindakan dokter dan petugas dalam bentuk checkbox yang bersumber dari master tindakan
10. THE RME_Module SHALL menyediakan fitur unggah dokumen dan gambar pendukung rekam medis
11. THE RME_Module SHALL menyediakan formulir resume kunjungan rawat jalan
12. WHEN Dokter membuat SKDP, THE RME_Module SHALL memvalidasi bahwa SEP sudah terbit, tanggal rencana kontrol telah diisi, spesialis/sub spesialis telah dipilih, DPJP telah dipilih, dan rujukan aktif terverifikasi melalui BPJS VClaim

---

### Requirement 6: Rekam Medis Rawat Inap

**User Story:** Sebagai Dokter dan Perawat, saya ingin mendokumentasikan pelayanan pasien rawat inap secara komprehensif, sehingga kontinuitas perawatan terjaga selama pasien dirawat.

#### Acceptance Criteria

1. THE Inpatient_Module SHALL menampilkan daftar pasien rawat inap dengan informasi: NoRawat, NoRM, Kode Dokter, Dokter PJ, Nama Poli, Nama Pasien, Nama Kamar, Jenis Bayar, dan Status Pulang
2. THE RME_Module SHALL menyediakan formulir SOAP rawat inap beserta pemilihan diagnosa ICD-10 dan tindakan ICD-9 CM
3. THE RME_Module SHALL menyediakan formulir penilaian keperawatan rawat inap, penilaian medis rawat inap, dan asesmen awal rawat inap
4. THE RME_Module SHALL menyediakan tiga jenis resep untuk rawat inap: resep dokter, obat terjadwal, dan resep pulang
5. THE RME_Module SHALL menyediakan fitur permintaan penunjang untuk pasien rawat inap
6. THE RME_Module SHALL menyediakan formulir resume pulang pasien rawat inap
7. WHEN Dokter membuat SKDP untuk pasien rawat inap, THE RME_Module SHALL menerapkan validasi yang sama dengan rawat jalan: SEP terbit, tanggal kontrol, spesialis, DPJP, dan rujukan aktif

---

### Requirement 7: Manajemen Bed dan Rawat Inap

**User Story:** Sebagai Perawat dan Admin, saya ingin memantau ketersediaan kamar dan bed secara real-time, sehingga penempatan pasien rawat inap dapat dilakukan dengan cepat dan tepat.

#### Acceptance Criteria

1. THE Inpatient_Module SHALL menampilkan status setiap bed (tersedia, terisi, dalam perawatan, tidak aktif) secara real-time
2. WHEN status bed berubah, THE Inpatient_Module SHALL memperbarui tampilan peta bed tanpa perlu refresh halaman
3. WHEN pasien ditempatkan di bed, THE Aplicare_Service SHALL mengirimkan pembaruan ketersediaan bed ke sistem Aplicare secara asinkron melalui Queue
4. IF sinkronisasi ke Aplicare gagal, THEN THE Aplicare_Service SHALL mencatat kegagalan ke log dan melakukan retry maksimal 3 kali dengan interval 5 menit
5. THE Inpatient_Module SHALL mengelola data kamar berdasarkan kelas (Kelas 1, Kelas 2, Kelas 3, VIP)

---

### Requirement 8: Modul Laboratorium

**User Story:** Sebagai Petugas Laboratorium, saya ingin mengelola permintaan dan hasil pemeriksaan laboratorium, sehingga hasil lab tersedia tepat waktu untuk mendukung keputusan klinis dokter.

#### Acceptance Criteria

1. THE Lab_Module SHALL menampilkan daftar permintaan pemeriksaan laboratorium dari pasien rawat jalan dan rawat inap secara terpisah
2. THE Lab_Module SHALL menampilkan detail permintaan pemeriksaan termasuk jenis pemeriksaan yang diminta dan data pasien
3. WHEN petugas laboratorium mengambil sampel, THE Lab_Module SHALL mencatat waktu pengambilan sampel dan identitas petugas
4. WHEN hasil pemeriksaan laboratorium diinput, THE Lab_Module SHALL menyimpan hasil dan memperbarui status permintaan menjadi selesai
5. WHEN hasil laboratorium tersimpan, THE RME_Module SHALL menampilkan notifikasi kepada Dokter yang meminta pemeriksaan

---

### Requirement 9: Modul Radiologi

**User Story:** Sebagai Petugas Radiologi, saya ingin mengelola permintaan dan hasil pemeriksaan radiologi, sehingga hasil radiologi tersedia untuk mendukung diagnosis dokter.

#### Acceptance Criteria

1. THE Radiology_Module SHALL menampilkan daftar permintaan pemeriksaan radiologi dari pasien rawat jalan dan rawat inap
2. THE Radiology_Module SHALL menampilkan detail permintaan pemeriksaan radiologi beserta data pasien
3. WHEN petugas radiologi menginput hasil pemeriksaan, THE Radiology_Module SHALL menyimpan hasil beserta kemampuan unggah gambar/dokumen hasil radiologi
4. WHEN hasil radiologi tersimpan, THE RME_Module SHALL menampilkan notifikasi kepada Dokter yang meminta pemeriksaan

---

### Requirement 10: Modul Farmasi

**User Story:** Sebagai Petugas Farmasi, saya ingin mengelola resep, stok obat, dan pengeluaran obat secara akurat, sehingga pelayanan obat kepada pasien tepat dan stok terkontrol.

#### Acceptance Criteria

1. THE Pharmacy_Module SHALL menampilkan daftar resep yang masuk dari dokter beserta status validasi
2. WHEN Petugas Farmasi memvalidasi resep, THE Pharmacy_Module SHALL memeriksa ketersediaan stok obat sebelum konfirmasi
3. IF stok obat tidak mencukupi untuk memenuhi resep, THEN THE Pharmacy_Module SHALL menampilkan peringatan dan jumlah stok yang tersedia
4. WHEN obat diserahkan kepada pasien, THE Pharmacy_Module SHALL mengurangi stok obat secara otomatis sesuai jumlah yang diserahkan
5. THE Pharmacy_Module SHALL mengelola data stok obat termasuk: jumlah stok, tanggal kadaluarsa, dan nomor batch
6. WHEN stok obat mencapai batas minimum yang ditentukan, THE Pharmacy_Module SHALL menampilkan peringatan stok menipis kepada Petugas Farmasi disertai notifikasi suara dan indikator visual berwarna merah pada antarmuka
7. WHEN tanggal kadaluarsa obat kurang dari 30 hari, THE Pharmacy_Module SHALL menampilkan peringatan obat mendekati kadaluarsa disertai notifikasi suara dan indikator visual berwarna kuning pada antarmuka
8. WHEN tanggal kadaluarsa obat telah terlewati, THE Pharmacy_Module SHALL menampilkan peringatan obat kadaluarsa dengan indikator visual berwarna merah dan mencegah pengeluaran obat tersebut kepada pasien
9. THE Pharmacy_Module SHALL menyediakan fitur SOAP farmasi untuk dokumentasi konseling obat

---

### Requirement 11: Billing dan Kasir

**User Story:** Sebagai Kasir, saya ingin mengelola tagihan dan pembayaran pasien secara akurat, sehingga proses keuangan fasilitas kesehatan berjalan tertib.

#### Acceptance Criteria

1. THE Billing_Module SHALL menghasilkan tagihan pasien secara otomatis berdasarkan tindakan, obat, dan layanan yang diterima selama kunjungan
2. THE Billing_Module SHALL mendukung tiga metode pembayaran: Umum (tunai/mandiri), BPJS, dan Asuransi swasta
3. WHEN pembayaran tunai dilakukan, THE Billing_Module SHALL mencatat transaksi dan menghasilkan bukti pembayaran
4. WHEN pembayaran Asuransi diproses, THE Billing_Module SHALL mencatat nomor polis, nama perusahaan asuransi, dan menghasilkan bukti tagihan untuk klaim ke perusahaan asuransi
5. WHEN pembayaran BPJS diproses, THE BPJS_Service SHALL mengirimkan data klaim ke sistem BPJS VClaim secara asinkron melalui Queue
6. IF pengiriman klaim BPJS gagal, THEN THE BPJS_Service SHALL mencatat kegagalan ke log dan melakukan retry maksimal 3 kali
7. THE Billing_Module SHALL menampilkan detail rincian tagihan per item layanan kepada Kasir dan pasien
8. THE Billing_Module SHALL menyediakan fitur monitoring status klaim BPJS yang telah dikirimkan

---

### Requirement 12: Berkas Digital dan Klaim BPJS

**User Story:** Sebagai Petugas Klaim, saya ingin mengelola berkas digital pasien dan menyiapkan bundling dokumen klaim BPJS, sehingga proses klaim berjalan lancar dan lengkap.

#### Acceptance Criteria

1. THE System SHALL menyediakan repositori berkas digital per pasien yang menampilkan seluruh dokumen terkait kunjungan
2. THE System SHALL menyediakan fitur pembuatan draft klaim BPJS berdasarkan data kunjungan, diagnosa, tindakan, dan SEP
3. THE System SHALL menyediakan fitur bundling dokumen klaim untuk diekspor sebagai satu paket berkas
4. THE System SHALL menyediakan fitur ekspor dokumen dalam format PDF

---

### Requirement 13: Integrasi BPJS VClaim

**User Story:** Sebagai sistem, saya ingin terintegrasi dengan BPJS VClaim untuk memvalidasi peserta dan mengelola SEP, sehingga pelayanan pasien BPJS sesuai prosedur nasional.

#### Acceptance Criteria

1. THE BPJS_Service SHALL menyediakan fungsi pengecekan peserta melalui endpoint VClaim /Peserta/nokartu/
2. WHEN data peserta berhasil diambil dari VClaim, THE BPJS_Service SHALL menyimpan data peserta ke cache selama 1 jam untuk mengurangi beban API
3. THE BPJS_Service SHALL menyediakan fungsi insert SEP melalui endpoint VClaim /SEP/insert
4. THE BPJS_Service SHALL menyediakan fungsi update SEP melalui endpoint VClaim yang sesuai
5. THE BPJS_Service SHALL menyediakan fungsi monitoring klaim BPJS
6. WHEN panggilan ke API VClaim gagal karena timeout atau error jaringan, THE BPJS_Service SHALL melakukan retry maksimal 3 kali dengan interval eksponensial
7. IF semua retry ke API VClaim gagal, THEN THE BPJS_Service SHALL mencatat kegagalan ke log sistem dan menampilkan pesan error yang informatif kepada pengguna
8. THE BPJS_Service SHALL memvalidasi seluruh data yang akan dikirim ke VClaim sebelum pengiriman dilakukan
9. THE BPJS_Service SHALL mengenkripsi kredensial API BPJS dan tidak menyimpannya dalam bentuk plaintext
10. WHEN mode testing aktif pada konfigurasi BPJS VClaim, THE BPJS_Service SHALL menggunakan mock response atau URL sandbox — tidak mengirimkan request ke API produksi BPJS

---

### Requirement 14: Integrasi SATUSEHAT (FHIR)

**User Story:** Sebagai sistem, saya ingin terintegrasi dengan platform SATUSEHAT Kemenkes menggunakan standar FHIR, sehingga data kesehatan pasien tersinkronisasi dengan platform nasional.

#### Acceptance Criteria

1. THE SATUSEHAT_Service SHALL menyinkronisasi data pasien ke SATUSEHAT menggunakan FHIR Resource Patient
2. WHEN encounter pasien selesai, THE SATUSEHAT_Service SHALL mengirimkan data encounter menggunakan FHIR Resource Encounter secara asinkron melalui Queue
3. THE SATUSEHAT_Service SHALL mengirimkan data diagnosa menggunakan FHIR Resource Condition
4. THE SATUSEHAT_Service SHALL mengirimkan data observasi klinis menggunakan FHIR Resource Observation
5. THE SATUSEHAT_Service SHALL mengirimkan data resep dan obat menggunakan FHIR Resource Medication
6. THE SATUSEHAT_Service SHALL menyediakan mapping antara kode internal sistem dengan kode standar FHIR
7. IF pengiriman data ke SATUSEHAT gagal, THEN THE SATUSEHAT_Service SHALL mencatat kegagalan ke log dan melakukan retry maksimal 3 kali
8. THE SATUSEHAT_Service SHALL memvalidasi struktur FHIR Resource sebelum pengiriman ke platform SATUSEHAT
9. WHEN mode testing aktif pada konfigurasi SATUSEHAT, THE SATUSEHAT_Service SHALL menggunakan mock response atau URL sandbox — tidak mengirimkan request ke API produksi SATUSEHAT

---

### Requirement 15: Integrasi Aplicare

**User Story:** Sebagai sistem, saya ingin terintegrasi dengan Aplicare untuk sinkronisasi data bed, sehingga ketersediaan tempat tidur terpantau secara terpusat.

#### Acceptance Criteria

1. THE Aplicare_Service SHALL mengirimkan pembaruan status ketersediaan bed ke sistem Aplicare setiap kali terjadi perubahan status bed
2. THE Aplicare_Service SHALL menyinkronisasi data kamar dan bed dari Aplicare ke sistem RME secara berkala
3. WHEN sinkronisasi Aplicare gagal, THE Aplicare_Service SHALL mencatat kegagalan ke log dan melakukan retry sesuai kebijakan retry yang dikonfigurasi
4. THE Aplicare_Service SHALL memproses seluruh komunikasi dengan Aplicare secara asinkron melalui Queue
5. WHEN mode testing aktif pada konfigurasi Aplicare, THE Aplicare_Service SHALL menggunakan mock response atau URL sandbox — tidak mengirimkan request ke API produksi Aplicare

---

### Requirement 16: Integrasi Kemenkes (Pelaporan RL)

**User Story:** Sebagai Manajemen, saya ingin sistem dapat menghasilkan dan mengirimkan laporan RL ke Kemenkes, sehingga kewajiban pelaporan data nasional terpenuhi.

#### Acceptance Criteria

1. THE Kemenkes_Service SHALL menghasilkan laporan RL (Laporan Rumah Sakit) berdasarkan data kunjungan dan pelayanan dalam periode yang ditentukan
2. THE Kemenkes_Service SHALL mengirimkan data laporan ke sistem integrasi data nasional Kemenkes
3. IF pengiriman laporan ke Kemenkes gagal, THEN THE Kemenkes_Service SHALL mencatat kegagalan dan menyediakan mekanisme pengiriman ulang manual
4. THE Kemenkes_Service SHALL memvalidasi kelengkapan data laporan sebelum pengiriman

---

### Requirement 17: Modul Laporan

**User Story:** Sebagai Manajemen, saya ingin mengakses berbagai laporan operasional dan keuangan, sehingga pengambilan keputusan berbasis data dapat dilakukan secara efektif.

#### Acceptance Criteria

1. THE Report_Module SHALL menyediakan laporan kunjungan pasien yang dapat difilter berdasarkan periode, poli, dokter, dan jenis penjamin
2. THE Report_Module SHALL menyediakan laporan penyakit berdasarkan kode ICD-10 yang dapat difilter berdasarkan periode
3. THE Report_Module SHALL menyediakan laporan keuangan yang mencakup pendapatan tunai dan klaim BPJS per periode
4. THE Report_Module SHALL menyediakan fitur ekspor laporan dalam format PDF dan Excel
5. WHEN pengguna mengekspor laporan dengan data lebih dari 1000 baris, THE Report_Module SHALL memproses ekspor secara asinkron melalui Queue dan mengirimkan notifikasi ketika file siap diunduh

---

### Requirement 18: Pendaftaran Online

**User Story:** Sebagai pasien, saya ingin mendaftar kunjungan secara online melalui web atau mobile, sehingga saya dapat memilih jadwal dan menghindari antrean panjang di fasilitas kesehatan.

#### Acceptance Criteria

1. THE Online_Registration_Module SHALL menyediakan formulir pendaftaran online yang dapat diakses melalui web browser tanpa perlu instalasi aplikasi
2. THE Online_Registration_Module SHALL menampilkan daftar poli, dokter, dan jadwal praktik yang tersedia untuk dipilih pasien
3. WHEN pasien memilih jadwal, THE Online_Registration_Module SHALL memvalidasi ketersediaan slot antrian sebelum konfirmasi pendaftaran
4. WHEN pendaftaran online berhasil, THE Online_Registration_Module SHALL menghasilkan nomor antrian secara otomatis
5. WHEN pasien mendaftar dengan jenis penjamin BPJS, THE BPJS_Service SHALL memvalidasi keaktifan peserta sebelum pendaftaran dikonfirmasi
6. WHEN pasien mendaftar dengan jenis penjamin Asuransi, THE Online_Registration_Module SHALL menyediakan field untuk nomor polis dan nama perusahaan asuransi
7. WHEN pendaftaran online berhasil dikonfirmasi, THE Notification_Service SHALL mengirimkan konfirmasi beserta nomor antrian kepada pasien melalui WhatsApp, SMS, atau Email sesuai preferensi pasien
8. IF slot antrian pada jadwal yang dipilih sudah penuh, THEN THE Online_Registration_Module SHALL menampilkan informasi dan menawarkan jadwal alternatif yang tersedia

---

### Requirement 19: Manajemen Jadwal Dokter dan Pegawai

**User Story:** Sebagai Admin, saya ingin mengelola data dokter, petugas, dan jadwal praktik, sehingga informasi ketersediaan dokter akurat untuk pendaftaran pasien.

#### Acceptance Criteria

1. THE System SHALL menyediakan manajemen data dokter termasuk: nama, spesialis, sub spesialis, nomor SIP, dan informasi kontak
2. THE System SHALL menyediakan manajemen data petugas non-dokter beserta peran dan unit kerja
3. THE System SHALL menyediakan manajemen jadwal praktik dokter per poli termasuk hari, jam mulai, jam selesai, dan kuota pasien per sesi
4. WHEN jadwal praktik dokter diubah, THE System SHALL memperbarui ketersediaan slot pada modul pendaftaran online secara langsung
5. WHEN kuota pasien pada jadwal tertentu telah terpenuhi, THE Online_Registration_Module SHALL menandai jadwal tersebut sebagai penuh dan tidak dapat dipilih

---

### Requirement 20: Keamanan dan Audit

**User Story:** Sebagai Admin, saya ingin sistem memiliki mekanisme keamanan yang kuat dan audit trail yang lengkap, sehingga data pasien terlindungi dan setiap akses dapat dipertanggungjawabkan.

#### Acceptance Criteria

1. THE System SHALL mengenkripsi data sensitif pasien (NIK, nomor telepon, alamat) saat disimpan di database
2. THE System SHALL mengenkripsi seluruh komunikasi antara client dan server menggunakan HTTPS/TLS
3. THE System SHALL mengenkripsi kredensial API eksternal (BPJS, SATUSEHAT, Aplicare) dan tidak menyimpannya dalam bentuk plaintext di kode sumber
4. THE Audit_Trail SHALL mencatat setiap akses, perubahan, dan penghapusan data rekam medis beserta identitas pengguna, waktu, dan alamat IP
5. THE Auth_Module SHALL menerapkan kebijakan password minimum 8 karakter yang mengandung kombinasi huruf besar, huruf kecil, angka, dan karakter khusus
6. WHEN pengguna mengakses data rekam medis pasien, THE Audit_Trail SHALL mencatat akses tersebut meskipun tidak ada perubahan data

---

### Requirement 21: Manajemen Setting API Eksternal

**User Story:** Sebagai Admin, saya ingin mengelola konfigurasi koneksi ke seluruh API eksternal melalui antarmuka sistem, sehingga pengaturan integrasi dapat diubah tanpa perlu mengubah kode sumber.

#### Acceptance Criteria

1. THE API_Settings_Module SHALL menyediakan halaman konfigurasi terpisah untuk setiap integrasi API: BPJS VClaim, INA-CBGs, SATUSEHAT, Aplicare, dan Kemenkes — dapat diakses melalui submenu "Setting API" di menu Master Data
2. THE API_Settings_Module SHALL menyediakan field konfigurasi untuk setiap API meliputi: URL endpoint, consumer key, consumer secret, mode (testing/production), dan parameter autentikasi yang relevan
3. WHEN Admin menyimpan konfigurasi API, THE API_Settings_Module SHALL mengenkripsi nilai kredensial sensitif sebelum disimpan ke database
4. THE API_Settings_Module SHALL menyediakan fitur uji koneksi (tombol "Test Koneksi") untuk memverifikasi bahwa konfigurasi API yang disimpan dapat terhubung dengan sukses
5. WHEN uji koneksi dilakukan, THE API_Settings_Module SHALL menampilkan hasil uji beserta kode status respons dan pesan dari server API dalam waktu kurang dari 10 detik
6. IF uji koneksi gagal, THEN THE API_Settings_Module SHALL menampilkan pesan error yang informatif beserta saran penanganan kepada Admin
7. THE API_Settings_Module SHALL menyediakan fitur toggle aktif/nonaktif untuk setiap integrasi API tanpa menghapus konfigurasi yang tersimpan
8. WHEN konfigurasi API diubah, THE Audit_Trail SHALL mencatat perubahan beserta identitas Admin dan waktu perubahan, tanpa mencatat nilai kredensial dalam plaintext
9. WHEN mode testing dipilih untuk suatu integrasi API, THE System SHALL menggunakan mock response atau URL sandbox yang dikonfigurasi — tidak mengirimkan request ke URL produksi
10. WHEN mode production dipilih untuk suatu integrasi API, THE System SHALL menggunakan URL endpoint produksi yang dikonfigurasi untuk semua request ke API tersebut
11. THE API_Settings_Module SHALL menampilkan indikator visual yang jelas untuk membedakan mode testing dan mode production pada setiap konfigurasi API
