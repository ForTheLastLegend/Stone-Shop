<?php

declare(strict_types=1);

if (empty($_SESSION['client'])) {
    header('Location: /index_.php?page=compte/login');
    exit;
}
