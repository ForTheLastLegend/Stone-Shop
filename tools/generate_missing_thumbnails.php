<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Script CLI uniquement.\n");
}

define('IS_ADMIN', true);
require_once __DIR__ . '/../src/php/utils/all_includes.php';

$_dir = ImageHelper::cheminUploadProduits();
if (!is_dir($_dir)) {
    exit("Dossier introuvable : {$_dir}\n");
}

$_generes = 0;
$_existants = 0;
$_erreurs   = 0;

foreach (glob($_dir . '*') as $_chemin) {
    if (!is_file($_chemin)) continue;
    $_nom = basename($_chemin);

    if (str_starts_with($_nom, 'thumb_')) {
        continue;
    }

    $_thumbAbs = $_dir . 'thumb_' . $_nom;
    if (is_file($_thumbAbs)) {
        $_existants++;
        continue;
    }

    if (ImageHelper::genererThumbnail($_chemin, 400)) {
        echo "Généré : thumb_{$_nom}\n";
        $_generes++;
    } else {
        echo "ERREUR : impossible de générer thumb_{$_nom}\n";
        $_erreurs++;
    }
}

echo "\n--- Bilan ---\n";
echo "Déjà présents : {$_existants}\n";
echo "Générés       : {$_generes}\n";
echo "Erreurs       : {$_erreurs}\n";
