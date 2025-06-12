# PENGUJIAN KEAMANAN SISTEM PENGAJUAN SKRIPSI

## **TABEL HASIL PENGUJIAN KEAMANAN**

| **ID Pengujian** | **Kategori Keamanan** | **Skenario Pengujian** | **Metode Pengujian** | **Hasil yang Diharapkan** | **Status** | **Tingkat Risiko** | **Catatan** |
|---|---|---|---|---|---|---|---|
| SEC-001 | Autentikasi | Pengujian kredensial tidak valid | Input username/password salah | Sistem menolak akses dengan pesan error yang sesuai | LULUS | RENDAH | Prepared statement mencegah SQL injection |
| SEC-002 | Otorisasi | Akses halaman tanpa login | Akses langsung ke dashboard.php | Dialihkan ke login.php | LULUS | RENDAH | Session management berfungsi dengan baik |
| SEC-003 | Kontrol Akses Berbasis Peran | Mahasiswa mengakses fungsi dosen | Role "mahasiswa" mencoba akses review.php | Dialihkan ke unauthorized.php | LULUS | RENDAH | Function checkRole() bekerja efektif |
| SEC-004 | SQL Injection | Injeksi SQL pada form login | Input: `' OR 1=1 --` pada username | Query gagal, tidak ada data bocor | LULUS | RENDAH | PDO prepared statement digunakan |
| SEC-005 | Cross-Site Scripting (XSS) | Script injection pada form pengajuan | Input: `<script>alert('xss')</script>` | Data disanitasi, script tidak dieksekusi | LULUS | RENDAH | htmlspecialchars() diterapkan |
| SEC-006 | Session Hijacking | Manipulasi session cookie | Modifikasi nilai session_id | Akses ditolak, session tidak valid | LULUS | RENDAH | Session validation berfungsi |
| SEC-007 | Keamanan Password | Penyimpanan password | Verifikasi hash password di database | Password tersimpan dalam bentuk hash | LULUS | RENDAH | password_hash() dan password_verify() |
| SEC-008 | CSRF Protection | Cross-Site Request Forgery | Form submission tanpa token CSRF | Request ditolak | PERLU PERBAIKAN | SEDANG | Token CSRF belum diimplementasi |
| SEC-009 | Input Validation | Data validation pada form | Input data tidak sesuai format | Error validation ditampilkan | LULUS | RENDAH | Validasi client-side dan server-side |
| SEC-010 | Information Disclosure | Error message exposure | Trigger database error | Error message tidak mengekspos informasi sensitif | LULUS | RENDAH | Error handling yang aman |
| SEC-011 | Brute Force Attack | Multiple login attempts | 100 percobaan login gagal berturut-turut | Sistem tetap stabil, tidak ada lockout | PERLU PERBAIKAN | SEDANG | Tidak ada rate limiting |
| SEC-012 | Path Traversal | Directory traversal attack | Input: `../../../etc/passwd` | Access denied, path terbatas | LULUS | RENDAH | File access terkontrol |
| SEC-013 | File Upload Security | Upload file berbahaya | Upload file .php/.exe/.bat | Hanya file yang diizinkan diterima | TIDAK DIUJI | - | Fitur upload tidak tersedia |
| SEC-014 | Data Encryption | Transmisi data sensitif | Monitor traffic HTTP | Data password di-hash sebelum transmisi | LULUS | RENDAH | Password hashing implementasi baik |
| SEC-015 | Access Control | Direct object reference | Akses data pengajuan user lain via ID | Hanya data milik user yang dapat diakses | LULUS | RENDAH | Authorization check per query |

## **REKOMENDASI PERBAIKAN KEAMANAN**

### **Prioritas Tinggi:**
1. **Implementasi CSRF Protection**: Tambahkan token CSRF pada semua form
2. **Rate Limiting**: Implementasi pembatasan percobaan login
3. **HTTPS Enforcement**: Pastikan semua komunikasi menggunakan HTTPS

### **Prioritas Sedang:**
1. **Session Timeout**: Implementasi timeout otomatis untuk session
2. **Input Sanitization**: Perkuat validasi input di semua endpoint
3. **Logging & Monitoring**: Catat aktivitas login dan perubahan data

### **Prioritas Rendah:**
1. **Password Policy**: Implementasi kebijakan password yang kuat
2. **Two-Factor Authentication**: Pertimbangkan 2FA untuk akun dosen
3. **Database Encryption**: Enkripsi data sensitif di database

## **KESIMPULAN PENGUJIAN KEAMANAN**

Sistem pengajuan skripsi menunjukkan tingkat keamanan yang **BAIK** dengan beberapa area yang perlu diperbaiki:

**Kekuatan:**
- Autentikasi dan otorisasi bekerja dengan baik
- Perlindungan terhadap SQL injection dan XSS
- Password hashing implementasi yang tepat
- Session management yang aman

**Kelemahan:**
- Belum ada CSRF protection
- Tidak ada rate limiting untuk brute force attack
- Belum ada session timeout

**Skor Keamanan: 80/100 (BAIK)**
