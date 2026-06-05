<?php

declare(strict_types=1);

/**
 * Fichier central d'initialisation — inclus par chaque index_.php.
 *
 * Chaque index_.php doit définir IS_ADMIN (true/false) avant d'inclure ce fichier.
 * Exemple public : define('IS_ADMIN', false); require_once './src/php/utils/all_includes.php';
 * Exemple admin  : define('IS_ADMIN', true);  require_once '../src/php/utils/all_includes.php';
 */

// Chemin absolu vers /src/php — stable quel que soit l'appelant
$_srcPhp = __DIR__ . '/..';

// 1. Constantes de connexion DB
$_dbFile = $_srcPhp . '/db/db_pg_connect.php';
if (file_exists($_dbFile)) {
    require_once $_dbFile;
}

// 2. Autoloader
$_autoloaderFile = $_srcPhp . '/classes/Autoloader.class.php';
if (file_exists($_autoloaderFile)) {
    require_once $_autoloaderFile;
    Autoloader::register();
}

// 3. Instance PDO singleton — disponible globalement en $cnx
$cnx = Connexion::getInstance(DB_DSN, DB_USER, DB_PASS);

// Nettoyage des variables locales
unset($_srcPhp, $_dbFile, $_autoloaderFile);

// En-têtes de sécurité HTTP
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; script-src 'self' cdn.jsdelivr.net code.jquery.com; style-src 'self' cdn.jsdelivr.net fonts.googleapis.com 'unsafe-inline'; font-src 'self' cdn.jsdelivr.net fonts.gstatic.com data:; img-src 'self' data: blob:; connect-src 'self' cdn.jsdelivr.net; frame-ancestors 'none'");

// Génération du token CSRF une seule fois par session (logique dans Csrf::class)
Csrf::genererSiAbsent();

// Thin wrapper procédural — délègue à Csrf::verifier() pour les ~33 sites d'appel existants.
// Tout nouveau code doit préférer Csrf::verifier() directement.
function verifier_csrf(): void
{
    Csrf::verifier();
}
