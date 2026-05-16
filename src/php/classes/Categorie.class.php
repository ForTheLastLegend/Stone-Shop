<?php

declare(strict_types=1);

class Categorie implements JsonSerializable
{
    public function __construct(
        public readonly int     $id_categorie,
        public readonly string  $nom_categorie,
        public readonly ?int    $id_categorie_parent,
        public readonly ?string $image_categorie
    ) {}

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
