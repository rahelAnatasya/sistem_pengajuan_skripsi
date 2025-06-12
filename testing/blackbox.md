# Pengujian Black Box - Sistem Pengajuan Skripsi

## Deskripsi

Pengujian Black Box adalah metode pengujian perangkat lunak yang menguji fungsionalitas aplikasi tanpa mempertimbangkan struktur internal, implementasi, atau kode sumber. Pengujian ini berfokus pada masukan dan keluaran yang diharapkan berdasarkan spesifikasi kebutuhan.

## Tujuan Pengujian

1. Memverifikasi bahwa semua fitur sistem pengajuan skripsi berfungsi sesuai dengan kebutuhan
2. Memastikan validasi masukan berjalan dengan benar
3. Menguji keamanan sistem dari berbagai serangan
4. Memvalidasi alur bisnis dari ujung ke ujung
5. Memastikan pengalaman pengguna yang konsisten

## Ruang Lingkup Pengujian

Pengujian mencakup seluruh modul dalam sistem pengajuan skripsi:
- Otentikasi & Otorisasi
- Modul Pendaftaran
- Manajemen Formulir Pengajuan
- Sistem Ulasan & Persetujuan
- Fungsionalitas Pencarian & Filter
- Operasi Dashboard
- Fitur Keamanan

---

## 1. Pengujian Modul Masuk Sistem

Pengujian modul otentikasi untuk memastikan sistem masuk berfungsi dengan benar dan aman.

| ID Kasus Uji | Skenario Pengujian | Data Masukan | Hasil yang Diharapkan | Status |
|--------------|-------------------|--------------|----------------------|--------|
| TC_LOGIN_001 | Masuk mahasiswa yang valid | username: "mahasiswa1"<br>password: "password" | Dialihkan ke dashboard.php dengan session role = "mahasiswa" | LULUS |
| TC_LOGIN_002 | Masuk dosen yang valid | username: "dosen1"<br>password: "password" | Dialihkan ke dashboard.php dengan session role = "dosen" | LULUS |
| TC_LOGIN_003 | Username tidak valid | username: "invalid"<br>password: "password" | Error: "Username tidak ditemukan!" | LULUS |
| TC_LOGIN_004 | Password tidak valid | username: "mahasiswa1"<br>password: "salah" | Error: "Password salah!" | LULUS |
| TC_LOGIN_005 | Field kosong | username: ""<br>password: "" | Error validasi form | LULUS |
| TC_LOGIN_006 | Percobaan SQL injection | username: "' OR 1=1 --"<br>password: "apa saja" | Login gagal, tidak ada pelanggaran keamanan | LULUS |
| TC_LOGIN_007 | Pengguna sudah masuk | Akses login.php saat sudah masuk | Dialihkan ke dashboard.php | LULUS |

---

## 2. Pengujian Modul Pendaftaran

Pengujian pendaftaran pengguna baru untuk memastikan validasi data dan penugasan peran berfungsi dengan benar.

| ID Kasus Uji | Skenario Pengujian | Data Masukan | Hasil yang Diharapkan | Status |
|--------------|-------------------|--------------|----------------------|--------|
| TC_REG_001 | Pendaftaran mahasiswa valid | username: "mahasiswabaru"<br>email: "baru@email.com"<br>role: "mahasiswa"<br>nim: "123456" | Pesan sukses + dialihkan ke login | LULUS |
| TC_REG_002 | Pendaftaran dosen valid | username: "dosenbaru"<br>email: "dosen@email.com"<br>role: "dosen"<br>nidn: "654321" | Pesan sukses + dialihkan ke login | LULUS |
| TC_REG_003 | Username duplikat | username: "mahasiswa1" (sudah ada)<br>field lain valid | Pesan error database | LULUS |
| TC_REG_004 | Email duplikat | email: "mahasiswa1@email.com" (sudah ada)<br>field lain valid | Pesan error database | LULUS |
| TC_REG_005 | Validasi pemilihan peran | Pilih "mahasiswa" | Field NIM muncul, field NIDN tersembunyi | LULUS |
| TC_REG_006 | Validasi pemilihan peran | Pilih "dosen" | Field NIDN muncul, field NIM tersembunyi | LULUS |
| TC_REG_007 | Field wajib kosong | Biarkan username kosong | Error validasi form | LULUS |
| TC_REG_008 | Format email tidak valid | email: "emailtidakvalid" | Error validasi email HTML5 | LULUS |

---

## 3. Pengujian Formulir Pengajuan (Mahasiswa)

Pengujian formulir pengajuan judul skripsi untuk memastikan data tersimpan dengan benar dan validasi berfungsi.

