CREATE DATABASE skripsi_pengajuan;
USE skripsi_pengajuan;

-- Tabel Users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('mahasiswa', 'dosen', 'admin') NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    nim VARCHAR(20) UNIQUE NULL,
    nidn VARCHAR(20) UNIQUE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Pengajuan Judul
CREATE TABLE pengajuan_judul (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mahasiswa_id INT NOT NULL,
    judul_skripsi TEXT NOT NULL,
    deskripsi TEXT NOT NULL,
    bidang_studi VARCHAR(100) NOT NULL,
    dosen_pembimbing_id INT NULL,
    status ENUM('pending', 'approved', 'rejected', 'revision') DEFAULT 'pending',
    catatan TEXT NULL,
    komentar_dosen TEXT NULL,
    tanggal_pengajuan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (mahasiswa_id) REFERENCES users(id),
    FOREIGN KEY (dosen_pembimbing_id) REFERENCES users(id)
);

-- Insert sample data
INSERT INTO users (username, email, password, role, nama_lengkap, nim) VALUES
('mahasiswa1', 'mahasiswa1@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mahasiswa', 'Ahmad Mahasiswa', '12345678'),
('dosen1', 'dosen1@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dosen', 'Dr. Dosen Pembimbing', NULL),
('admin1', 'admin@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Admin Sistem', NULL);

-- Tambah kolom yang hilang
ALTER TABLE pengajuan_judul 
ADD COLUMN komentar_dosen TEXT NULL AFTER catatan,
ADD COLUMN tanggal_pengajuan TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER created_at;

-- Update data existing jika ada
UPDATE pengajuan_judul SET tanggal_pengajuan = created_at WHERE tanggal_pengajuan IS NULL;