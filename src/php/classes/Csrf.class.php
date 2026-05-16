<?php

declare(strict_types=1);

/**
 * Gestion centralisée du jeton CSRF (cours TI2 §1.6 : aucune fonction hors classe).
 * Le wrapper procédural verifier_csrf() dans all_includes.php délègue ici pour
 * conserver la rétro-compatibilité sur les ~33 sites d'appel existants.
 */
final class Csrf
{
    private const SESSION_KEY = 'csrf_token';

    public static function genererSiAbsent(): void
    {
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }
    }

    public static function token(): string
    {
        return (string) ($_SESSION[self::SESSION_KEY] ?? '');
    }

    /**
     * Vérifie le token CSRF pour les requêtes POST. Coupe la requête en 403
     * si le jeton est absent ou invalide. À appeler en tête de chaque bloc POST.
     */
    public static function verifier(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            if (!hash_equals($_SESSION[self::SESSION_KEY] ?? '', $token)) {
                http_response_code(403);
                die('Token CSRF invalide.');
            }
        }
    }
}
