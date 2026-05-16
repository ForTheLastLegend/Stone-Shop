<?php

declare(strict_types=1);

class Root implements JsonSerializable
{
    public function __construct(
        public readonly int    $id_root,
        public readonly string $login_root
    ) {}

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
