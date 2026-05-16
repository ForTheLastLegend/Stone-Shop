<?php

declare(strict_types=1);

/**
 * Autoloader PSR-style.
 * Cherche les classes dans le même dossier sous la forme NomClasse.class.php.
 * Appelé depuis all_includes.php : Autoloader::register();
 */
class Autoloader
{
    public static function register(): void
    {
        spl_autoload_register([__CLASS__, 'autoload']);
    }

    public static function autoload(string $class): void
    {
        $file = __DIR__ . '/' . $class . '.class.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
}
