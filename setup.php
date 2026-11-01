<?php
/**
 * ====================================================================================
 * FILE: setup.php
 * MODUL: Web Installer & Database Reset Otomatis (1-Klik)
 * DESKRIPSI:
 * Membantu pengguna atau programmer yang baru memasang project untuk secara otomatis
 * membuat database `rekrutmen_aigen`, membuat seluruh 6 tabel, dan menyuntikkan (seed)
 * 30 data kandidat asli dari skripsi tanpa perlu repot membuka phpMyAdmin manual.
 * ====================================================================================
 */

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'rekrutmen_aigen';

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Connect to MySQL server without selecting db first
        $conn = new mysqli($db_host, $db_user, $db_pass);
        if ($conn->connect_error) {
            throw new Exception("Koneksi ke MySQL gagal: " . $conn->connect_error);
        }

        // Read SQL script
        $sqlFile = __DIR__ . '/database/rekrutmen_aigen.sql';
        if (!file_exists($sqlFile)) {
            throw new Exception("File SQL tidak ditemukan di: " . $sqlFile);
        }

        $sql = file_get_contents($sqlFile);
        
        // Execute multi query
        if ($conn->multi_query($sql)) {
            do {
                if ($res = $conn->store_result()) {
                    $res->free();
                }
            } while ($conn->more_results() && $conn->next_result());
            
            $success = true;
            $message = "Database <strong>rekrutmen_aigen</strong> dan seluruh tabel & data dummy (30 peserta + kriteria + nilai) berhasil di-setup!";
        } else {
            throw new Exception("Gagal mengeksekusi script SQL: " . $conn->error);
        }
        $conn->close();
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Database - Sistem Rekrutmen IT PT Aigen Global Teknologi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3 text-center">
                    <h4 class="mb-0 fw-bold"><i class="fa-solid fa-database me-2"></i> Setup Database Sistem Rekrutmen IT</h4>
                    <small>PT. Aigen Global Teknologi Tangerang</small>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted">
                        Halaman ini memudahkan Anda membuat database <code>rekrutmen_aigen</code> dan mengisi tabel kriteria, bobot, 30 peserta awal, dan data penilaian langsung tanpa perlu import manual.
                    </p>

                    <?php if ($message): ?>
                        <div class="alert alert-<?= $success ? 'success' : 'danger' ?> d-flex align-items-center" role="alert">
                            <i class="fa-solid fa-<?= $success ? 'check-circle' : 'triangle-exclamation' ?> fa-2x me-3"></i>
                            <div>
                                <?= $message ?>
                            </div>
                        </div>
                        <?php if ($success): ?>
                            <div class="text-center my-3">
                                <a href="login.php" class="btn btn-success btn-lg px-4"><i class="fa-solid fa-arrow-right-to-bracket me-2"></i> Ke Halaman Login</a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div class="card bg-body-tertiary border p-3 mb-4">
                        <h6 class="fw-bold mb-2"><i class="fa-solid fa-circle-info text-primary me-2"></i> Konfigurasi Default:</h6>
                        <ul class="mb-0 small text-secondary">
                            <li><strong>Host:</strong> localhost</li>
                            <li><strong>User:</strong> root</li>
                            <li><strong>Password:</strong> (kosong)</li>
                            <li><strong>Database Name:</strong> rekrutmen_aigen</li>
                            <li><strong>File SQL:</strong> <code>database/rekrutmen_aigen.sql</code></li>
                        </ul>
                    </div>

                    <form method="POST">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-2 fw-semibold">
                                <i class="fa-solid fa-wand-magic-sparkles me-2"></i> Jalankan Setup Database Otomatis
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">
                    <div class="text-center">
                        <p class="small text-muted mb-1">Atau import manual file <code>database/rekrutmen_aigen.sql</code> lewat <strong>phpMyAdmin</strong>.</p>
                        <a href="login.php" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-house me-1"></i> Buka Aplikasi (Login)</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
