<?php

declare(strict_types=1);

session_start();
define('IS_ADMIN', true);
require_once __DIR__ . '/../utils/all_includes.php';

Csrf::verifier();

unset($_SESSION['admin'], $_SESSION['support']);
session_regenerate_id(true);

header('Location: /admin/index_.php?page=login');
exit;
