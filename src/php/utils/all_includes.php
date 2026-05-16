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

// Génération du token CSRF une seule fois par session (logique dans Csrf::class)
Csrf::genererSiAbsent();

// Thin wrapper procédural — délègue à Csrf::verifier() pour les ~33 sites d'appel existants.
// Tout nouveau code doit préférer Csrf::verifier() directement.
function verifier_csrf(): void
{
    Csrf::verifier();
}
