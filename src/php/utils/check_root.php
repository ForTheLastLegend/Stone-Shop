<?php

declare(strict_types=1);

if (empty($_SESSION['root'])) {
    header('Location: /root/index_.php?page=login');
    exit;
}
