<?php

declare(strict_types=1);

class ListeEnvie implements JsonSerializable
{
    public function __construct(
        public readonly int    $id_liste_envie,
        public readonly string $id_session,
        public readonly ?int   $id_client,
        public readonly int    $id_variante,
        public readonly string $date_ajout
    ) {}

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
