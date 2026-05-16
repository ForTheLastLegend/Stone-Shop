<?php

declare(strict_types=1);

class MessageContact implements JsonSerializable
{
    public function __construct(
        public readonly int     $id_message,
        public readonly ?int    $id_client,
        public readonly string  $nom_contact,
        public readonly string  $email_contact,
        public readonly string  $sujet,
        public readonly string  $contenu,
        public readonly string  $date_envoi,
        public readonly bool    $traite
    ) {}

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
