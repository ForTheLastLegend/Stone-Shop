<?php

declare(strict_types=1);

require_once __DIR__ . '/../../src/php/utils/check_admin.php';

$_transDAO = new TransporteurDAO($cnx);
$_succes   = '';
$_erreurs  = [];

// Un seul appel couvre toutes les branches POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::verifier();
}

// Ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_transporteur'])) {
    $_nom   = trim($_POST['nom_transporteur'] ?? '');
    $_delai = trim($_POST['delai_estime']     ?? '');
    $_frais = (float) str_replace(',', '.', $_POST['frais_livraison'] ?? '0');

    if ($_nom  === '') $_erreurs[] = 'Nom requis.';
    if ($_frais < 0)   $_erreurs[] = 'Frais invalides.';

    if (empty($_erreurs)) {
        $_ret = $_transDAO->ajouterTransporteur($_nom, $_delai, $_frais);
        $_succes = $_ret > 0 ? 'Transporteur ajouté.' : 'Erreur ajout.';
    }
}

// Toggle actif
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_actif'])) {
    $_idTrans = (int) ($_POST['id_transporteur'] ?? 0);
    $_actif   = $_POST['actif'] ?? '0';
    $_transDAO->updateChamp($_idTrans, 'actif', $_actif === '1' ? 'false' : 'true');
    $_succes = 'Statut mis à jour.';
}

$_transporteurs = $_transDAO->getAllTransporteurs() ?? [];
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Transporteurs</h3>
        <button class="btn btn-primary" data-bs-toggle="modal"
                data-bs-target="#modalAjoutTrans">
            <i class="bi bi-plus-circle me-2"></i>Nouveau transporteur
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
                        <th>Nom</th>
                        <th>Délai estimé</th>
                        <th class="text-end">Frais (€)</th>
                        <th class="text-center">Actif</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($_transporteurs)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                Aucun transporteur.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($_transporteurs as $_t): ?>
                            <tr>
                                <td class="fw-semibold">
                                    <?= htmlspecialchars($_t->nom_transporteur) ?>
                                </td>
                                <td class="text-muted">
                                    <?= htmlspecialchars($_t->delai_estime ?? '—') ?>
                                </td>
                                <td class="text-end">
                                    <?= number_format($_t->frais_livraison, 2, ',', ' ') ?> €
                                </td>
                                <td class="text-center">
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="csrf_token"
                                               value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="id_transporteur"
                                               value="<?= (int) $_t->id_transporteur ?>">
                                        <input type="hidden" name="actif"
                                               value="<?= $_t->actif ? '1' : '0' ?>">
                                        <button type="submit" name="toggle_actif"
                                                class="btn btn-sm btn-link p-0">
                                            <i class="bi bi-toggle-<?= $_t->actif ? 'on text-success' : 'off text-secondary' ?> fs-4"></i>
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

<!-- Modal ajout transporteur -->
<div class="modal fade" id="modalAjoutTrans" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <input type="hidden" name="csrf_token"
                   value="<?= $_SESSION['csrf_token'] ?>">
            <div class="modal-header">
                <h5 class="modal-title">Nouveau transporteur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nom</label>
                    <input type="text" name="nom_transporteur" class="form-control"
                           placeholder="bpost, Colis Privé…" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Délai estimé <span class="text-muted small">(opt.)</span>
                    </label>
                    <input type="text" name="delai_estime" class="form-control"
                           placeholder="2-3 jours ouvrables">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Frais de livraison (€)</label>
                    <input type="number" name="frais_livraison" class="form-control"
                           step="0.01" min="0" value="0" required>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="actif" id="actif_t"
                           class="form-check-input" checked>
                    <label class="form-check-label" for="actif_t">Actif</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Annuler</button>
                <button type="submit" name="ajouter_transporteur"
                        class="btn btn-primary">Ajouter</button>
            </div>
        </form>
    </div>
</div>
