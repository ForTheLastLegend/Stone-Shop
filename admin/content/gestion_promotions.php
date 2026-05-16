<?php

declare(strict_types=1);

require_once __DIR__ . '/../../src/php/utils/check_admin.php';

$_promoDAO = new PromotionDAO($cnx);
$_varDAO2  = new VarianteDAO($cnx);
$_succes   = '';
$_erreurs  = [];

// Un seul appel couvre toutes les branches POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_csrf();
}

// Ajout promotion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_promo'])) {
    $_nom   = trim($_POST['nom_promotion'] ?? '');
    $_taux  = (float) str_replace(',', '.', $_POST['taux_reduction'] ?? '0');
    $_debut = trim($_POST['date_debut'] ?? '');
    $_fin   = trim($_POST['date_fin']   ?? '');

    if ($_nom  === '')  $_erreurs[] = 'Nom requis.';
    if ($_taux <= 0)    $_erreurs[] = 'Taux invalide.';
    if ($_debut === '') $_erreurs[] = 'Date de début requise.';
    if ($_fin   === '') $_erreurs[] = 'Date de fin requise.';

    if (empty($_erreurs)) {
        $_ret = $_promoDAO->ajouterPromotion($_nom, $_taux, $_debut, $_fin);
        $_succes = $_ret > 0 ? 'Promotion créée.' : 'Erreur création.';
    }
}

// Suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_promo'])) {
    $_idPromo = (int) ($_POST['id_promotion'] ?? 0);
    if ($_idPromo > 0) {
        $_promoDAO->supprimerPromotion($_idPromo);
        $_succes = 'Promotion supprimée.';
    }
}

// Association variante ↔ promotion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['associer_variante'])) {
    $_idPromo = (int) ($_POST['id_promotion'] ?? 0);
    $_idVar   = (int) ($_POST['id_variante']  ?? 0);
    if ($_idPromo > 0 && $_idVar > 0) {
        $_promoDAO->lierVariante($_idPromo, $_idVar);
        $_succes = 'Variante associée.';
    }
}

$_promotions = $_promoDAO->getAllPromotions() ?? [];
$_catalogue  = $_varDAO2->getCatalogueComplet() ?? [];
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Promotions</h3>
        <button class="btn btn-primary" data-bs-toggle="modal"
                data-bs-target="#modalAjoutPromo">
            <i class="bi bi-plus-circle me-2"></i>Nouvelle promotion
        </button>
    </div>

    <?php if ($_succes): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_succes) ?></div>
    <?php endif; ?>
    <?php foreach ($_erreurs as $_e): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_e) ?></div>
    <?php endforeach; ?>

    <?php if (empty($_promotions)): ?>
        <div class="alert alert-info">Aucune promotion.</div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($_promotions as $_p): ?>
                <div class="col-12 col-md-6">
                    <div class="card border-0 shadow-sm p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold mb-0">
                                    <?= htmlspecialchars($_p->nom_promotion) ?>
                                </h6>
                                <span class="badge bg-danger mt-1">
                                    -<?= (int) $_p->taux_reduction ?>%
                                </span>
                                <?= $_p->actif
                                    ? '<span class="badge bg-success ms-1">Active</span>'
                                    : '<span class="badge bg-secondary ms-1">Inactive</span>' ?>
                            </div>
                            <form method="post">
                                <input type="hidden" name="csrf_token"
                                       value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="id_promotion"
                                       value="<?= (int) $_p->id_promotion ?>">
                                <button type="submit" name="supprimer_promo"
                                        class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('Supprimer ?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                        <small class="text-muted">
                            Du <?= htmlspecialchars($_p->date_debut ?? '') ?>
                            au <?= htmlspecialchars($_p->date_fin ?? '') ?>
                        </small>

                        <!-- Associer variante -->
                        <form method="post" class="d-flex gap-2 mt-3">
                            <input type="hidden" name="csrf_token"
                                   value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="id_promotion"
                                   value="<?= (int) $_p->id_promotion ?>">
                            <select name="id_variante" class="form-select form-select-sm flex-grow-1">
                                <option value="">— Variante —</option>
                                <?php foreach ($_catalogue as $_v): ?>
                                    <option value="<?= (int) $_v['id_variante'] ?>">
                                        <?= htmlspecialchars($_v['nom_produit'] . ' — ' . $_v['nom_variante']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="associer_variante"
                                    class="btn btn-outline-primary btn-sm">
                                Associer
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal nouvelle promotion -->
<div class="modal fade" id="modalAjoutPromo" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <input type="hidden" name="csrf_token"
                   value="<?= $_SESSION['csrf_token'] ?>">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle promotion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nom</label>
                    <input type="text" name="nom_promotion" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Taux de réduction (%)</label>
                    <input type="number" name="taux_reduction" class="form-control"
                           step="0.01" min="0.01" max="100" required>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Date début</label>
                        <input type="date" name="date_debut" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Date fin</label>
                        <input type="date" name="date_fin" class="form-control" required>
                    </div>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="actif" id="actif" class="form-check-input"
                           checked>
                    <label class="form-check-label" for="actif">Active</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Annuler</button>
                <button type="submit" name="ajouter_promo"
                        class="btn btn-primary">Créer</button>
            </div>
        </form>
    </div>
</div>
