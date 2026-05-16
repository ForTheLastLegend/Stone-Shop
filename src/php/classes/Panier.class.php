<?php

declare(strict_types=1);

/**
 * DTO Panier.
 * Fonctionne avec ou sans client connecté :
 *   - id_client = null → panier anonyme (visiteur)
 *   - id_client = int  → panier client (fusionné à la connexion)
 */
class Panier implements JsonSerializable
{
    public function __construct(
        public readonly int     $id_panier,
        public readonly string  $id_session,
        public readonly ?int    $id_client,
        public readonly string  $date_creation,
        public readonly string  $date_modification
    ) {}

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
