<?php

declare(strict_types=1);

class Transporteur implements JsonSerializable
{
    public function __construct(
        public readonly int     $id_transporteur,
        public readonly string  $nom_transporteur,
        public readonly ?string $delai_estime,
        public readonly float   $frais_livraison,
        public readonly bool    $actif
    ) {}

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
