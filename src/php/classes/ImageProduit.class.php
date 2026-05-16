<?php

declare(strict_types=1);

class ImageProduit implements JsonSerializable
{
    public function __construct(
        public readonly int     $id_image,
        public readonly int     $id_variante,
        public readonly string  $url_image,
        public readonly int     $ordre,
        public readonly ?string $alt_text
    ) {}

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
