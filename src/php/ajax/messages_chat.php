<?php

declare(strict_types=1);

session_start();
define('IS_ADMIN', false);
require_once __DIR__ . '/../utils/all_includes.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['ok' => false, 'erreur' => 'Méthode non autorisée.']);
    exit;
}

$_idConv    = (int) ($_GET['id_conv']    ?? 0);
$_dernierId = (int) ($_GET['dernier_id'] ?? 0);

if ($_idConv <= 0) {
    echo json_encode(['ok' => false, 'erreur' => 'Paramètre manquant.']);
    exit;
}

$_convDAO = new ConversationDAO($cnx);

if (!empty($_SESSION['admin']) || !empty($_SESSION['support'])) {
    $_autorise = true;
} elseif (!empty($_SESSION['client'])) {
    $_conv = $_convDAO->getConversationParId($_idConv);
    $_autorise = $_conv !== null
        && (int) $_conv['id_client'] === (int) $_SESSION['client']['id_client'];
} else {
    $_autorise = false;
}

if (!$_autorise) {
    echo json_encode(['ok' => false, 'erreur' => 'Non autorisé.']);
    exit;
}

$_msgDAO   = new MessageChatDAO($cnx);
$_messages = $_msgDAO->getMessagesDepuis($_idConv, $_dernierId) ?? [];

echo json_encode(['ok' => true, 'messages' => $_messages]);
