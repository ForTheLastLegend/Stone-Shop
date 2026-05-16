<?php

declare(strict_types=1);

/**
 * DTO VarianteProduit.
 * ATTENTION : toute la logique stock / prix / image est portée par cette classe,
 * pas par Produit.
 */
class Variante implements JsonSerializable
{
    public function __construct(
        public readonly int     $id_variante,
        public readonly int     $id_produit,
        public readonly string  $nom_variante,
        public readonly string  $sku,
        public readonly float   $prix,
        public readonly int     $stock,
        public readonly ?string $couleur,
        public readonly ?string $capacite
    ) {}

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
