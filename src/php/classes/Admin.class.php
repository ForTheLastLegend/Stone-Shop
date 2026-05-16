<?php

declare(strict_types=1);

class Admin implements JsonSerializable
{
    public function __construct(
        public readonly int    $id_admin,
        public readonly string $nom_admin,
        public readonly string $prenom_admin,
        public readonly string $email_admin
    ) {}

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
