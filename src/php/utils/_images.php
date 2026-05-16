<?php

declare(strict_types=1);

/**
 * Façades procédurales — délèguent à ImageHelper::* (cours TI2 §1.6).
 * Conservées pour rétro-compatibilité sur les ~11 fichiers qui appellent ces
 * fonctions. Tout nouveau code doit préférer ImageHelper::* directement.
 */

const PRODUITS_UPLOAD_REL = 'assets/images/produits/';

function racine_projet(): string
{
    return ImageHelper::racineProjet();
}

function chemin_upload_produits(): string
{
    return ImageHelper::cheminUploadProduits();
}

function format_image_supporte(int $type): bool
{
    return ImageHelper::formatSupporte($type);
}

function redimensionner_image(string $cheminAbsolu, int $maxDim = 1500): bool
{
    return ImageHelper::redimensionner($cheminAbsolu, $maxDim);
}

function generer_thumbnail(string $cheminAbsolu, int $maxDim = 400): bool
{
    return ImageHelper::genererThumbnail($cheminAbsolu, $maxDim);
}

function url_thumbnail(string $urlImage): string
{
    return ImageHelper::urlThumbnail($urlImage);
}

function supprimer_image_locale(string $urlImage): bool
{
    return ImageHelper::supprimerLocale($urlImage);
}
