<?php
require_once 'config/database.php';
require_once 'includes/session.php';

checkLogin();

$db = new Database();
$conn = $db->getConnection();

// Filter by status
$status_filter = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';

// Build query based on filters
$where_conditions = [];
$params = [];

if ($status_filter !== 'all') {
    $where_conditions[] = "p.status = :status";
    $params[':status'] = $status_filter;
}

if (!empty($search)) {
    $where_conditions[] = "(p.judul_skripsi LIKE :search OR u1.nama_lengkap LIKE :search OR p.bidang_studi LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

$stmt = $conn->prepare("SELECT p.*, 
                               u1.nama_lengkap as nama_mahasiswa, 
                               u1.nim,
                               u2.nama_lengkap as nama_dosen
                        FROM pengajuan_judul p 
                        JOIN users u1 ON p.mahasiswa_id = u1.id 
                        LEFT JOIN users u2 ON p.dosen_pembimbing_id = u2.id 
                        $where_clause
                        ORDER BY p.created_at DESC");

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->execute();
$pengajuan_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$stats_stmt = $conn->prepare("SELECT 
                                COUNT(*) as total,
                                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
                              FROM pengajuan_judul");
$stats_stmt->execute();
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pengajuan Judul Skripsi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Sistem Pengajuan Skripsi</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Selamat datang, <?php echo $_SESSION['nama_lengkap']; ?></span>
                <a class="btn btn-outline-light" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Header with Statistics -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h2><i class="fas fa-list"></i> Daftar Pengajuan Judul Skripsi</h2>
                <p class="text-muted">Lihat semua pengajuan judul skripsi dan statusnya</p>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6>Statistik Pengajuan</h6>
                        <div class="d-flex justify-content-around">
                            <div>
                                <span class="badge bg-secondary fs-6"><?php echo $stats['total']; ?></span>
                                <small class="d-block">Total</small>
                            </div>
                            <div>
                                <span class="badge bg-warning fs-6"><?php echo $stats['pending']; ?></span>
                                <small class="d-block">Pending</small>
                            </div>
                            <div>
                                <span class="badge bg-success fs-6"><?php echo $stats['approved']; ?></span>
                                <small class="d-block">Approved</small>
                            </div>
                            <div>
                                <span class="badge bg-danger fs-6"><?php echo $stats['rejected']; ?></span>
                                <small class="d-block">Rejected</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter and Search -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="status" class="form-label">Filter Status:</label>
                        <select class="form-control" id="status" name="status" onchange="this.form.submit()">
                            <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>Semua Status</option>
                            <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                            <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="search" class="form-label">Cari:</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="<?php echo htmlspecialchars($search); ?>" 
                               placeholder="Cari berdasarkan judul, nama mahasiswa, atau bidang studi...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results -->
        <?php if (empty($pengajuan_list)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                Tidak ada pengajuan yang ditemukan dengan kriteria pencarian tersebut.
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($pengajuan_list as $pengajuan): ?>
                    <div class="col-md-12 mb-3">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">
                                        <i class="fas fa-user"></i> 
                                        <?php echo htmlspecialchars($pengajuan['nama_mahasiswa']); ?>
                                        <small class="text-muted">(<?php echo htmlspecialchars($pengajuan['nim']); ?>)</small>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar"></i> 
                                        <?php echo date('d-m-Y H:i', strtotime($pengajuan['created_at'])); ?>
                                    </small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-<?php 
                                        echo $pengajuan['status'] === 'pending' ? 'warning' : 
                                             ($pengajuan['status'] === 'approved' ? 'success' : 
                                              ($pengajuan['status'] === 'rejected' ? 'danger' : 'secondary')); 
                                    ?> mb-1">
                                        <i class="fas fa-<?php 
                                            echo $pengajuan['status'] === 'pending' ? 'clock' : 
                                                 ($pengajuan['status'] === 'approved' ? 'check' : 
                                                  ($pengajuan['status'] === 'rejected' ? 'times' : 'question')); 
                                        ?>"></i>
                                        <?php echo ucfirst($pengajuan['status']); ?>
                                    </span>
                                    <?php if ($pengajuan['nama_dosen']): ?>
                                        <br><small class="text-muted">
                                            <i class="fas fa-chalkboard-teacher"></i> 
                                            <?php echo htmlspecialchars($pengajuan['nama_dosen']); ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6><strong><i class="fas fa-graduation-cap"></i> Judul Skripsi:</strong></h6>
                                        <p class="mb-2"><?php echo htmlspecialchars($pengajuan['judul_skripsi']); ?></p>
                                        
                                        <h6><strong><i class="fas fa-book"></i> Bidang Studi:</strong></h6>
                                        <span class="badge bg-info mb-2"><?php echo htmlspecialchars($pengajuan['bidang_studi']); ?></span>
                                    </div>
                                    <div class="col-md-4">
                                        <!-- Show description in collapsed format -->
                                        <h6><strong><i class="fas fa-file-alt"></i> Deskripsi:</strong></h6>
                                        <div class="collapse" id="desc_<?php echo $pengajuan['id']; ?>">
                                            <div class="card card-body bg-light">
                                                <small><?php echo nl2br(htmlspecialchars($pengajuan['deskripsi'])); ?></small>
                                            </div>
                                        </div>
                                        <button class="btn btn-sm btn-outline-secondary" type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#desc_<?php echo $pengajuan['id']; ?>">
                                            <i class="fas fa-eye"></i> Lihat Deskripsi
                                        </button>
                                        
                                        <?php if ($pengajuan['catatan']): ?>
                                            <h6 class="mt-3"><strong><i class="fas fa-comment"></i> Catatan:</strong></h6>
                                            <div class="collapse" id="note_<?php echo $pengajuan['id']; ?>">
                                                <div class="card card-body bg-warning bg-opacity-25">
                                                    <small><?php echo nl2br(htmlspecialchars($pengajuan['catatan'])); ?></small>
                                                </div>
                                            </div>
                                            <button class="btn btn-sm btn-outline-warning" type="button" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#note_<?php echo $pengajuan['id']; ?>">
                                                <i class="fas fa-comment-dots"></i> Lihat Catatan
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Back to Dashboard -->
        <div class="mt-4 text-center">
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
            <?php if ($_SESSION['role'] === 'mahasiswa'): ?>
                <a href="pengajuan_form.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Ajukan Judul Baru
                </a>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>