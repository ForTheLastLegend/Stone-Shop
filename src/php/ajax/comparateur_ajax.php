<?php

declare(strict_types=1);

session_start();
define('IS_ADMIN', false);
require_once __DIR__ . '/../utils/all_includes.php';
header('Content-Type: application/json');

// Limite UX raisonnable pour la mise en regard côte à côte.
const COMPARE_MAX = 4;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'erreur' => 'Méthode non autorisée.']);
    exit;
}

try {
    verifier_csrf();

    $_action = (string) ($_POST['action'] ?? 'toggle');
    if (!in_array($_action, ['toggle', 'vider'], true)) {
        echo json_encode(['ok' => false, 'erreur' => 'Action inconnue.']);
        exit;
    }

    if (!isset($_SESSION['compare']) || !is_array($_SESSION['compare'])) {
        $_SESSION['compare'] = [];
    }

    if ($_action === 'vider') {
        $_SESSION['compare'] = [];
        echo json_encode(['ok' => true, 'nb' => 0]);
        exit;
    }

    $_idVariante = (int) ($_POST['id_variante'] ?? 0);
    if ($_idVariante <= 0) {
        echo json_encode(['ok' => false, 'erreur' => 'Variante invalide.']);
        exit;
    }

    $_pos = array_search($_idVariante, $_SESSION['compare'], true);
    if ($_pos !== false) {
        array_splice($_SESSION['compare'], (int) $_pos, 1);
        echo json_encode([
            'ok' => true,
            'in_compare' => false,
            'nb' => count($_SESSION['compare']),
        ]);
        exit;
    }

    if (count($_SESSION['compare']) >= COMPARE_MAX) {
        echo json_encode([
            'ok' => false,
            'plein' => true,
            'nb' => count($_SESSION['compare']),
            'erreur' => 'Comparateur plein (' . COMPARE_MAX . ' produits maximum).',
        ]);
        exit;
    }

    $_SESSION['compare'][] = $_idVariante;
    echo json_encode([
        'ok' => true,
        'in_compare' => true,
        'nb' => count($_SESSION['compare']),
    ]);

} catch (Exception $e) {
    echo json_encode(['ok' => false, 'erreur' => 'Erreur serveur : ' . $e->getMessage()]);
}
