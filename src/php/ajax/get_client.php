<?php

declare(strict_types=1);

session_start();
define('IS_ADMIN', false);
require_once __DIR__ . '/../utils/all_includes.php';
header('Content-Type: application/json');

verifier_csrf();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['trouve' => false, 'erreur' => 'Méthode non autorisée.']);
    exit;
}

try {
    $email = trim($_GET['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['trouve' => false, 'erreur' => 'Email invalide.']);
        exit;
    }

    $clientDAO = new ClientDAO($cnx);
    $client = $clientDAO->getClientParEmail($email);

    if ($client) {
        echo json_encode([
            'trouve' => true,
            'prenom' => $client['prenom_client'] ?? ''
        ]);
    } else {
        echo json_encode([
            'trouve' => false
        ]);
    }

} catch (Exception $e) {
    echo json_encode(['trouve' => false, 'erreur' => 'Erreur serveur: ' . $e->getMessage()]);
}
