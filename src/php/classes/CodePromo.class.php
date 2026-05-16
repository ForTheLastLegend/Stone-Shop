<?php

declare(strict_types=1);

class CodePromo implements JsonSerializable
{
    public function __construct(
        public readonly int    $id_code_promo,
        public readonly string $code,
        public readonly float  $taux_reduction,
        public readonly string $date_debut,
        public readonly string $date_fin,
        public readonly ?int   $usage_max,
        public readonly int    $usage_actuel,
        public readonly bool   $actif
    ) {}

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
