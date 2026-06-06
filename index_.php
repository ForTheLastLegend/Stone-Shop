<?php

declare(strict_types=1);

ob_start();
session_start();

// Identifiant de session anonyme — utilisé pour le panier et la liste d'envie
if (empty($_SESSION['id_session'])) {
    $_SESSION['id_session'] = session_id();
}

define('IS_ADMIN', false);
require_once __DIR__ . '/src/php/utils/all_includes.php';

// Whitelists des pages autorisées (centralisées dans config/pages.php)
require_once __DIR__ . '/config/pages.php';

$page = $_GET['page'] ?? 'accueil';
if (!in_array($page, $_pages, true)) {
    $page = 'page_404';
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
    <title>Stone Shop</title>
    <!-- Google Fonts — chargé en <link> natif pour fiabilité maximale -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Roboto:wght@300;400;500;700&display=swap">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          integrity="sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+"
          crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime(__DIR__ . '/assets/css/style.css') ?>">
    <link rel="stylesheet" href="assets/css/custom.css?v=<?= filemtime(__DIR__ . '/assets/css/custom.css') ?>">
</head>
<body data-csrf-token="<?= $_SESSION['csrf_token'] ?? '' ?>">

<?php require_once __DIR__ . '/src/php/utils/header.php'; ?>

<main class="container-fluid px-0">
    <?php require_once $_pageFile; ?>
</main>

<?php require_once __DIR__ . '/src/php/utils/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous"></script>
<script src="assets/js/ui.js?v=<?= filemtime(__DIR__ . '/assets/js/ui.js') ?>"></script>
<script src="assets/js/panier.js?v=<?= filemtime(__DIR__ . '/assets/js/panier.js') ?>"></script>
<script src="assets/js/chat_polling.js?v=<?= filemtime(__DIR__ . '/assets/js/chat_polling.js') ?>"></script>
<script src="assets/js/liste_envie.js?v=<?= filemtime(__DIR__ . '/assets/js/liste_envie.js') ?>"></script>
<script src="assets/js/comparateur.js?v=<?= filemtime(__DIR__ . '/assets/js/comparateur.js') ?>"></script>
</body>
</html>
<?php ob_end_flush(); ?>
