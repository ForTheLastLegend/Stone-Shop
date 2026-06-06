<?php

declare(strict_types=1);

require_once __DIR__ . '/../../src/php/utils/check_admin.php';

$_imgDAO    = new ImageProduitDAO($cnx);
$_idVariante = isset($_GET['id_variante']) ? (int) $_GET['id_variante'] : 0;
$_succes    = isset($_GET['succes']) ? 'Image ajoutée.' : '';
$_erreurs   = [];

// Un seul appel couvre les deux branches POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::verifier();
}

// Ajout image avec upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_image'])) {
    $_idVar  = (int) ($_POST['id_variante'] ?? 0);
    $_alt    = trim($_POST['alt_text'] ?? '');
    $_ordre  = (int) ($_POST['ordre'] ?? 1);

    $_uploadDir = ImageHelper::cheminUploadProduits();

    if (empty($_FILES['fichier']['name'])) {
        $_erreurs[] = 'Fichier requis.';
    } else {
        $_ext      = strtolower(pathinfo($_FILES['fichier']['name'], PATHINFO_EXTENSION));
        $_allowed  = ['jpg', 'jpeg', 'png'];
        if (function_exists('imagecreatefromwebp') && function_exists('imagewebp')) {
            $_allowed[] = 'webp';
        }
        $_maxSize  = 10 * 1024 * 1024;

        if (!in_array($_ext, $_allowed, true)) {
            $_erreurs[] = 'Format non autorisé (' . implode(', ', $_allowed) . ').';
        } elseif ($_FILES['fichier']['size'] > $_maxSize) {
            $_erreurs[] = 'Fichier trop lourd (max 10 Mo).';
        } else {
            $_filename = uniqid('img_', true) . '.' . $_ext;
            $_destAbs  = $_uploadDir . $_filename;
            if (move_uploaded_file($_FILES['fichier']['tmp_name'], $_destAbs)) {
                ImageHelper::redimensionner($_destAbs, 1500);
                ImageHelper::genererThumbnail($_destAbs, 400);

                $_url = ImageHelper::RELATIVE_DIR . $_filename;
                $_ret = $_imgDAO->ajouterImage($_idVar, $_url, $_ordre, $_alt ?: null);
                if ($_ret > 0) {
                    header('Location: /admin/index_.php?page=gestion_images&id_variante=' . $_idVar . '&succes=1');
                    exit;
                }
                @unlink($_destAbs);
                $_erreurs[] = 'Erreur ajout en base.';
            } else {
                $_erreurs[] = 'Échec du déplacement du fichier.';
            }
        }
    }
}

// Suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_image'])) {
    $_idImg  = (int) ($_POST['id_image']   ?? 0);
    $_urlImg = trim($_POST['url_image']    ?? '');
    if ($_idImg > 0) {
        $_imgDAO->supprimerImage($_idImg);
        if ($_urlImg !== '') {
            ImageHelper::supprimerLocale($_urlImg);
        }
        $_succes = 'Image supprimée.';
    }
}

$_images   = $_idVariante > 0 ? ($_imgDAO->getImagesByVariante($_idVariante) ?? []) : [];
$_libelle  = '';
$_idProdHeader = 0;
if ($_idVariante > 0) {
    $_varDAO3 = new VarianteDAO($cnx);
    $_row     = $_varDAO3->getVarianteCatalogueParId($_idVariante);
    if ($_row !== null) {
        $_libelle      = $_row['nom_produit'] . ' — ' . $_row['nom_variante'];
        $_idProdHeader = (int) $_row['id_produit'];
    }
}
?>

<div class="container-fluid p-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="/admin/index_.php?page=gestion_variantes<?= $_idProdHeader > 0 ? '&id_produit=' . $_idProdHeader : '' ?>"
           class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h3 class="fw-bold mb-0">
            Images
            <?= $_libelle !== '' ? '— ' . htmlspecialchars($_libelle) : '' ?>
        </h3>
        <?php if ($_idVariante > 0): ?>
            <button class="btn btn-primary ms-auto" data-bs-toggle="modal"
                    data-bs-target="#modalAjoutImg">
                <i class="bi bi-plus-circle me-2"></i>Ajouter une image
            </button>
        <?php endif; ?>
    </div>

    <?php if ($_succes): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_succes) ?></div>
    <?php endif; ?>
    <?php foreach ($_erreurs as $_e): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_e) ?></div>
    <?php endforeach; ?>

    <?php if ($_idVariante === 0): ?>
        <div class="alert alert-info">
            Accédez aux images depuis
            <a href="/admin/index_.php?page=gestion_variantes">la gestion des variantes</a>.
        </div>
    <?php elseif (empty($_images)): ?>
        <div class="alert alert-info">Aucune image pour cette variante.</div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($_images as $_img): ?>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm">
                        <img src="/<?= htmlspecialchars(ltrim($_img->url_image, '/')) ?>"
                             class="card-img-top ss-thumb-160-cover"
                             alt="<?= htmlspecialchars($_img->alt_text ?? '') ?>">
                        <div class="card-body p-2">
                            <small class="text-muted d-block">
                                Ordre : <?= (int) $_img->ordre ?>
                            </small>
                            <small class="text-muted text-truncate d-block">
                                <?= htmlspecialchars($_img->alt_text ?? '') ?>
                            </small>
                        </div>
                        <div class="card-footer p-2 bg-white">
                            <form method="post" data-confirm="Supprimer cette image ?">
                                <input type="hidden" name="csrf_token"
                                       value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="id_image"
                                       value="<?= (int) $_img->id_image ?>">
                                <input type="hidden" name="url_image"
                                       value="<?= htmlspecialchars($_img->url_image) ?>">
                                <button type="submit" name="supprimer_image"
                                        class="btn btn-outline-danger btn-sm w-100">
                                    <i class="bi bi-trash me-1"></i>Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal ajout image -->
<div class="modal fade" id="modalAjoutImg" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" enctype="multipart/form-data" class="modal-content">
            <input type="hidden" name="id_variante" value="<?= $_idVariante ?>">
            <input type="hidden" name="csrf_token"
                   value="<?= $_SESSION['csrf_token'] ?>">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter une image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?php
                    $_acceptList = ['.jpg', '.jpeg', '.png'];
                    if (function_exists('imagecreatefromwebp') && function_exists('imagewebp')) {
                        $_acceptList[] = '.webp';
                    }
                ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Fichier image</label>
                    <input type="file" name="fichier" class="form-control"
                           accept="<?= implode(',', $_acceptList) ?>" required>
                    <div class="form-text">
                        <?= strtoupper(implode(', ', array_map(fn($e) => ltrim($e, '.'), $_acceptList))) ?> — max 10 Mo
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Texte alternatif <span class="text-muted small">(opt.)</span>
                    </label>
                    <input type="text" name="alt_text" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Ordre d'affichage</label>
                    <input type="number" name="ordre" class="form-control"
                           value="1" min="1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Annuler</button>
                <button type="submit" name="ajouter_image"
                        class="btn btn-primary">Ajouter</button>
            </div>
        </form>
    </div>
</div>
