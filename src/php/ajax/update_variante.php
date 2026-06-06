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

    Csrf::verifier();

    $id     = (int) ($_POST['id'] ?? 0);
    $champ  = trim($_POST['champ'] ?? '');
    $valeur = trim($_POST['valeur'] ?? '');

    if ($id <= 0 || $champ === '' || $valeur === '') {
        echo json_encode(['ok' => false, 'erreur' => 'Paramètres manquants ou invalides.']);
        exit;
    }

    $varDAO = new VarianteDAO($cnx);
    $resultat = $varDAO->updateChamp($id, $champ, $valeur);

    // EXECUTE format() en plpgsql ne met pas à jour FOUND, donc la fonction renvoie toujours 0
    // même en cas de succès. On accepte >= 0 ici (un échec SQL aurait levé une exception PDO).
    if ($resultat >= 0) {
        echo json_encode(['ok' => true]);
    } else {
        echo json_encode(['ok' => false, 'erreur' => 'Mise à jour échouée (valeur incorrecte ?).']);
    }

} catch (Exception $e) {
    error_log('[Stone Shop] update_variante: ' . $e->getMessage());
    echo json_encode(['ok' => false, 'erreur' => 'Erreur interne.']);
}
