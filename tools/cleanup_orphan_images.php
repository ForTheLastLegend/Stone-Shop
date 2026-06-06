<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Script CLI uniquement.\n");
}

define('IS_ADMIN', true);
require_once __DIR__ . '/../src/php/utils/all_includes.php';

$_imgDAO = new ImageProduitDAO($cnx);

$_dir = ImageHelper::cheminUploadProduits();
if (!is_dir($_dir)) {
    exit("Dossier introuvable : {$_dir}\n");
}

$_urlsDB = array_map(
    fn(string $u) => basename($u),
    $_imgDAO->getAllUrls()
);
$_urlsDB = array_flip($_urlsDB);

$_supprimes = 0;
$_conserves = 0;
$_erreurs   = 0;

foreach (glob($_dir . '*') as $_chemin) {
    if (!is_file($_chemin)) continue;
    $_nom = basename($_chemin);

    $_nomReference = str_starts_with($_nom, 'thumb_') ? substr($_nom, 6) : $_nom;
    if (isset($_urlsDB[$_nomReference])) {
        $_conserves++;
        continue;
    }

    if (@unlink($_chemin)) {
        echo "Supprimé : {$_nom}\n";
        $_supprimes++;
    } else {
        echo "ERREUR : impossible de supprimer {$_nom}\n";
        $_erreurs++;
    }
}

echo "\n--- Bilan ---\n";
echo "Conservés : {$_conserves}\n";
echo "Supprimés : {$_supprimes}\n";
echo "Erreurs   : {$_erreurs}\n";
