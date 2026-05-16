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
// Pages publiques autorisées (whitelist)
$_pages = [
    'accueil', 'catalogue', 'fiche_produit', 'panier',
    'checkout', 'confirmation_commande', 'contact', 'recherche',
    'compare',
    'compte/login', 'compte/inscription', 'compte/profil',
    'compte/adresses', 'compte/historique_commandes',
    'compte/detail_commande', 'compte/mes_avis',
    'compte/liste_envie', 'compte/chat',
];

$page = $_GET['page'] ?? 'accueil';
if (!in_array($page, $_pages, true)) {
    $page = 'page_404';
}

$_pageFile = __DIR__ . '/content/' . $page . '.php';
if (!file_exists($_pageFile)) {
    $page      = 'page_404';
    $_pageFile = __DIR__ . '/content/page_404.php';
}

// Compteur panier pour le badge dans le header
$_panierDAO    = new PanierDAO($cnx);
$_nbPanier     = $_panierDAO->getNbArticles($_SESSION['id_session']);

// Compteur comparateur (stockage session-only, pas de DAO).
$_nbCompare = isset($_SESSION['compare']) && is_array($_SESSION['compare'])
    ? count($_SESSION['compare'])
    : 0;
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
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime(__DIR__ . '/assets/css/style.css') ?>">
    <link rel="stylesheet" href="assets/css/custom.css?v=<?= filemtime(__DIR__ . '/assets/css/custom.css') ?>">
</head>
<body data-csrf-token="<?= $_SESSION['csrf_token'] ?? '' ?>">

<?php require_once __DIR__ . '/src/php/utils/header.php'; ?>

<main class="container-fluid px-0">
    <?php require_once $_pageFile; ?>
</main>

<?php require_once __DIR__ . '/src/php/utils/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="src/js/ui.js?v=<?= time() ?>"></script>
<script src="src/js/panier.js?v=<?= time() ?>"></script>
<script src="src/js/chat_polling.js?v=<?= time() ?>"></script>
<script src="src/js/liste_envie.js?v=<?= time() ?>"></script>
<script src="src/js/comparateur.js?v=<?= time() ?>"></script>
</body>
</html>
<?php ob_end_flush(); ?>
