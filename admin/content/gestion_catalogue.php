<?php

declare(strict_types=1);

require_once __DIR__ . '/../../src/php/utils/check_admin.php';
require_once __DIR__ . '/../../src/php/utils/_images.php';

$_produitDAO = new ProduitDAO($cnx);
$_catDAO2    = new CategorieDAO($cnx);
$_imgDAO     = new ImageProduitDAO($cnx);

$_succes  = '';
$_erreurs = [];

// Un seul appel couvre toutes les branches POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_csrf();
}

// Ajout produit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_produit'])) {
    $_nom     = trim($_POST['nom_produit']    ?? '');
    $_desc    = trim($_POST['description']    ?? '');
    $_idCat   = (int) ($_POST['id_categorie'] ?? 0);

    if ($_nom === '')  $_erreurs[] = 'Nom requis.';
    if ($_idCat === 0) $_erreurs[] = 'Catégorie requise.';

    if (empty($_erreurs)) {
        $_ret = $_produitDAO->ajouterProduit($_idCat, $_nom, $_desc ?: '', null);
        if ($_ret > 0) {
            $_succes = 'Produit ajouté.';
        } else {
            $_erreurs[] = 'Erreur lors de l\'ajout.';
        }
    }
}

// Suppression produit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_produit'])) {
    $_idProd = (int) ($_POST['id_produit'] ?? 0);
    if ($_idProd > 0) {
        $_urlsAvant = $_imgDAO->getUrlsByProduit($_idProd);
        $_ret = $_produitDAO->effacerProduit($_idProd);
        if ($_ret > 0) {
            foreach ($_urlsAvant as $_url) {
                supprimer_image_locale($_url);
            }
            $_succes = 'Produit supprimé.';
        } else {
            $_erreurs[] = 'Erreur suppression.';
        }
    }
}

// Toggle actif
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_actif'])) {
    $_idProd = (int) ($_POST['id_produit'] ?? 0);
    $_actif  = $_POST['actif'] ?? '0';
    $_produitDAO->updateChamp($_idProd, 'actif', $_actif === '1' ? 'false' : 'true');
    $_succes = 'Statut mis à jour.';
}

$_produits   = $_produitDAO->getAllProduits() ?? [];
$_categories = $_catDAO2->getAllCategories() ?? [];

// Map id_categorie -> nom_categorie pour afficher le nom dans le tableau.
$_nomsCategories = [];
foreach ($_categories as $_c) {
    $_nomsCategories[(int) $_c->id_categorie] = $_c->nom_categorie;
}
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Gestion du catalogue</h3>
        <button class="btn btn-primary" data-bs-toggle="modal"
                data-bs-target="#modalAjoutProduit">
            <i class="bi bi-plus-circle me-2"></i>Nouveau produit
        </button>
    </div>

    <?php if ($_succes): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_succes) ?></div>
    <?php endif; ?>
    <?php foreach ($_erreurs as $_e): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_e) ?></div>
    <?php endforeach; ?>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th class="text-center">Actif</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($_produits)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Aucun produit.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($_produits as $_p): ?>
                            <tr>
                                <td class="text-muted small">
                                    <?= (int) $_p->id_produit ?>
                                </td>
                                <td class="fw-semibold">
                                    <?= htmlspecialchars($_p->nom_produit) ?>
                                </td>
                                <td class="text-muted">
                                    <?= htmlspecialchars($_nomsCategories[(int) $_p->id_categorie] ?? '—') ?>
                                </td>
                                <td class="text-center">
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="csrf_token"
                                               value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="id_produit"
                                               value="<?= (int) $_p->id_produit ?>">
                                        <input type="hidden" name="actif"
                                               value="<?= $_p->actif ? '1' : '0' ?>">
                                        <button type="submit" name="toggle_actif"
                                                class="btn btn-sm btn-link p-0"
                                                title="Basculer actif">
                                            <i class="bi bi-toggle-<?= $_p->actif ? 'on text-success' : 'off text-secondary' ?> fs-4"></i>
                                        </button>
                                    </form>
                                </td>
                                <td class="text-center">
                                    <a href="/admin/index_.php?page=gestion_variantes&id_produit=<?= (int) $_p->id_produit ?>"
                                       class="btn btn-outline-primary btn-sm me-1"
                                       title="Variantes">
                                        <i class="bi bi-box-seam"></i>
                                    </a>
                                    <form method="post" class="d-inline"
                                          data-confirm="Supprimer ce produit et toutes ses variantes ?">
                                        <input type="hidden" name="csrf_token"
                                               value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="id_produit"
                                               value="<?= (int) $_p->id_produit ?>">
                                        <button type="submit" name="supprimer_produit"
                                                class="btn btn-outline-danger btn-sm"
                                                title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal ajout produit -->
<div class="modal fade" id="modalAjoutProduit" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <input type="hidden" name="csrf_token"
                   value="<?= $_SESSION['csrf_token'] ?>">
            <div class="modal-header">
                <h5 class="modal-title">Nouveau produit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nom du produit</label>
                    <input type="text" name="nom_produit" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Catégorie</label>
                    <select name="id_categorie" class="form-select" required>
                        <option value="">— Choisir —</option>
                        <?php foreach ($_categories as $_cat): ?>
                            <option value="<?= (int) $_cat->id_categorie ?>">
                                <?= htmlspecialchars($_cat->nom_categorie) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Annuler
                </button>
                <button type="submit" name="ajouter_produit" class="btn btn-primary">
                    Ajouter
                </button>
            </div>
        </form>
    </div>
</div>