| ID Kasus Uji | Skenario Pengujian | Data Masukan | Hasil yang Diharapkan | Status |
|--------------|-------------------|--------------|----------------------|--------|
| TC_PENGAJUAN_001 | Pengajuan valid | judul: "Sistem AI untuk Prediksi"<br>deskripsi: "Penelitian tentang AI"<br>bidang: "Kecerdasan Buatan"<br>dosen: ID Valid | Sukses: "Pengajuan judul berhasil dikirim!" | LULUS |
| TC_PENGAJUAN_002 | Judul kosong | judul: ""<br>field lain valid | Error validasi form | LULUS |
| TC_PENGAJUAN_003 | Deskripsi kosong | deskripsi: ""<br>field lain valid | Error validasi form | LULUS |
| TC_PENGAJUAN_004 | Bidang tidak dipilih | bidang: ""<br>field lain valid | Error validasi form | LULUS |
| TC_PENGAJUAN_005 | Dosen tidak dipilih | dosen: ""<br>field lain valid | Error validasi form | LULUS |
| TC_PENGAJUAN_006 | Validasi judul panjang | judul: 1000+ karakter | Data ditangani dengan tepat | LULUS |
| TC_PENGAJUAN_007 | Pencegahan XSS | judul: "Sistem <script>alert('xss')</script>" | XSS dicegah, data disimpan aman | LULUS |
| TC_PENGAJUAN_008 | Akses tidak sah | Akses langsung tanpa login | Dialihkan ke login.php | LULUS |
| TC_PENGAJUAN_009 | Akses berbasis peran | Dosen mengakses form | Dialihkan ke unauthorized.php | LULUS |

---

## 4. Pengujian Sistem Ulasan (Dosen)

Pengujian sistem ulasan untuk memastikan dosen dapat mengevaluasi pengajuan dengan benar.

| ID Kasus Uji | Skenario Pengujian | Data Masukan | Hasil yang Diharapkan | Status |
|--------------|-------------------|--------------|----------------------|--------|
| TC_REVIEW_001 | Menyetujui pengajuan | action: "approve"<br>komentar: "Proposal bagus" | Status berubah ke "approved", pesan sukses | LULUS |
| TC_REVIEW_002 | Menolak pengajuan | action: "reject"<br>komentar: "Perlu perbaikan" | Status berubah ke "rejected", pesan sukses | LULUS |
| TC_REVIEW_003 | Menyetujui tanpa komentar | action: "approve"<br>komentar: "" | Status berubah, komentar kosong disimpan | LULUS |
| TC_REVIEW_004 | Kontrol akses | Coba ulasan tugas dosen lain | Hanya tugas sendiri yang terlihat | LULUS |
| TC_REVIEW_005 | ID pengajuan tidak valid | pengajuan_id: "999999" | Tidak ada pembaruan, penanganan error tepat | LULUS |
| TC_REVIEW_006 | Akses tidak sah | Akses langsung tanpa login | Dialihkan ke login.php | LULUS |
| TC_REVIEW_007 | Akses berbasis peran | Mahasiswa mengakses ulasan | Dialihkan ke unauthorized.php | LULUS |
| TC_REVIEW_008 | Tampilan statistik | Akses halaman ulasan | Menampilkan jumlah pending/approved/rejected | LULUS |

---

## 5. Pengujian Pencarian dan Filter

Pengujian fitur pencarian dan filter untuk memastikan data dapat ditemukan dengan akurat.

| ID Kasus Uji | Skenario Pengujian | Data Masukan | Hasil yang Diharapkan | Status |
|--------------|-------------------|--------------|----------------------|--------|
| TC_SEARCH_001 | Filter berdasarkan status pending | status: "pending" | Hanya pengajuan pending yang ditampilkan | LULUS |
| TC_SEARCH_002 | Filter berdasarkan status disetujui | status: "approved" | Hanya pengajuan disetujui yang ditampilkan | LULUS |
| TC_SEARCH_003 | Filter berdasarkan status ditolak | status: "rejected" | Hanya pengajuan ditolak yang ditampilkan | LULUS |
| TC_SEARCH_004 | Pencarian berdasarkan judul | search: "Sistem" | Semua pengajuan yang mengandung "Sistem" di judul | LULUS |
| TC_SEARCH_005 | Pencarian berdasarkan nama mahasiswa | search: "Ahmad" | Semua pengajuan oleh mahasiswa bernama "Ahmad" | LULUS |
| TC_SEARCH_006 | Pencarian berdasarkan bidang studi | search: "Artificial" | Semua pengajuan di bidang AI | LULUS |
| TC_SEARCH_007 | Filter dan pencarian gabungan | status: "approved", search: "AI" | Pengajuan disetujui yang mengandung "AI" | LULUS |
| TC_SEARCH_008 | Hasil tidak ditemukan | search: "tidakada" | "Tidak ada pengajuan yang ditemukan" | LULUS |
| TC_SEARCH_009 | Pencegahan XSS dalam pencarian | search: "<script>" | Pencarian aman dijalankan, tidak ada XSS | LULUS |
| TC_SEARCH_010 | Pencarian tidak peka huruf | search: "sistem" vs "SISTEM" | Hasil identik untuk kedua kasus | LULUS |

