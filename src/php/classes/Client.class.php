<?php

declare(strict_types=1);

class Client implements JsonSerializable
{
    public function __construct(
        public readonly int     $id_client,
        public readonly string  $nom_client,
        public readonly string  $prenom_client,
        public readonly string  $email_client,
        public readonly ?string $telephone,
        public readonly string  $date_inscription
    ) {}

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
