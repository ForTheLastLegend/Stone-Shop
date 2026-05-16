<?php

declare(strict_types=1);

session_start();
define('IS_ADMIN', true);
require_once __DIR__ . '/../utils/all_includes.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'erreur' => 'Méthode non autorisée.']);
    exit;
}

try {
    if (empty($_SESSION['admin']) && empty($_SESSION['support'])) {
        echo json_encode(['ok' => false, 'erreur' => 'Non autorisé.']);
        exit;
    }

    verifier_csrf();

    $id     = (int) ($_POST['id'] ?? 0);
    $statut = trim($_POST['statut'] ?? '');

    if ($id <= 0 || $statut === '') {
        echo json_encode(['ok' => false, 'erreur' => 'Paramètres manquants ou invalides.']);
        exit;
    }

    $cmdDAO = new CommandeDAO($cnx);
    $resultat = $cmdDAO->updateStatut($id, $statut);

    // Note: EXECUTE format() dans plpgsql ne met pas à jour FOUND, donc la DB renvoie toujours 0.
    if ($resultat >= 0) {
        echo json_encode(['ok' => true]);
    } else {
        echo json_encode(['ok' => false, 'erreur' => 'Mise à jour échouée.']);
    }

} catch (Exception $e) {
    echo json_encode(['ok' => false, 'erreur' => 'Erreur serveur: ' . $e->getMessage()]);
}
