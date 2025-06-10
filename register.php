<?php
require_once 'config/database.php';
require_once 'includes/session.php';

if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$success = '';

if ($_POST) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $nim = isset($_POST['nim']) ? $_POST['nim'] : null;
    $nidn = isset($_POST['nidn']) ? $_POST['nidn'] : null;

    $db = new Database();
    $conn = $db->getConnection();

    try {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, nama_lengkap, nim, nidn) 
                               VALUES (:username, :email, :password, :role, :nama_lengkap, :nim, :nidn)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':nama_lengkap', $nama_lengkap);
        $stmt->bindParam(':nim', $nim);
        $stmt->bindParam(':nidn', $nidn);

        if ($stmt->execute()) {
            $success = "Registrasi berhasil! Silakan login.";
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Sistem Pengajuan Judul Skripsi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="text-center">Registrasi Pengguna</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <form method="POST" id="registrationForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-control" id="role" name="role" required onchange="toggleFields()">
                                    <option value="">Pilih Role</option>
                                    <option value="mahasiswa">Mahasiswa</option>
                                    <option value="dosen">Dosen</option>
                                </select>
                            </div>

                            <div class="mb-3" id="nimField" style="display: none;">
                                <label for="nim" class="form-label">NIM</label>
                                <input type="text" class="form-control" id="nim" name="nim">
                            </div>

                            <div class="mb-3" id="nidnField" style="display: none;">
                                <label for="nidn" class="form-label">NIDN</label>
                                <input type="text" class="form-control" id="nidn" name="nidn">
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Daftar</button>
                        </form>

                        <div class="mt-3 text-center">
                            <a href="login.php">Sudah punya akun? Login di sini</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleFields() {
            const role = document.getElementById('role').value;
            const nimField = document.getElementById('nimField');
            const nidnField = document.getElementById('nidnField');

            if (role === 'mahasiswa') {
                nimField.style.display = 'block';
                nidnField.style.display = 'none';
                document.getElementById('nim').required = true;
                document.getElementById('nidn').required = false;
            } else if (role === 'dosen') {
                nimField.style.display = 'none';
                nidnField.style.display = 'block';
                document.getElementById('nim').required = false;
                document.getElementById('nidn').required = true;
            } else {
                nimField.style.display = 'none';
                nidnField.style.display = 'none';
                document.getElementById('nim').required = false;
                document.getElementById('nidn').required = false;
            }
        }
    </script>
</body>

</html>