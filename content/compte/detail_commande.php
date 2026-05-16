<?php

declare(strict_types=1);

require_once __DIR__ . '/../../src/php/utils/check_connexion.php';

$_idCmd    = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$_idClient = (int) $_SESSION['client']['id_client'];
$_cmdDAO   = new CommandeDAO($cnx);
$_transDAO = new TransporteurDAO($cnx);

$_cmd    = $_idCmd > 0 ? $_cmdDAO->getCommandeParId($_idCmd) : null;
$_lignes = $_idCmd > 0 ? ($_cmdDAO->getDetailCommande($_idCmd) ?? []) : [];

// Sécurité : la commande doit appartenir au client
if ($_cmd === null || (int) $_cmd->id_client !== $_idClient) {
    echo '<div class="container py-5"><div class="alert alert-danger">Commande introuvable.</div></div>';
    return;
}

// Transporteur : non porté par Commande DTO, on le charge séparément.
$_transporteur = $_transDAO->getTransporteurParId($_cmd->id_transporteur);
?>

<section class="py-5">
    <div class="container-xl">
        <div class="row">
            <nav class="col-12 col-md-3 mb-4">
                <?php require_once __DIR__ . '/../../src/php/utils/menu_compte.php'; ?>
            </nav>

            <div class="col-12 col-md-9">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <a href="/index_.php?page=compte/historique_commandes"
                       class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <h3 class="fw-bold mb-0">
                        Commande #<?= (int) $_cmd->id_commande ?>
                    </h3>
                </div>

                <!-- Statut & infos -->
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <p class="text-muted small mb-0">Date</p>
                            <strong>
                                <?= date('d/m/Y', strtotime($_cmd->date_commande)) ?>
                            </strong>
                        </div>
                        <div class="col-6 col-md-3">
                            <p class="text-muted small mb-0">Statut</p>
                            <span class="badge bg-warning text-dark">
                                <?= htmlspecialchars($_cmd->statut_commande) ?>
                            </span>
                        </div>
                        <div class="col-6 col-md-3">
                            <p class="text-muted small mb-0">Transporteur</p>
                            <strong>
                                <?= htmlspecialchars($_transporteur?->nom_transporteur ?? '—') ?>
                            </strong>
                        </div>
                        <div class="col-6 col-md-3">
                            <p class="text-muted small mb-0">Suivi</p>
                            <strong>
                                <?= htmlspecialchars($_cmd->numero_suivi ?? '—') ?>
                            </strong>
                        </div>
                    </div>
                </div>

                <!-- Lignes de commande -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produit</th>
                                        <th class="text-center">Qté</th>
                                        <th class="text-end">Prix unitaire</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $_total = 0.0; ?>
                                    <?php foreach ($_lignes as $_l): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">
                                                    <?= htmlspecialchars($_l['nom_produit'] ?? '') ?>
                                                </div>
                                                <small class="text-muted">
                                                    <?= htmlspecialchars($_l['nom_variante'] ?? '') ?>
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <?= (int) $_l['quantite'] ?>
                                            </td>
                                            <td class="text-end">
                                                <?= number_format((float) $_l['prix_unitaire'], 2, ',', ' ') ?> €
                                            </td>
                                            <td class="text-end fw-semibold">
                                                <?php $_st = (float) $_l['prix_unitaire'] * (int) $_l['quantite'];
                                                      $_total += $_st;
                                                      echo number_format($_st, 2, ',', ' ') . ' €'; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Total</td>
                                        <td class="text-end fw-bold text-primary">
                                            <?= number_format($_total, 2, ',', ' ') ?> €
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
