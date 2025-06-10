<?php
require_once 'includes/session.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                        </div>
                        <h4>Akses Ditolak</h4>
                        <p class="text-muted">Anda tidak memiliki akses ke halaman tersebut.</p>
                        <a href="dashboard.php" class="btn btn-primary">Kembali ke Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>