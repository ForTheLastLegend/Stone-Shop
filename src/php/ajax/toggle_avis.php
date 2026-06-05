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
    if (empty($_SESSION['admin'])) {
        echo json_encode(['ok' => false, 'erreur' => 'Non autorisé.']);
        exit;
    }

    verifier_csrf();

    $id     = (int) ($_POST['id'] ?? 0);
    $action = trim($_POST['action'] ?? '');

    if ($id <= 0 || !in_array($action, ['approuve', 'refuse'], true)) {
        echo json_encode(['ok' => false, 'erreur' => 'Paramètres manquants ou invalides.']);
        exit;
    }

    $avisDAO = new AvisDAO($cnx);
    $resultat = $avisDAO->modererAvis($id, $action);

    if ($resultat > 0) {
        echo json_encode(['ok' => true]);
    } else {
        echo json_encode(['ok' => false, 'erreur' => 'Mise à jour échouée.']);
    }

} catch (Exception $e) {
    error_log('[Stone Shop] toggle_avis: ' . $e->getMessage());
    echo json_encode(['ok' => false, 'erreur' => 'Erreur interne.']);
}
