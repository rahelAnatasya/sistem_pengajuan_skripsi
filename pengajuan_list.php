<?php
require_once 'config/database.php';
require_once 'includes/session.php';

checkRole('mahasiswa');

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT p.*, u.nama_lengkap as dosen_nama 
                       FROM pengajuan_judul p 
                       LEFT JOIN users u ON p.dosen_pembimbing_id = u.id 
                       WHERE p.mahasiswa_id = :mahasiswa_id 
                       ORDER BY p.created_at DESC");
$stmt->bindParam(':mahasiswa_id', $_SESSION['user_id']);
$stmt->execute();
$pengajuan_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pengajuan Saya</title>
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
        <h2>Daftar Pengajuan Judul Skripsi</h2>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Bidang Studi</th>
                        <th>Dosen Pembimbing</th>
                        <th>Status</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($pengajuan_list) > 0): ?>
                        <?php foreach ($pengajuan_list as $index => $pengajuan): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo substr($pengajuan['judul_skripsi'], 0, 50) . '...'; ?></td>
                                <td><?php echo $pengajuan['bidang_studi']; ?></td>
                                <td><?php echo $pengajuan['dosen_nama']; ?></td>
                                <td>
                                    <span class="badge bg-<?php
                                    echo $pengajuan['status'] === 'approved' ? 'success' :
                                        ($pengajuan['status'] === 'rejected' ? 'danger' :
                                            ($pengajuan['status'] === 'revision' ? 'warning' : 'secondary')); ?>">
                                        <?php echo ucfirst($pengajuan['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($pengajuan['created_at'])); ?></td>
                                <td><?php echo $pengajuan['catatan'] ? substr($pengajuan['catatan'], 0, 30) . '...' : '-'; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Belum ada pengajuan</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
        <a href="pengajuan_form.php" class="btn btn-primary">Ajukan Judul Baru</a>
    </div>
</body>

</html>