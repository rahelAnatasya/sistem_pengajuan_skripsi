<?php
require_once 'config/database.php';
require_once 'includes/session.php';

checkRole('mahasiswa');

$success = '';
$error = '';

$db = new Database();
$conn = $db->getConnection();

// Get list of dosen
$stmt = $conn->prepare("SELECT id, nama_lengkap FROM users WHERE role = 'dosen'");
$stmt->execute();
$dosen_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_POST) {
    $judul_skripsi = $_POST['judul_skripsi'];
    $deskripsi = $_POST['deskripsi'];
    $bidang_studi = $_POST['bidang_studi'];
    $dosen_pembimbing_id = $_POST['dosen_pembimbing_id'];
    
    try {
        $stmt = $conn->prepare("INSERT INTO pengajuan_judul (mahasiswa_id, judul_skripsi, deskripsi, bidang_studi, dosen_pembimbing_id) 
                               VALUES (:mahasiswa_id, :judul_skripsi, :deskripsi, :bidang_studi, :dosen_pembimbing_id)");
        $stmt->bindParam(':mahasiswa_id', $_SESSION['user_id']);
        $stmt->bindParam(':judul_skripsi', $judul_skripsi);
        $stmt->bindParam(':deskripsi', $deskripsi);
        $stmt->bindParam(':bidang_studi', $bidang_studi);
        $stmt->bindParam(':dosen_pembimbing_id', $dosen_pembimbing_id);
        
        if ($stmt->execute()) {
            $success = "Pengajuan judul berhasil dikirim!";
        }
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Judul Skripsi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Sistem Pengajuan Skripsi</a>
            <div class="navbar-nav ms-auto">
                <a class="btn btn-outline-light" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>Form Pengajuan Judul Skripsi</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="judul_skripsi" class="form-label">Judul Skripsi</label>
                                <textarea class="form-control" id="judul_skripsi" name="judul_skripsi" rows="3" required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi/Latar Belakang</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5" required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="bidang_studi" class="form-label">Bidang Studi</label>
                                <select class="form-control" id="bidang_studi" name="bidang_studi" required>
                                    <option value="">Pilih Bidang Studi</option>
                                    <option value="Rekayasa Perangkat Lunak">Rekayasa Perangkat Lunak</option>
                                    <option value="Sistem Informasi">Sistem Informasi</option>
                                    <option value="Jaringan Komputer">Jaringan Komputer</option>
                                    <option value="Kecerdasan Buatan">Kecerdasan Buatan</option>
                                    <option value="Multimedia">Multimedia</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="dosen_pembimbing_id" class="form-label">Dosen Pembimbing</label>
                                <select class="form-control" id="dosen_pembimbing_id" name="dosen_pembimbing_id" required>
                                    <option value="">Pilih Dosen Pembimbing</option>
                                    <?php foreach ($dosen_list as $dosen): ?>
                                        <option value="<?php echo $dosen['id']; ?>"><?php echo $dosen['nama_lengkap']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                            <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>