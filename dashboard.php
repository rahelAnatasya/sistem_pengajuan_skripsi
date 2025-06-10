<?php
require_once 'config/database.php';
require_once 'includes/session.php';

checkLogin();

$db = new Database();
$conn = $db->getConnection();

// Get statistics based on role
$stats = [];
if ($_SESSION['role'] === 'mahasiswa') {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM pengajuan_judul WHERE mahasiswa_id = :mahasiswa_id");
    $stmt->bindParam(':mahasiswa_id', $_SESSION['user_id']);
    $stmt->execute();
    $stats['total_pengajuan'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
} elseif ($_SESSION['role'] === 'dosen') {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM pengajuan_judul WHERE dosen_pembimbing_id = :dosen_id");
    $stmt->bindParam(':dosen_id', $_SESSION['user_id']);
    $stmt->execute();
    $stats['total_bimbingan'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

// Get general statistics for all users
$general_stats_stmt = $conn->prepare("SELECT 
    COUNT(*) as total_pengajuan,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
    FROM pengajuan_judul");
$general_stats_stmt->execute();
$general_stats = $general_stats_stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Pengajuan Judul Skripsi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Sistem Pengajuan Skripsi</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Selamat datang, <?php echo $_SESSION['nama_lengkap']; ?></span>
                <a class="btn btn-outline-light" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header"><i class="fas fa-bars"></i> Menu Navigasi</div>
                    <div class="list-group list-group-flush">
                        <!-- Menu untuk semua role -->
                        <a href="public_pengajuan.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-list"></i> Lihat Semua Pengajuan
                        </a>

                        <?php if ($_SESSION['role'] === 'mahasiswa'): ?>
                            <a href="pengajuan_form.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-plus"></i> Ajukan Judul
                            </a>
                            <a href="pengajuan_list.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-file-alt"></i> Daftar Pengajuan Saya
                            </a>
                        <?php elseif ($_SESSION['role'] === 'dosen'): ?>
                            <a href="review_pengajuan.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-edit"></i> Review Pengajuan
                            </a>
                        <?php elseif ($_SESSION['role'] === 'admin'): ?>
                            <a href="manage_users.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-users"></i> Kelola Pengguna
                            </a>
                            <a href="all_pengajuan.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-tasks"></i> Kelola Semua Pengajuan
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- General Statistics Card -->
                <div class="card mt-3">
                    <div class="card-header">
                        <i class="fas fa-chart-bar"></i> Statistik Umum
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 mb-2">
                                <span
                                    class="badge bg-secondary fs-6"><?php echo $general_stats['total_pengajuan']; ?></span>
                                <small class="d-block">Total</small>
                            </div>
                            <div class="col-6 mb-2">
                                <span class="badge bg-warning fs-6"><?php echo $general_stats['pending']; ?></span>
                                <small class="d-block">Pending</small>
                            </div>
                            <div class="col-6">
                                <span class="badge bg-success fs-6"><?php echo $general_stats['approved']; ?></span>
                                <small class="d-block">Approved</small>
                            </div>
                            <div class="col-6">
                                <span class="badge bg-danger fs-6"><?php echo $general_stats['rejected']; ?></span>
                                <small class="d-block">Rejected</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <h2><i class="fas fa-tachometer-alt"></i> Dashboard <?php echo ucfirst($_SESSION['role']); ?></h2>

                <?php if ($_SESSION['role'] === 'mahasiswa'): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-file-alt"></i> Statistik Pengajuan Saya
                                    </h5>
                                    <p class="card-text">Total pengajuan Anda:
                                        <span class="badge bg-primary"><?php echo $stats['total_pengajuan']; ?></span>
                                    </p>
                                    <a href="pengajuan_form.php" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Ajukan Judul Baru
                                    </a>
                                    <a href="pengajuan_list.php" class="btn btn-outline-primary">
                                        <i class="fas fa-list"></i> Lihat Pengajuan Saya
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-eye"></i> Lihat Pengajuan Lain
                                    </h5>
                                    <p class="card-text">Lihat pengajuan judul skripsi dari mahasiswa lain untuk inspirasi
                                    </p>
                                    <a href="public_pengajuan.php" class="btn btn-info">
                                        <i class="fas fa-list"></i> Lihat Semua Pengajuan
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php elseif ($_SESSION['role'] === 'dosen'): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-users"></i> Statistik Bimbingan
                                    </h5>
                                    <p class="card-text">Total mahasiswa bimbingan:
                                        <span class="badge bg-success"><?php echo $stats['total_bimbingan']; ?></span>
                                    </p>
                                    <a href="review_pengajuan.php" class="btn btn-success">
                                        <i class="fas fa-edit"></i> Review Pengajuan
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-eye"></i> Monitoring Umum
                                    </h5>
                                    <p class="card-text">Pantau semua pengajuan judul skripsi di sistem</p>
                                    <a href="public_pengajuan.php" class="btn btn-info">
                                        <i class="fas fa-list"></i> Lihat Semua Pengajuan
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php elseif ($_SESSION['role'] === 'admin'): ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="card-title">
                                        <i class="fas fa-users"></i> Kelola Pengguna
                                    </h5>
                                    <a href="manage_users.php" class="btn btn-primary">
                                        <i class="fas fa-users-cog"></i> Kelola
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="card-title">
                                        <i class="fas fa-tasks"></i> Kelola Pengajuan
                                    </h5>
                                    <a href="all_pengajuan.php" class="btn btn-success">
                                        <i class="fas fa-cogs"></i> Kelola
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="card-title">
                                        <i class="fas fa-chart-bar"></i> Lihat Statistik
                                    </h5>
                                    <a href="public_pengajuan.php" class="btn btn-info">
                                        <i class="fas fa-chart-line"></i> Lihat
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Recent Activity Section -->
                <div class="mt-4">
                    <h4><i class="fas fa-clock"></i> Aktivitas Terbaru</h4>
                    <div class="card">
                        <div class="card-body">
                            <?php
                            // Get recent activity based on role
                            if ($_SESSION['role'] === 'mahasiswa') {
                                $recent_stmt = $conn->prepare("SELECT p.*, u.nama_lengkap as dosen_nama 
                                                              FROM pengajuan_judul p 
                                                              LEFT JOIN users u ON p.dosen_pembimbing_id = u.id 
                                                              WHERE p.mahasiswa_id = :user_id 
                                                              ORDER BY p.updated_at DESC LIMIT 3");
                            } elseif ($_SESSION['role'] === 'dosen') {
                                $recent_stmt = $conn->prepare("SELECT p.*, u.nama_lengkap as nama_mahasiswa 
                                                              FROM pengajuan_judul p 
                                                              JOIN users u ON p.mahasiswa_id = u.id 
                                                              WHERE p.dosen_pembimbing_id = :user_id 
                                                              ORDER BY p.updated_at DESC LIMIT 3");
                            } else {
                                $recent_stmt = $conn->prepare("SELECT p.*, 
                                                                     u1.nama_lengkap as nama_mahasiswa,
                                                                     u2.nama_lengkap as nama_dosen
                                                              FROM pengajuan_judul p 
                                                              JOIN users u1 ON p.mahasiswa_id = u1.id 
                                                              LEFT JOIN users u2 ON p.dosen_pembimbing_id = u2.id 
                                                              ORDER BY p.updated_at DESC LIMIT 5");
                            }
                            $recent_stmt->bindParam(':user_id', $_SESSION['user_id']);
                            $recent_stmt->execute();
                            $recent_activities = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);
                            ?>

                            <?php if (empty($recent_activities)): ?>
                                <p class="text-muted">Belum ada aktivitas terbaru.</p>
                            <?php else: ?>
                                <div class="list-group">
                                    <?php foreach ($recent_activities as $activity): ?>
                                        <div class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">
                                                    <?php if ($_SESSION['role'] === 'mahasiswa'): ?>
                                                        <?php echo substr($activity['judul_skripsi'], 0, 50) . '...'; ?>
                                                    <?php else: ?>
                                                        <?php echo $activity['nama_mahasiswa']; ?> -
                                                        <?php echo substr($activity['judul_skripsi'], 0, 30) . '...'; ?>
                                                    <?php endif; ?>
                                                </h6>
                                                <small><?php echo date('d-m-Y H:i', strtotime($activity['updated_at'])); ?></small>
                                            </div>
                                            <span class="badge bg-<?php
                                            echo $activity['status'] === 'pending' ? 'warning' :
                                                ($activity['status'] === 'approved' ? 'success' : 'danger');
                                            ?>"><?php echo ucfirst($activity['status']); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>