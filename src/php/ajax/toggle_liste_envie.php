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

Csrf::verifier();

if (empty($_SESSION['client'])) {
    echo json_encode(['ok' => false, 'need_login' => true, 'erreur' => 'Connexion requise.']);
    exit;
}

$_idVariante = (int) ($_POST['id_variante'] ?? 0);
if ($_idVariante <= 0) {
    echo json_encode(['ok' => false, 'erreur' => 'Variante invalide.']);
    exit;
}

$_idSession = $_SESSION['id_session'];
$_idClient  = (int) $_SESSION['client']['id_client'];
$_listeDAO  = new ListeEnvieDAO($cnx);

if ($_listeDAO->estEnListe($_idSession, $_idVariante)) {
    $_listeDAO->retirerVariantePourSession($_idSession, $_idVariante);
    echo json_encode(['ok' => true, 'in_list' => false]);
} else {
    $_ret = $_listeDAO->ajouterVariante($_idSession, $_idVariante, $_idClient);
    echo json_encode(['ok' => $_ret > 0 || $_ret === -1, 'in_list' => true]);
}
