<?php

declare(strict_types=1);

/**
 * Manipulation des images produit.
 *
 * GD locale ne supporte pas WebP : l'upload est filtré dynamiquement via
 * formatSupporte() (cf. function_exists dans gestion_images).
 */
final class ImageHelper
{
    public const RELATIVE_DIR = 'assets/images/produits/';

    public static function racineProjet(): string
    {
        return realpath(__DIR__ . '/../../..') ?: dirname(__DIR__, 3);
    }

    public static function cheminUploadProduits(): string
    {
        return self::racineProjet() . DIRECTORY_SEPARATOR
             . str_replace('/', DIRECTORY_SEPARATOR, self::RELATIVE_DIR);
    }

    /**
     * Indique si un type d'image est supporté en lecture ET en écriture par la GD locale.
     */
    public static function formatSupporte(int $type): bool
    {
        return match ($type) {
            IMAGETYPE_JPEG => function_exists('imagecreatefromjpeg') && function_exists('imagejpeg'),
            IMAGETYPE_PNG  => function_exists('imagecreatefrompng')  && function_exists('imagepng'),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') && function_exists('imagewebp'),
            default        => false,
        };
    }

    /**
     * Redimensionne l'image en place pour que sa plus grande dimension <= $maxDim.
     * Downscale par paliers de 2x pour éviter le moiré sur les textures fines.
     */
    public static function redimensionner(string $cheminAbsolu, int $maxDim = 1500): bool
    {
        $info = @getimagesize($cheminAbsolu);
        if ($info === false) {
            return false;
        }
        [$w, $h, $type] = $info;
        if (!self::formatSupporte($type)) {
            return false;
        }
        if ($w <= $maxDim && $h <= $maxDim) {
            return true;
        }

        $ratio = $w / $h;
        if ($w > $h) {
            $finalW = $maxDim;
            $finalH = (int) round($maxDim / $ratio);
        } else {
            $finalH = $maxDim;
            $finalW = (int) round($maxDim * $ratio);
        }

        $src = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($cheminAbsolu),
            IMAGETYPE_PNG  => @imagecreatefrompng($cheminAbsolu),
            IMAGETYPE_WEBP => @imagecreatefromwebp($cheminAbsolu),
        };
        if ($src === false) {
            return false;
        }

        $courant = $src;
        $curW    = $w;
        $curH    = $h;
        while ($curW / 2 > $finalW && $curH / 2 > $finalH) {
            $stepW = (int) ($curW / 2);
            $stepH = (int) ($curH / 2);
            $step  = self::canvasImage($stepW, $stepH, $type);
            imagecopyresampled($step, $courant, 0, 0, 0, 0, $stepW, $stepH, $curW, $curH);
            if ($courant !== $src) {
                imagedestroy($courant);
            }
            $courant = $step;
            $curW    = $stepW;
            $curH    = $stepH;
        }

        $dst = self::canvasImage($finalW, $finalH, $type);
        imagecopyresampled($dst, $courant, 0, 0, 0, 0, $finalW, $finalH, $curW, $curH);

        if ($courant !== $src) {
            imagedestroy($courant);
        }
        imagedestroy($src);

        $ok = match ($type) {
            IMAGETYPE_JPEG => imagejpeg($dst, $cheminAbsolu, 90),
            IMAGETYPE_PNG  => imagepng($dst, $cheminAbsolu, 6),
            IMAGETYPE_WEBP => imagewebp($dst, $cheminAbsolu, 90),
        };

        imagedestroy($dst);
        return $ok;
    }

    /**
     * Crée une miniature côté disque, suffixée `thumb_`, en réutilisant l'algo
     * multi-passes pour éviter le moiré au resize.
     */
    public static function genererThumbnail(string $cheminAbsolu, int $maxDim = 400): bool
    {
        $dir   = dirname($cheminAbsolu);
        $nom   = basename($cheminAbsolu);
        $thumb = $dir . DIRECTORY_SEPARATOR . 'thumb_' . $nom;

        if (!@copy($cheminAbsolu, $thumb)) {
            return false;
        }
        if (!self::redimensionner($thumb, $maxDim)) {
            @unlink($thumb);
            return false;
        }
        return true;
    }

    /**
     * Renvoie l'URL de la miniature si elle existe sur disque, sinon l'URL
     * d'origine. Permet aux templates de swap automatiquement vers le thumb
     * pour les affichages réduits (cards, listings) sans casser le legacy.
     */
    public static function urlThumbnail(string $urlImage): string
    {
        $url = ltrim($urlImage, '/');
        if (!str_starts_with($url, self::RELATIVE_DIR)) {
            return $urlImage;
        }
        $nom      = basename($url);
        $thumbAbs = self::cheminUploadProduits() . 'thumb_' . $nom;
        if (is_file($thumbAbs)) {
            return self::RELATIVE_DIR . 'thumb_' . $nom;
        }
        return $urlImage;
    }

    /**
     * Supprime un fichier image en local à partir de son URL relative.
     * Purge également la miniature `thumb_` si présente.
     * Garde-fou : le chemin résolu doit rester sous assets/images/produits/.
     */
    public static function supprimerLocale(string $urlImage): bool
    {
        $url = ltrim($urlImage, '/');
        if (!str_starts_with($url, self::RELATIVE_DIR)) {
            return false;
        }

        $base = realpath(self::cheminUploadProduits());
        $abs  = realpath(self::racineProjet() . DIRECTORY_SEPARATOR
                         . str_replace('/', DIRECTORY_SEPARATOR, $url));

        if ($abs === false || $base === false) {
            return false;
        }
        if (!str_starts_with($abs, $base)) {
            return false;
        }

        $ok       = @unlink($abs);
        $thumbAbs = dirname($abs) . DIRECTORY_SEPARATOR . 'thumb_' . basename($abs);
        if (is_file($thumbAbs)) {
            @unlink($thumbAbs);
        }
        return $ok;
    }

    /**
     * Crée un canvas truecolor avec gestion de la transparence selon le format.
     */
    private static function canvasImage(int $w, int $h, int $type): \GdImage
    {
        $img = imagecreatetruecolor($w, $h);
        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
            imagealphablending($img, false);
            imagesavealpha($img, true);
            $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
            imagefilledrectangle($img, 0, 0, $w, $h, $transparent);
        }
        return $img;
    }
}
