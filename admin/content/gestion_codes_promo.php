<?php

declare(strict_types=1);

require_once __DIR__ . '/../../src/php/utils/check_admin.php';

$_cpDAO = new CodePromoDAO($cnx);
$_succes = '';
$_erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_csrf();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_code'])) {
    $_code = trim(strtoupper($_POST['code'] ?? ''));
    $_taux = (float) str_replace(',', '.', $_POST['taux'] ?? '0');
    $_debut = trim($_POST['date_debut'] ?? '');
    $_fin = trim($_POST['date_fin'] ?? '');
    $_usageBrut = trim($_POST['usage_max'] ?? '');
    $_usageMax = $_usageBrut !== '' ? (int) $_usageBrut : null;

    if ($_code === '') $_erreurs[] = 'Code requis.';
    if ($_taux <= 0) $_erreurs[] = 'Taux invalide.';
    if ($_debut === '') $_erreurs[] = 'Date début requise.';
    if ($_fin === '') $_erreurs[] = 'Date fin requise.';

    if (empty($_erreurs)) {
        $_ret = $_cpDAO->ajouterCodePromo($_code, $_taux, $_debut, $_fin, $_usageMax);
        if ($_ret > 0) {
            $_succes = 'Code promo créé.';
        } elseif ($_ret === -1) {
            $_erreurs[] = 'Ce code existe déjà.';
        } else {
            $_erreurs[] = 'Erreur création.';
        }
    }
}

$_codes = $_cpDAO->getAllCodePromos() ?? [];
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Codes promotionnels</h3>
        <button class="btn btn-primary" data-bs-toggle="modal"
                data-bs-target="#modalAjoutCode">
            <i class="bi bi-plus-circle me-2"></i>Nouveau code
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
                        <th>Code</th>
                        <th class="text-end">Taux</th>
                        <th>Validité</th>
                        <th class="text-center">Usages</th>
                        <th class="text-center">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($_codes)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Aucun code promo.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($_codes as $_cp): ?>
                            <tr>
                                <td>
                                    <code class="fw-bold fs-6">
                                        <?= htmlspecialchars($_cp->code) ?>
                                    </code>
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-danger">
                                        -<?= (int) $_cp->taux_reduction ?>%
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    <?= htmlspecialchars($_cp->date_debut) ?>
                                    →
                                    <?= htmlspecialchars($_cp->date_fin) ?>
                                </td>
                                <td class="text-center">
                                    <?= (int) $_cp->usage_actuel ?>
                                    /
                                    <?= $_cp->usage_max !== null ? (int) $_cp->usage_max : '∞' ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $_cp->actif ? 'success' : 'secondary' ?>">
                                        <?= $_cp->actif ? 'Actif' : 'Inactif' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal nouveau code promo -->
<div class="modal fade" id="modalAjoutCode" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <input type="hidden" name="csrf_token"
                   value="<?= $_SESSION['csrf_token'] ?>">
            <div class="modal-header">
                <h5 class="modal-title">Nouveau code promo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Code</label>
                    <input type="text" name="code" class="form-control text-uppercase"
                           placeholder="ETE2025" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Taux de réduction (%)</label>
                    <input type="number" name="taux" class="form-control"
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
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Usage max <span class="text-muted small">(vide = illimité)</span>
                    </label>
                    <input type="number" name="usage_max" class="form-control"
                           min="1" placeholder="100">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Annuler</button>
                <button type="submit" name="ajouter_code"
                        class="btn btn-primary">Créer</button>
            </div>
        </form>
    </div>
</div>
