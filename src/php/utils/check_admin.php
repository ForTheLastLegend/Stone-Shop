<?php

declare(strict_types=1);

if (empty($_SESSION['admin'])) {
    header('Location: /admin/index_.php?page=login');
    exit;
}
