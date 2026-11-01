<?php
/**
 * ====================================================================================
 * FILE: login.php
 * MODUL: Autentikasi Pengguna (Admin & Penguji)
 * DESKRIPSI:
 * Menangani proses otentikasi login sesuai Gambar 4.15 - 4.17 dokumen skripsi.
 * Mendukung autentikasi berbasis role (Admin / Penguji), validasi input form,
 * pengecekan password, serta pembuatan session login pengguna.
 * ====================================================================================
 */

require_once __DIR__ . '/config/database.php';

// 1. Cek status sesi login: Jika sudah login, langsung alihkan ke dashboard utama
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

// 2. Memproses form saat tombol login ditekan (metode POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitasi dan tangkap data input pengguna
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $kategori = trim($_POST['kategori'] ?? ''); // Role: 'Admin' atau 'Penguji'

    // Validasi apakah input form telah diisi lengkap
    if (empty($username) || empty($password) || empty($kategori)) {
        $error = 'Silakan isi username, password, dan pilih kategori pengguna!';
    } else {
        // Validasi koneksi database
        if (!$db_connected) {
            $error = 'Belum terhubung ke database. Silakan jalankan <a href="setup.php" class="alert-link">Setup Database</a> terlebih dahulu.';
        } else {
            // Prepared statement untuk mencegah SQL Injection
            $stmt = $conn->prepare("SELECT * FROM pengguna WHERE username = ? AND kategori = ? LIMIT 1");
            $stmt->bind_param("ss", $username, $kategori);
            $stmt->execute();
            $result = $stmt->get_result();

            // Cek apakah data user ditemukan di database
            if ($user = $result->fetch_assoc()) {
                // Verifikasi kecocokan password (mendukung plaintext dan hash password_verify)
                if ($password === $user['password'] || password_verify($password, $user['password'])) {
                    // Daftarkan data pengguna ke dalam SESSION
                    $_SESSION['user_id']   = $user['id_pengguna'];
                    $_SESSION['user_name'] = $user['nama'];
                    $_SESSION['username']  = $user['username'];
                    $_SESSION['user_role'] = $user['kategori'];

                    // Buat notifikasi flash selamat datang dan arahkan ke dashboard
                    set_flash('success', 'Selamat datang kembali, ' . htmlspecialchars($user['nama']) . '!');
                    header("Location: dashboard.php");
                    exit;
                } else {
                    $error = 'Kombinasi kata sandi (password) salah!';
                }
            } else {
                $error = 'Akun pengguna dengan kategori tersebut tidak ditemukan di sistem!';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Seleksi Karyawan Bootcamp PT Aigen Global Teknologi</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.25), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
            max-width: 440px;
            width: 100%;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .login-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 2rem 1.5rem 1.5rem 1.5rem;
            text-align: center;
        }
        .brand-badge {
            width: 64px;
            height: 64px;
            background: #eff6ff;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            font-size: 2rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.15);
        }
        .input-group-text {
            background-color: #f8fafc;
            border-right: none;
            color: #64748b;
        }
        .form-control, .form-select {
            border-left: none;
        }
        .form-control:focus, .form-select:focus {
            box-shadow: none;
            border-color: #dee2e6;
        }
        .input-group:focus-within {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
            border-radius: 0.375rem;
        }
        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control,
        .input-group:focus-within .form-select {
            border-color: #86b7fe;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <div class="text-center mb-2">
            <img src="assets/logo-aigen.jpg" alt="Logo PT Aigen" style="width: 75px; height: 75px; object-fit: contain; background: white; border-radius: 12px; padding: 4px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
        </div>
        <h5 class="fw-bold text-dark mb-1">SELEKSI KARYAWAN BOOTCAMP</h5>
        <div class="text-primary fw-semibold small letter-spacing-1">PT AIGEN GLOBAL TEKNOLOGI</div>
        <div class="text-muted small mt-2">Sistem Informasi Rekrutmen & Uji Kompetensi</div>
    </div>

    <div class="p-4 p-md-4">
        <?php if ($error): ?>
            <div class="alert alert-danger py-2 px-3 small d-flex align-items-center mb-3" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <div><?= $error ?></div>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <!-- Username Input -->
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autofocus value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>
            </div>

            <!-- Password Input -->
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
            </div>

            <!-- Kategori / Role Selector -->
            <div class="mb-4">
                <label class="form-label small fw-semibold text-secondary">Pilih Pengguna</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-users"></i></span>
                    <select name="kategori" class="form-select" required>
                        <option value="">-- Pilih Pengguna --</option>
                        <option value="Admin" <?= (isset($_POST['kategori']) && $_POST['kategori'] === 'Admin') ? 'selected' : '' ?>>Admin</option>
                        <option value="Penguji" <?= (isset($_POST['kategori']) && $_POST['kategori'] === 'Penguji') ? 'selected' : '' ?>>Penguji</option>
                    </select>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary py-2 fw-bold text-uppercase">
                    <i class="fa-solid fa-right-to-bracket me-2"></i> LOGIN
                </button>
            </div>
        </form>

        <div class="bg-light p-3 rounded-3 border small text-muted">
            <div class="fw-bold text-dark mb-1"><i class="fa-solid fa-key text-warning me-1"></i> Akun Demo Tersedia:</div>
            <div class="d-flex justify-content-between mb-1">
                <span><strong>Admin:</strong> <code>admin</code> / <code>admin123</code></span>
                <span class="badge bg-primary">Admin</span>
            </div>
            <div class="d-flex justify-content-between">
                <span><strong>Penguji:</strong> <code>penguji</code> / <code>penguji123</code></span>
                <span class="badge bg-success">Penguji</span>
            </div>
        </div>

        <div class="text-center mt-3">
            <a href="setup.php" class="text-decoration-none small text-secondary">
                <i class="fa-solid fa-wrench me-1"></i> Setup / Reset Database
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
