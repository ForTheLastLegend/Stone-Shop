<?php

declare(strict_types=1);

/**
 * Centralisation du hashage de mot de passe (cours TI2 §1.6 : aucune fonction
 * hors classe). Force PASSWORD_ARGON2ID partout — un hash bcrypt apparaissant
 * un jour pour un compte sans cleartext connu nécessite un parcours reset
 * password par lien email, pas de migration algorithmique.
 *
 * Pattern aligné sur Csrf et ImageHelper (cf. RULES_V2 §3).
 */
final class Password
{
    public static function hash(string $clear): string
    {
        return password_hash($clear, PASSWORD_ARGON2ID);
    }

    public static function verify(string $clear, string $hash): bool
    {
        return password_verify($clear, $hash);
    }
}