---

## 6. Pengujian Fungsionalitas Dashboard

Pengujian dashboard untuk memastikan informasi ditampilkan sesuai dengan peran pengguna.

| ID Kasus Uji | Skenario Pengujian | Data Masukan | Hasil yang Diharapkan | Status |
|--------------|-------------------|--------------|----------------------|--------|
| TC_DASH_001 | Dashboard mahasiswa | Masuk sebagai mahasiswa | Menu dan statistik khusus mahasiswa | LULUS |
| TC_DASH_002 | Dashboard dosen | Masuk sebagai dosen | Menu dan statistik khusus dosen | LULUS |
| TC_DASH_003 | Akurasi statistik | Pengguna mana pun | Jumlah total/pending/approved/rejected akurat | LULUS |
| TC_DASH_004 | Aktivitas terkini | Pengguna mana pun | Pengajuan/ulasan terkini ditampilkan | LULUS |
| TC_DASH_005 | Fungsionalitas navigasi | Klik item menu | Semua tautan navigasi berfungsi dengan benar | LULUS |
| TC_DASH_006 | Desain responsif | Akses dari perangkat mobile | Tata letak menyesuaikan ukuran layar | LULUS |

---

## 7. Pengujian Fitur Keamanan

Pengujian keamanan sistem untuk memastikan perlindungan terhadap berbagai serangan.

| ID Kasus Uji | Skenario Pengujian | Data Masukan | Hasil yang Diharapkan | Status |
|--------------|-------------------|--------------|----------------------|--------|
| TC_SEC_001 | Manajemen session | Modifikasi cookie session | Akses ditolak, dialihkan ke login | LULUS |
| TC_SEC_002 | Akses URL langsung | Akses halaman terbatas tanpa login | Dialihkan ke login.php | LULUS |
| TC_SEC_003 | Perlindungan CSRF | Kirim form tanpa token CSRF | Permintaan ditolak | LULUS |
| TC_SEC_004 | Keamanan upload file | Upload jenis file berbahaya | Hanya jenis file yang diizinkan diterima | LULUS |
| TC_SEC_005 | Keamanan password | Lihat password di form | Password disembunyikan/dienkripsi | LULUS |
| TC_SEC_006 | Injeksi database | SQL injection di form | Prepared statements mencegah injeksi | LULUS |
| TC_SEC_007 | Cross-site scripting | XSS di field input | Input disanitasi, eksekusi script dicegah | LULUS |

---

## 8. Pengujian Operasi Database

Pengujian operasi database untuk memastikan integritas data dan konsistensi.

| ID Kasus Uji | Skenario Pengujian | Data Masukan | Hasil yang Diharapkan | Status |
|--------------|-------------------|--------------|----------------------|--------|
| TC_DB_001 | Penyisipan data | Pengajuan form valid | Data tersimpan dengan benar di database | LULUS |
| TC_DB_002 | Pembaruan data | Ulasan pengajuan | Status dan komentar diperbarui dengan benar | LULUS |
| TC_DB_003 | Pengambilan data | Akses dashboard | Data akurat ditampilkan dari database | LULUS |
| TC_DB_004 | Batasan foreign key | Hapus pengguna yang direferensikan | Batasan mencegah penghapusan | LULUS |
| TC_DB_005 | Integritas transaksi | Beberapa operasi bersamaan | Konsistensi data tetap terjaga | LULUS |

---

## Kesimpulan Pengujian

Berdasarkan hasil Pengujian Black Box yang telah dilakukan, sistem pengajuan skripsi telah memenuhi semua kriteria fungsional dan keamanan yang diharapkan. Semua kasus uji menunjukkan status LULUS, menandakan bahwa:

1. **Fungsionalitas Inti**: Login, pendaftaran, pengajuan, dan ulasan berfungsi dengan baik
2. **Keamanan**: Sistem terlindungi dari SQL injection, XSS, dan akses tidak sah
3. **Validasi Data**: Validasi input berjalan sesuai dengan aturan bisnis
4. **Pengalaman Pengguna**: Interface responsif dan mudah digunakan
5. **Integritas Data**: Operasi database berjalan dengan konsisten

Sistem siap untuk penerapan dengan tingkat kepercayaan tinggi bahwa semua fitur akan berfungsi sesuai dengan harapan pengguna.