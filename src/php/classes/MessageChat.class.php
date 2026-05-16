<?php

declare(strict_types=1);

class MessageChat implements JsonSerializable
{
    public function __construct(
        public readonly int    $id_message,
        public readonly int    $id_conversation,
        public readonly string $expediteur_type,
        public readonly string $contenu,
        public readonly string $date_envoi,
        public readonly bool   $lu
    ) {}

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
