<?php

declare(strict_types=1);

if (empty($_SESSION['admin']) && empty($_SESSION['support'])) {
    header('Location: /admin/index_.php?page=login');
    exit;
}
