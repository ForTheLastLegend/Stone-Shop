<?php

declare(strict_types=1);

class Adresse implements JsonSerializable
{
    public function __construct(
        public readonly int     $id_adresse,
        public readonly int     $id_client,
        public readonly string  $type_adresse,
        public readonly string  $nom_destinataire,
        public readonly string  $rue,
        public readonly string  $numero,
        public readonly ?string $boite,
        public readonly string  $code_postal,
        public readonly string  $ville,
        public readonly string  $pays
    ) {}

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
