<?php

declare(strict_types=1);

ob_start();
session_start();

define('IS_ADMIN', true);
require_once __DIR__ . '/../src/php/utils/all_includes.php';

// Whitelists des pages autorisées (centralisées dans config/pages.php)
require_once __DIR__ . '/../config/pages.php';

$page = $_GET['page'] ?? 'accueil';

if (!in_array($page, $_openPages, true)) {
    if (empty($_SESSION['root'])) {
        header('Location: /root/index_.php?page=login');
        exit;
    }
    if (!in_array($page, $_rootPages, true)) {
        $page = 'page_404';
    }
}

$_pageFile = __DIR__ . '/content/' . $page . '.php';
if (!file_exists($_pageFile)) {
    $page      = 'page_404';
    $_pageFile = __DIR__ . '/content/page_404.php';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stone Shop — Root</title>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
</head>
<body class="<?= $page === 'login' ? 'login-screen' : 'admin-layout' ?>">

<?php if (!in_array($page, $_openPages, true)): ?>
    <nav class="navbar navbar-dark bg-header admin-navbar">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="/root/index_.php">
                <i class="bi bi-shield-lock me-1"></i>Stone Root
            </a>
            <ul class="navbar-nav flex-row gap-3 ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'gestion_admins' ? 'active' : '' ?>"
                       href="/root/index_.php?page=gestion_admins">Admins</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'gestion_supports' ? 'active' : '' ?>"
                       href="/root/index_.php?page=gestion_supports">Supports</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page === 'gestion_clients' ? 'active' : '' ?>"
                       href="/root/index_.php?page=gestion_clients">Clients</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-warning" href="/root/index_.php?page=login&logout=1">
                        <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
                    </a>
                </li>
            </ul>
        </div>
    </nav>
<?php endif; ?>

<main class="<?= !in_array($page, $_openPages, true) ? 'admin-content' : '' ?>">
    <?php require_once $_pageFile; ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>
