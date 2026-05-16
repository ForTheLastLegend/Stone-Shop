<?php

declare(strict_types=1);

require_once __DIR__ . '/../../src/php/utils/check_connexion.php';

$_idClient = (int) $_SESSION['client']['id_client'];
$_cmdDAO   = new CommandeDAO($cnx);
$_commandes = $_cmdDAO->getCommandesByClient($_idClient) ?? [];

// Statuts avec couleurs Bootstrap
$_statuts = [
    'en_attente'  => 'warning',
    'confirmee'   => 'info',
    'en_cours'    => 'primary',
    'expediee'    => 'success',
    'livree'      => 'success',
    'annulee'     => 'danger',
];
?>

<section class="py-5">
    <div class="container-xl">
        <div class="row">
            <nav class="col-12 col-md-3 mb-4">
                <?php require_once __DIR__ . '/../../src/php/utils/menu_compte.php'; ?>
            </nav>

            <div class="col-12 col-md-9">
                <h3 class="fw-bold mb-4">Mes commandes</h3>

                <?php if (empty($_commandes)): ?>
                    <div class="alert alert-info">Aucune commande pour l'instant.</div>
                    <a href="/index_.php?page=catalogue" class="btn btn-primary">
                        Voir le catalogue
                    </a>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th class="text-end">Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($_commandes as $_cmd): ?>
                                    <tr>
                                        <td><strong>#<?= (int) $_cmd->id_commande ?></strong></td>
                                        <td>
                                            <?= htmlspecialchars(
                                                date('d/m/Y', strtotime($_cmd->date_commande))
                                            ) ?>
                                        </td>
                                        <td>
                                            <?php $_couleur = $_statuts[$_cmd->statut_commande] ?? 'secondary'; ?>
                                            <span class="badge bg-<?= $_couleur ?>">
                                                <?= htmlspecialchars($_cmd->statut_commande) ?>
                                            </span>
                                        </td>
                                        <td class="text-end fw-semibold">
                                            <?= number_format($_cmd->total_commande, 2, ',', ' ') ?> €
                                        </td>
                                        <td class="text-end">
                                            <a href="/index_.php?page=compte/detail_commande&id=<?= (int) $_cmd->id_commande ?>"
                                               class="btn btn-outline-primary btn-sm">
                                                Détail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
