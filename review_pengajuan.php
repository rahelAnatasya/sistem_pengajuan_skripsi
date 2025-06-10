<?php
require_once 'config/database.php';
require_once 'includes/session.php';

checkRole('dosen');

$success = '';
$error = '';

$db = new Database();
$conn = $db->getConnection();

// Handle status update
if ($_POST && isset($_POST['action'])) {
    $pengajuan_id = $_POST['pengajuan_id'];
    $action = $_POST['action'];
    $komentar = $_POST['komentar'] ?? '';
    
    $status = ($action === 'approve') ? 'approved' : 'rejected';
    
    try {
        $stmt = $conn->prepare("UPDATE pengajuan_judul SET status = :status, catatan = :komentar 
                               WHERE id = :id AND dosen_pembimbing_id = :dosen_id");
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':komentar', $komentar);
        $stmt->bindParam(':id', $pengajuan_id);
        $stmt->bindParam(':dosen_id', $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $success = "Status pengajuan berhasil diperbarui!";
        }
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get submissions for this lecturer
$stmt = $conn->prepare("SELECT p.*, u.nama_lengkap as nama_mahasiswa, u.nim 
                       FROM pengajuan_judul p 
                       JOIN users u ON p.mahasiswa_id = u.id 
                       WHERE p.dosen_pembimbing_id = :dosen_id 
                       ORDER BY p.created_at DESC");
$stmt->bindParam(':dosen_id', $_SESSION['user_id']);
$stmt->execute();
$pengajuan_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Pengajuan Judul Skripsi</title>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Review Pengajuan Judul Skripsi</h2>
            <div>
                <span class="badge bg-warning me-2">Pending: <?php echo count(array_filter($pengajuan_list, fn($p) => $p['status'] === 'pending')); ?></span>
                <span class="badge bg-success me-2">Approved: <?php echo count(array_filter($pengajuan_list, fn($p) => $p['status'] === 'approved')); ?></span>
                <span class="badge bg-danger">Rejected: <?php echo count(array_filter($pengajuan_list, fn($p) => $p['status'] === 'rejected')); ?></span>
            </div>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (empty($pengajuan_list)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Tidak ada pengajuan judul yang perlu direview.
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($pengajuan_list as $pengajuan): ?>
                    <div class="col-md-12 mb-4">
                        <div class="card <?php echo $pengajuan['status'] === 'pending' ? 'border-warning' : ''; ?>">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">
                                        <i class="fas fa-user"></i> 
                                        <?php echo htmlspecialchars($pengajuan['nama_mahasiswa']); ?> 
                                        <small class="text-muted">(<?php echo htmlspecialchars($pengajuan['nim']); ?>)</small>
                                    </h5>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar"></i> 
                                        <?php echo date('d-m-Y H:i', strtotime($pengajuan['created_at'])); ?>
                                    </small>
                                </div>
                                <span class="badge bg-<?php 
                                    echo $pengajuan['status'] === 'pending' ? 'warning' : 
                                         ($pengajuan['status'] === 'approved' ? 'success' : 
                                          ($pengajuan['status'] === 'rejected' ? 'danger' : 'secondary')); 
                                ?> fs-6">
                                    <i class="fas fa-<?php 
                                        echo $pengajuan['status'] === 'pending' ? 'clock' : 
                                             ($pengajuan['status'] === 'approved' ? 'check' : 
                                              ($pengajuan['status'] === 'rejected' ? 'times' : 'question')); 
                                    ?>"></i>
                                    <?php echo ucfirst($pengajuan['status']); ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6><strong><i class="fas fa-graduation-cap"></i> Judul Skripsi:</strong></h6>
                                        <div class="bg-light p-3 rounded mb-3">
                                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($pengajuan['judul_skripsi'])); ?></p>
                                        </div>
                                        
                                        <h6><strong><i class="fas fa-book"></i> Bidang Studi:</strong></h6>
                                        <p><span class="badge bg-info"><?php echo htmlspecialchars($pengajuan['bidang_studi']); ?></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6><strong><i class="fas fa-file-alt"></i> Deskripsi/Latar Belakang:</strong></h6>
                                        <div class="bg-light p-3 rounded mb-3" style="max-height: 200px; overflow-y: auto;">
                                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($pengajuan['deskripsi'])); ?></p>
                                        </div>
                                        
                                        <?php if ($pengajuan['catatan']): ?>
                                            <h6><strong><i class="fas fa-comment"></i> Catatan Dosen:</strong></h6>
                                            <div class="bg-warning bg-opacity-25 p-3 rounded">
                                                <p class="mb-0"><?php echo nl2br(htmlspecialchars($pengajuan['catatan'])); ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <?php if ($pengajuan['status'] === 'pending'): ?>
                                    <hr>
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-edit"></i> Review Pengajuan</h6>
                                        </div>
                                        <div class="card-body">
                                            <form method="POST" class="row g-3" onsubmit="return confirmAction(event)">
                                                <input type="hidden" name="pengajuan_id" value="<?php echo $pengajuan['id']; ?>">
                                                <div class="col-12">
                                                    <label for="komentar_<?php echo $pengajuan['id']; ?>" class="form-label">
                                                        <i class="fas fa-comment-dots"></i> Komentar/Feedback:
                                                    </label>
                                                    <textarea class="form-control" id="komentar_<?php echo $pengajuan['id']; ?>" 
                                                              name="komentar" rows="3" 
                                                              placeholder="Berikan komentar, saran, atau alasan penolakan..."></textarea>
                                                </div>
                                                <div class="col-12">
                                                    <div class="btn-group w-100" role="group">
                                                        <button type="submit" name="action" value="approve" 
                                                                class="btn btn-success btn-lg">
                                                            <i class="fas fa-check"></i> Setujui Pengajuan
                                                        </button>
                                                        <button type="submit" name="action" value="reject" 
                                                                class="btn btn-danger btn-lg">
                                                            <i class="fas fa-times"></i> Tolak Pengajuan
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="mt-4 text-center">
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmAction(event) {
            const action = event.submitter.value;
            const actionText = action === 'approve' ? 'menyetujui' : 'menolak';
            const message = `Apakah Anda yakin ingin ${actionText} pengajuan ini?`;
            
            if (!confirm(message)) {
                event.preventDefault();
                return false;
            }
            return true;
        }

        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>