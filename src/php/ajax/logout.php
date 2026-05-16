<?php

declare(strict_types=1);

session_start();
define('IS_ADMIN', false);
require_once __DIR__ . '/../utils/all_includes.php';

verifier_csrf();

unset($_SESSION['client']);
session_regenerate_id(true);

header('Location: /index_.php?page=accueil');
exit;
