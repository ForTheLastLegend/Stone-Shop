<?php

declare(strict_types=1);

require_once __DIR__ . '/../../src/php/utils/check_admin.php';

$_catDAO2 = new CategorieDAO($cnx);
$_succes  = '';
$_erreurs = [];

// Un seul appel couvre toutes les branches POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_csrf();
}

// Ajout catégorie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_categorie'])) {
    $_nom    = trim($_POST['nom_categorie'] ?? '');
    $_parent = isset($_POST['id_parent']) && $_POST['id_parent'] !== ''
               ? (int) $_POST['id_parent'] : null;

    if ($_nom === '') {
        $_erreurs[] = 'Nom requis.';
    } else {
        $_ret = $_catDAO2->ajouterCategorie($_nom, $_parent, null);
        $_succes = $_ret > 0 ? 'Catégorie ajoutée.' : 'Erreur ajout.';
    }
}

// Suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_categorie'])) {
    $_idCat2 = (int) ($_POST['id_categorie'] ?? 0);
    if ($_idCat2 > 0) {
        $_ret = $_catDAO2->supprimerCategorie($_idCat2);
        $_succes = $_ret > 0 ? 'Catégorie supprimée.' : 'Impossible (produits liés ?).';
    }
}

$_categories = $_catDAO2->getAllCategories() ?? [];

// Map id -> nom pour afficher le nom de la catégorie parente sans requête supplémentaire.
$_nomsParCategorie = [];
foreach ($_categories as $_c) {
    $_nomsParCategorie[(int) $_c->id_categorie] = $_c->nom_categorie;
}
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Catégories</h3>
        <button class="btn btn-primary" data-bs-toggle="modal"
                data-bs-target="#modalAjoutCat">
            <i class="bi bi-plus-circle me-2"></i>Nouvelle catégorie
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
                        <th>Parente</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_categories as $_cat): ?>
                        <tr>
                            <td class="text-muted small">
                                <?= (int) $_cat->id_categorie ?>
                            </td>
                            <td><?= htmlspecialchars($_cat->nom_categorie) ?></td>
                            <td class="text-muted">
                                <?php if ($_cat->id_categorie_parent !== null): ?>
                                    <?= htmlspecialchars($_nomsParCategorie[$_cat->id_categorie_parent] ?? '—') ?>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="csrf_token"
                                           value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="id_categorie"
                                           value="<?= (int) $_cat->id_categorie ?>">
                                    <button type="submit" name="supprimer_categorie"
                                            class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('Supprimer cette catégorie ?')">
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
</div>

<!-- Modal ajout catégorie -->
<div class="modal fade" id="modalAjoutCat" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <input type="hidden" name="csrf_token"
                   value="<?= $_SESSION['csrf_token'] ?>">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle catégorie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nom</label>
                    <input type="text" name="nom_categorie" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Catégorie parente <span class="text-muted small">(opt.)</span>
                    </label>
                    <select name="id_parent" class="form-select">
                        <option value="">— Aucune (racine) —</option>
                        <?php foreach ($_categories as $_c): ?>
                            <option value="<?= (int) $_c->id_categorie ?>">
                                <?= htmlspecialchars($_c->nom_categorie) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Annuler</button>
                <button type="submit" name="ajouter_categorie"
                        class="btn btn-primary">Ajouter</button>
            </div>
        </form>
    </div>
</div>
