<?php

declare(strict_types=1);

session_start();
define('IS_ADMIN', false);
require_once __DIR__ . '/../utils/all_includes.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'erreur' => 'Méthode non autorisée.']);
    exit;
}

try {
    verifier_csrf();

    $action = $_POST['action'] ?? '';
    $idPanier = (int) ($_POST['id_panier'] ?? 0);
    $idVariante = (int) ($_POST['id_variante'] ?? 0);
    $qte = (int) ($_POST['qte'] ?? 0);

    if ($idPanier <= 0 || $idVariante <= 0 || !in_array($action, ['update', 'remove'], true)) {
        echo json_encode(['ok' => false, 'erreur' => 'Paramètres invalides.']);
        exit;
    }

    $panierDAO = new PanierDAO($cnx);

    if ($action === 'update') {
        if ($qte <= 0) {
            $panierDAO->retirerVariante($idPanier, $idVariante);
        } else {
            $panierDAO->updateQuantite($idPanier, $idVariante, $qte);
        }
    } elseif ($action === 'remove') {
        $panierDAO->retirerVariante($idPanier, $idVariante);
    }

    $idSession = $_SESSION['id_session'] ?? '';
    $nbArticles = $panierDAO->getNbArticles($idSession);
    $contenu = $panierDAO->getContenuPanier($idSession) ?? [];

    // vue_panier_complet ne renvoie pas toujours sous_total — fallback sur prix × qte
    $total = 0.0;
    foreach ($contenu as $ligne) {
        if (isset($ligne['sous_total'])) {
            $total += (float) $ligne['sous_total'];
        } else {
            $total += (float) ($ligne['prix'] ?? 0) * (int) ($ligne['quantite'] ?? 0);
        }
    }

    echo json_encode([
        'ok' => true,
        'nb' => $nbArticles,
        'total' => round($total, 2)
    ]);

} catch (Exception $e) {
    echo json_encode(['ok' => false, 'erreur' => 'Erreur serveur: ' . $e->getMessage()]);
}
