<?php

declare(strict_types=1);

class Promotion implements JsonSerializable
{
    public function __construct(
        public readonly int    $id_promotion,
        public readonly string $nom_promotion,
        public readonly float  $taux_reduction,
        public readonly string $date_debut,
        public readonly string $date_fin,
        public readonly bool   $actif
    ) {}

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
