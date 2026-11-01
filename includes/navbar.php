<?php
// includes/navbar.php
$page_heading = $page_heading ?? 'Dashboard';
?>
<div class="main-content">
    <header class="top-navbar">
        <div class="d-flex align-items-center">
            <h5 class="mb-0 fw-bold text-dark"><?= htmlspecialchars($page_heading) ?></h5>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="d-none d-md-block text-end">
                <small class="text-muted d-block"><?= date('l, d F Y') ?></small>
                <span class="badge bg-light text-dark border"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?> (<?= htmlspecialchars($_SESSION['user_role'] ?? 'Admin') ?>)</span>
            </div>
            <a href="logout.php" class="btn btn-outline-danger btn-sm" title="Keluar">
                <i class="fa-solid fa-power-off"></i>
            </a>
        </div>
    </header>

    <div class="content-body">
        <?php
        $flash = get_flash();
        if ($flash):
        ?>
            <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-info me-2"></i><?= $flash['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
