<?php

declare(strict_types=1);

require_once __DIR__ . '/../../src/php/utils/check_connexion.php';

$_idClient  = (int) $_SESSION['client']['id_client'];
$_avisDAO   = new AvisDAO($cnx);
$_cmdDAO    = new CommandeDAO($cnx);
$_varDAO2   = new VarianteDAO($cnx);

// Variantes achetées par ce client : seules celles-ci permettent de laisser un avis.
$_variantesAchetees = $_cmdDAO->getVariantesAchetees($_idClient) ?? [];
$_mesAvis           = $_avisDAO->getAvisByClient($_idClient) ?? [];
$_idVariantesAvec   = array_column($_mesAvis, 'id_variante');

$_succes  = '';
$_erreurs = [];

// Ajout avis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['soumettre_avis'])) {
    verifier_csrf();
    $_idVar    = (int) ($_POST['id_variante'] ?? 0);
    $_note     = (int) ($_POST['note']     ?? 0);
    $_titre    = trim($_POST['titre']    ?? '');
    $_comment  = trim($_POST['commentaire'] ?? '');

    if ($_idVar  === 0) $_erreurs[] = 'Variante invalide.';
    if ($_note < 1 || $_note > 5) $_erreurs[] = 'Note entre 1 et 5 requise.';
    if ($_titre  === '') $_erreurs[] = 'Titre requis.';
    if ($_comment === '') $_erreurs[] = 'Commentaire requis.';

    if (empty($_erreurs)) {
        $_ret = $_avisDAO->ajouterAvis($_idClient, $_idVar, $_note, $_titre, $_comment);
        if ($_ret > 0) {
            $_succes = 'Votre avis a été soumis et sera visible après modération.';
            $_mesAvis = $_avisDAO->getAvisByClient($_idClient) ?? [];
            $_idVariantesAvec = array_column($_mesAvis, 'id_variante');
        } else {
            $_erreurs[] = 'Erreur lors de la soumission.';
        }
    }
}
?>

<section class="py-5">
    <div class="container-xl">
        <div class="row">
            <nav class="col-12 col-md-3 mb-4">
                <?php require_once __DIR__ . '/../../src/php/utils/menu_compte.php'; ?>
            </nav>

            <div class="col-12 col-md-9">
                <h3 class="fw-bold mb-4">Mes avis</h3>

                <?php if ($_succes): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($_succes) ?></div>
                <?php endif; ?>
                <?php foreach ($_erreurs as $_e): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($_e) ?></div>
                <?php endforeach; ?>

                <!-- Avis existants -->
                <?php if (!empty($_mesAvis)): ?>
                    <h5 class="fw-semibold mb-3">Avis déposés</h5>
                    <div class="row g-3 mb-5">
                        <?php foreach ($_mesAvis as $_av): ?>
                            <div class="col-12">
                                <div class="card border-0 shadow-sm p-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <strong><?= htmlspecialchars($_av['titre'] ?? '') ?></strong>
                                        <span>
                                            <?php for ($_s = 1; $_s <= 5; $_s++): ?>
                                                <i class="bi bi-star<?= $_s <= (int) $_av['note'] ? '-fill text-warning' : ' text-secondary' ?> small"></i>
                                            <?php endfor; ?>
                                        </span>
                                    </div>
                                    <p class="text-muted small mb-1">
                                        <?= htmlspecialchars($_av['commentaire'] ?? '') ?>
                                    </p>
                                    <small class="text-secondary">
                                        Statut :
                                        <span class="badge bg-<?= $_av['modere'] === 'approuve' ? 'success' : ($_av['modere'] === 'refuse' ? 'danger' : 'warning text-dark') ?>">
                                            <?= htmlspecialchars($_av['modere'] ?? 'en_attente') ?>
                                        </span>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Formulaire nouvel avis -->
                <?php
                $_variantesDisponibles = array_filter(
                    $_variantesAchetees,
                    fn($v) => !in_array((int) $v['id_variante'], $_idVariantesAvec, true)
                );
                ?>
                <?php if (!empty($_variantesDisponibles)): ?>
                    <h5 class="fw-semibold mb-3">Laisser un avis</h5>
                    <div class="card border-0 shadow-sm p-4">
                        <form method="post">
                            <input type="hidden" name="csrf_token"
                                   value="<?= $_SESSION['csrf_token'] ?>">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Produit</label>
                                <select name="id_variante" class="form-select" required>
                                    <option value="">— Choisir un produit —</option>
                                    <?php foreach ($_variantesDisponibles as $_vd): ?>
                                        <option value="<?= (int) $_vd['id_variante'] ?>">
                                            <?= htmlspecialchars($_vd['nom_produit'] . ' — ' . $_vd['nom_variante']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Note</label>
                                <select name="note" class="form-select" required>
                                    <?php for ($_n = 5; $_n >= 1; $_n--): ?>
                                        <option value="<?= $_n ?>">
                                            <?= $_n ?> étoile<?= $_n > 1 ? 's' : '' ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Titre</label>
                                <input type="text" name="titre" class="form-control" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Commentaire</label>
                                <textarea name="commentaire" class="form-control"
                                          rows="4" required></textarea>
                            </div>
                            <button type="submit" name="soumettre_avis" class="btn btn-primary">
                                <i class="bi bi-star me-2"></i>Soumettre mon avis
                            </button>
                        </form>
                    </div>
                <?php elseif (empty($_variantesAchetees)): ?>
                    <p class="text-muted">
                        Vous devez acheter un produit avant de pouvoir laisser un avis.
                    </p>
                <?php else: ?>
                    <p class="text-muted">
                        Vous avez déjà laissé un avis pour tous vos achats.
                    </p>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>
