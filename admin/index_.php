<?php

declare(strict_types=1);

ob_start();
session_start();

define('IS_ADMIN', true);
require_once __DIR__ . '/../src/php/utils/all_includes.php';

// Pages réservées à l'administrateur
$_adminPages = [
    'accueil', 'login', 'page_404',
    'gestion_catalogue', 'gestion_variantes', 'gestion_categories',
    'gestion_images', 'gestion_commandes', 'gestion_promotions',
    'gestion_codes_promo', 'gestion_transporteurs',
    'moderation_avis', 'messages_contact',
];

// Pages réservées au support (sous-ensemble)
$_supportPages = ['accueil', 'login', 'page_404', 'support_chat'];

// Pages sans vérification d'authentification
$_openPages = ['login', 'page_404'];

$page = $_GET['page'] ?? 'accueil';

if (!in_array($page, $_openPages, true)) {
    if (isset($_SESSION['admin'])) {
        if (!in_array($page, $_adminPages, true)) {
            $page = 'page_404';
        }
    } elseif (isset($_SESSION['support'])) {
        if (!in_array($page, $_supportPages, true)) {
            $page = 'page_404';
        }
    } else {
        header('Location: index_.php?page=login');
        exit;
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
    <title>Stone Shop — Administration</title>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
</head>
<body class="<?= $page === 'login' ? 'login-screen' : 'admin-layout' ?>" data-csrf-token="<?= $_SESSION['csrf_token'] ?? '' ?>">

<?php if (!in_array($page, $_openPages, true)): ?>
    <?php if (isset($_SESSION['admin'])): ?>
        <?php require_once __DIR__ . '/../src/php/utils/menu_admin.php'; ?>
    <?php else: ?>
        <?php require_once __DIR__ . '/../src/php/utils/menu_support.php'; ?>
    <?php endif; ?>
<?php endif; ?>

<main class="<?= !in_array($page, $_openPages, true) ? 'admin-content ms-0' : '' ?>">
    <?php require_once $_pageFile; ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="../src/js/stocks.js"></script>
<script src="../src/js/chat_polling.js?v=<?= time() ?>"></script>
</body>
</html>
<?php ob_end_flush(); ?>
