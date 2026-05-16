<?php

declare(strict_types=1);

require_once __DIR__ . '/../../src/php/utils/check_admin.php';
require_once __DIR__ . '/../../src/php/utils/_images.php';

$_idProduit = isset($_GET['id_produit']) ? (int) $_GET['id_produit'] : 0;
$_varDAO2 = new VarianteDAO($cnx);
$_produitDAO = new ProduitDAO($cnx);
$_imgDAO = new ImageProduitDAO($cnx);

$_succes = '';
$_erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_csrf();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_variante'])) {
    $_nom = trim($_POST['nom_variante'] ?? '');
    $_sku = trim($_POST['sku'] ?? '');
    $_prix = (float) str_replace(',', '.', $_POST['prix'] ?? '0');
    $_stock = (int) ($_POST['stock'] ?? 0);
    $_couleur = trim($_POST['couleur'] ?? '') ?: null;
    $_capacite = trim($_POST['capacite'] ?? '') ?: null;

    if ($_nom === '') $_erreurs[] = 'Nom requis.';
    if ($_sku === '') $_erreurs[] = 'SKU requis.';
    if ($_prix <= 0) $_erreurs[] = 'Prix invalide.';

    if (empty($_erreurs)) {
        $_ret = $_varDAO2->ajouterVariante(
            $_idProduit, $_nom, $_sku, $_prix, $_stock, $_couleur, $_capacite
        );
        if ($_ret > 0) {
            $_succes = 'Variante ajoutée.';
        } else {
            $_erreurs[] = 'Erreur (SKU déjà existant ?).';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_variante'])) {
    $_idVar = (int) ($_POST['id_variante'] ?? 0);
    if ($_idVar > 0) {
        $_urlsAvant = $_imgDAO->getUrlsByVariante($_idVar);
        $_ret = $_varDAO2->effacerVariante($_idVar);
        if ($_ret > 0) {
            foreach ($_urlsAvant as $_url) {
                supprimer_image_locale($_url);
            }
            $_succes = 'Variante supprimée.';
        } else {
            $_erreurs[] = 'Erreur suppression.';
        }
    }
}

$_produit = $_idProduit > 0 ? $_produitDAO->getProduitParId($_idProduit) : null;
$_variantes = $_idProduit > 0 ? ($_varDAO2->getVariantesByProduit($_idProduit) ?? []) : [];
?>

<div class="container-fluid p-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="/admin/index_.php?page=gestion_catalogue"
           class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h3 class="fw-bold mb-0">
            Variantes
            <?php if ($_produit): ?>
                — <?= htmlspecialchars($_produit->nom_produit) ?>
            <?php endif; ?>
        </h3>
        <?php if ($_idProduit > 0): ?>
            <button class="btn btn-primary ms-auto" data-bs-toggle="modal"
                    data-bs-target="#modalAjoutVariante">
                <i class="bi bi-plus-circle me-2"></i>Nouvelle variante
            </button>
        <?php endif; ?>
    </div>

    <?php if ($_succes): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_succes) ?></div>
    <?php endif; ?>
    <?php foreach ($_erreurs as $_e): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_e) ?></div>
    <?php endforeach; ?>

    <?php if ($_idProduit === 0): ?>
        <div class="alert alert-info">
            Sélectionnez un produit depuis
            <a href="/admin/index_.php?page=gestion_catalogue">le catalogue</a>.
        </div>
    <?php elseif (empty($_variantes)): ?>
        <div class="alert alert-info">Aucune variante pour ce produit.</div>
    <?php else: ?>
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="table-variantes">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nom variante</th>
                            <th>SKU</th>
                            <th class="text-end">
                                Prix (€)
                                <small class="text-muted fw-normal">(éditables)</small>
                            </th>
                            <th class="text-center">
                                Stock
                                <small class="text-muted fw-normal">(éditables)</small>
                            </th>
                            <th>Couleur</th>
                            <th>Capacité</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_variantes as $_v): ?>
                            <tr data-id="<?= (int) $_v->id_variante ?>">
                                <td class="text-muted small">
                                    <?= (int) $_v->id_variante ?>
                                </td>
                                <td class="fw-semibold">
                                    <?= htmlspecialchars($_v->nom_variante) ?>
                                </td>
                                <td>
                                    <code><?= htmlspecialchars($_v->sku) ?></code>
                                </td>
                                <td class="text-end">
                                    <span class="editable-cell"
                                          data-champ="prix"
                                          data-id="<?= (int) $_v->id_variante ?>"
                                          contenteditable="true">
                                        <?= number_format($_v->prix, 2, '.', '') ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="editable-cell"
                                          data-champ="stock"
                                          data-id="<?= (int) $_v->id_variante ?>"
                                          contenteditable="true">
                                        <?= (int) $_v->stock ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($_v->couleur ?? '—') ?></td>
                                <td><?= htmlspecialchars($_v->capacite ?? '—') ?></td>
                                <td class="text-center">
                                    <a href="/admin/index_.php?page=gestion_images&id_variante=<?= (int) $_v->id_variante ?>"
                                       class="btn btn-outline-secondary btn-sm me-1"
                                       title="Images">
                                        <i class="bi bi-image"></i>
                                    </a>
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="csrf_token"
                                               value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="id_variante"
                                               value="<?= (int) $_v->id_variante ?>">
                                        <button type="submit" name="supprimer_variante"
                                                class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('Supprimer cette variante ?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal ajout variante -->
<div class="modal fade" id="modalAjoutVariante" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <input type="hidden" name="csrf_token"
                   value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="id_produit" value="<?= $_idProduit ?>">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle variante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-8">
                        <label class="form-label fw-semibold">Nom</label>
                        <input type="text" name="nom_variante" class="form-control" required>
                    </div>
                    <div class="col-4">
                        <label class="form-label fw-semibold">SKU</label>
                        <input type="text" name="sku" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Prix (€)</label>
                        <input type="number" name="prix" class="form-control"
                               step="0.01" min="0.01" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Stock</label>
                        <input type="number" name="stock" class="form-control"
                               min="0" value="0" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">
                            Couleur <span class="text-muted small">(opt.)</span>
                        </label>
                        <input type="text" name="couleur" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">
                            Capacité <span class="text-muted small">(opt.)</span>
                        </label>
                        <input type="text" name="capacite" class="form-control"
                               placeholder="256 Go">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Annuler
                </button>
                <button type="submit" name="ajouter_variante" class="btn btn-primary">
                    Ajouter
                </button>
            </div>
        </form>
    </div>
</div>
