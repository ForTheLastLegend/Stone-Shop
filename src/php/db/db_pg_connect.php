<?php

declare(strict_types=1);

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '5432');
define('DB_NAME', getenv('DB_NAME') ?: 'stone_shop');
define('DB_USER', getenv('DB_USER') ?: 'postgres');
define('DB_PASS', getenv('DB_PASS') ?: throw new \RuntimeException('Variable d\'environnement DB_PASS non définie'));
define('DB_DSN',  'pgsql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';port=' . DB_PORT);
