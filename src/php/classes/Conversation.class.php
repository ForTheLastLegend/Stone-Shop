<?php

declare(strict_types=1);

class Conversation implements JsonSerializable
{
    public function __construct(
        public readonly int     $id_conversation,
        public readonly int     $id_client,
        public readonly ?int    $id_support,
        public readonly string  $sujet,
        public readonly string  $statut,
        public readonly string  $date_creation,
        public readonly ?string $date_fermeture
    ) {}

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
