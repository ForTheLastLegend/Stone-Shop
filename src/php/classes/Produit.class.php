<?php

declare(strict_types=1);

class Produit implements JsonSerializable
{
    public function __construct(
        public readonly int     $id_produit,
        public readonly ?int    $id_categorie,
        public readonly string  $nom_produit,
        public readonly ?string $description_courte,
        public readonly ?string $fiche_technique_url,
        public readonly bool    $actif
    ) {}

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
