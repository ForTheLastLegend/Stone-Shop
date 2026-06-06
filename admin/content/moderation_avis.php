<?php

declare(strict_types=1);

require_once __DIR__ . '/../../src/php/utils/check_admin.php';

$_avisDAO = new AvisDAO($cnx);
$_succes  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::verifier();
}

// Approbation / refus direct (le bouton AJAX de la couche 6 utilise toggle_avis.php).
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['moderer'])) {
    $_idAvis = (int) ($_POST['id_avis'] ?? 0);
    $_action = trim($_POST['action'] ?? '');
    if ($_idAvis > 0 && in_array($_action, ['approuve', 'refuse'], true)) {
        $_avisDAO->modererAvis($_idAvis, $_action);
        $_succes = 'Avis mis à jour.';
    }
}

$_avis = $_avisDAO->getAvisEnAttente() ?? [];
?>

<div class="container-fluid p-4">
    <h3 class="fw-bold mb-4">Modération des avis</h3>

    <?php if ($_succes): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_succes) ?></div>
    <?php endif; ?>

    <?php if (empty($_avis)): ?>
        <div class="alert alert-info">
            <i class="bi bi-check-circle me-2"></i>
            Aucun avis en attente de modération.
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($_avis as $_av): ?>
                <div class="col-12" id="avis-<?= (int) $_av['id_avis'] ?>">
                    <div class="card border-0 shadow-sm p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong><?= htmlspecialchars($_av['titre'] ?? '') ?></strong>
                                <span class="text-warning ms-2">
                                    <?= str_repeat('★', (int) $_av['note']) ?>
                                    <?= str_repeat('☆', 5 - (int) $_av['note']) ?>
                                </span>
                                <small class="text-muted ms-2">
                                    — <?= htmlspecialchars($_av['nom_produit'] ?? '') ?>
                                    (<?= htmlspecialchars($_av['nom_variante'] ?? '') ?>)
                                </small>
                            </div>
                            <small class="text-secondary">
                                <?= date('d/m/Y', strtotime($_av['date_avis'] ?? 'now')) ?>
                            </small>
                        </div>
                        <p class="text-muted small mb-3">
                            <?= nl2br(htmlspecialchars($_av['commentaire'] ?? '')) ?>
                        </p>
                        <!-- Boutons modération — AJAX toggle_avis.php en couche 6 -->
                        <div class="d-flex gap-2">
                            <form method="post" class="d-inline">
                                <input type="hidden" name="csrf_token"
                                       value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="id_avis"
                                       value="<?= (int) $_av['id_avis'] ?>">
                                <input type="hidden" name="action" value="approuve">
                                <button type="submit" name="moderer"
                                        class="btn btn-success btn-sm"
                                        data-id="<?= (int) $_av['id_avis'] ?>"
                                        data-action="approuve">
                                    <i class="bi bi-check-circle me-1"></i>Approuver
                                </button>
                            </form>
                            <form method="post" class="d-inline">
                                <input type="hidden" name="csrf_token"
                                       value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="id_avis"
                                       value="<?= (int) $_av['id_avis'] ?>">
                                <input type="hidden" name="action" value="refuse">
                                <button type="submit" name="moderer"
                                        class="btn btn-outline-danger btn-sm"
                                        data-id="<?= (int) $_av['id_avis'] ?>"
                                        data-action="refuse">
                                    <i class="bi bi-x-circle me-1"></i>Refuser
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
