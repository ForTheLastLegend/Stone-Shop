<?php

declare(strict_types=1);

/**
 * Singleton de connexion PDO à PostgreSQL.
 * Usage : $cnx = Connexion::getInstance(DSN, USER, PASS);
 */
class Connexion
{
    private static ?PDO $_instance = null;

    private function __construct() {}

    public static function getInstance(string $dsn, string $user, string $pass): PDO
    {
        if (self::$_instance === null) {
            self::$_instance = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
        return self::$_instance;
    }
}
