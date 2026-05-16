<?php

declare(strict_types=1);

class Support implements JsonSerializable
{
    public function __construct(
        public readonly int    $id_support,
        public readonly string $nom_support,
        public readonly string $prenom_support,
        public readonly string $email_support
    ) {}

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
