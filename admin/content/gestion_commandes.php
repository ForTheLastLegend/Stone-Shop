<?php

declare(strict_types=1);

require_once __DIR__ . '/../../src/php/utils/check_admin.php';

$_cmdDAO   = new CommandeDAO($cnx);
$_succes   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::verifier();
}

// Mise à jour statut
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_statut'])) {
    $_idCmd  = (int) ($_POST['id_commande'] ?? 0);
    $_statut = trim($_POST['statut'] ?? '');
    if ($_idCmd > 0 && $_statut !== '') {
        $_cmdDAO->updateStatut($_idCmd, $_statut);
        $_succes = 'Statut mis à jour.';
    }
}

// Numéro de suivi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_suivi'])) {
    $_idCmd = (int) ($_POST['id_commande'] ?? 0);
    $_suivi = trim($_POST['numero_suivi'] ?? '');
    if ($_idCmd > 0) {
        $_cmdDAO->updateChamp($_idCmd, 'numero_suivi', $_suivi);
        $_succes = 'Numéro de suivi mis à jour.';
    }
}

$_commandes = $_cmdDAO->getAllCommandes() ?? [];
$_statuts   = ['en_attente', 'confirmee', 'en_cours', 'expediee', 'livree', 'annulee'];
$_couleurs  = [
    'en_attente' => 'warning',
    'confirmee'  => 'info',
    'en_cours'   => 'primary',
    'expediee'   => 'success',
    'livree'     => 'success',
    'annulee'    => 'danger',
];

// Détail commande si demandé
$_idDetail  = isset($_GET['detail']) ? (int) $_GET['detail'] : 0;
$_lignesDetail = $_idDetail > 0 ? ($_cmdDAO->getDetailCommande($_idDetail) ?? []) : [];
?>

<div class="container-fluid p-4">
    <h3 class="fw-bold mb-4">Gestion des commandes</h3>

    <?php if ($_succes): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_succes) ?></div>
    <?php endif; ?>

    <!-- Détail commande -->
    <?php if ($_idDetail > 0 && !empty($_lignesDetail)): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between">
                <span>Détail commande #<?= $_idDetail ?></span>
                <a href="/admin/index_.php?page=gestion_commandes"
                   class="btn btn-sm btn-outline-secondary">Fermer</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Produit</th>
                            <th class="text-center">Qté</th>
                            <th class="text-end">Prix unitaire</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_lignesDetail as $_l): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($_l['nom_produit'] ?? '') ?>
                                    <small class="text-muted d-block">
                                        <?= htmlspecialchars($_l['nom_variante'] ?? '') ?>
                                    </small>
                                </td>
                                <td class="text-center"><?= (int) $_l['quantite'] ?></td>
                                <td class="text-end">
                                    <?= number_format((float) $_l['prix_unitaire'], 2, ',', ' ') ?> €
                                </td>
                                <td class="text-end fw-semibold">
                                    <?= number_format((float) $_l['prix_unitaire'] * (int) $_l['quantite'], 2, ',', ' ') ?> €
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- Tableau commandes -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Statut</th>
                        <th>Suivi</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($_commandes)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Aucune commande.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($_commandes as $_cmd): ?>
                            <tr>
                                <td><strong>#<?= (int) $_cmd->id_commande ?></strong></td>
                                <td class="text-muted small">
                                    <?= date('d/m/Y', strtotime($_cmd->date_commande)) ?>
                                </td>
                                <td>Client #<?= (int) $_cmd->id_client ?></td>
                                <td>
                                    <!-- Statut modifiable -->
                                    <form method="post" class="d-flex gap-1 align-items-center">
                                        <input type="hidden" name="csrf_token"
                                               value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="id_commande"
                                               value="<?= (int) $_cmd->id_commande ?>">
                                        <select name="statut"
                                                class="form-select form-select-sm statut-select ss-w-130"
                                                data-id="<?= (int) $_cmd->id_commande ?>">
                                            <?php foreach ($_statuts as $_s): ?>
                                                <option value="<?= $_s ?>"
                                                        <?= $_cmd->statut_commande === $_s ? 'selected' : '' ?>>
                                                    <?= $_s ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" name="update_statut"
                                                class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-check"></i>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <form method="post" class="d-flex gap-1">
                                        <input type="hidden" name="csrf_token"
                                               value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="id_commande"
                                               value="<?= (int) $_cmd->id_commande ?>">
                                        <input type="text" name="numero_suivi"
                                               class="form-control form-control-sm ss-w-120"
                                               value="<?= htmlspecialchars($_cmd->numero_suivi ?? '') ?>"
                                               placeholder="Numéro suivi">
                                        <button type="submit" name="update_suivi"
                                                class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-check"></i>
                                        </button>
                                    </form>
                                </td>
                                <td class="text-center">
                                    <a href="/admin/index_.php?page=gestion_commandes&detail=<?= (int) $_cmd->id_commande ?>"
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
