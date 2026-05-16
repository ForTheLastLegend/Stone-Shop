<?php

declare(strict_types=1);

require_once __DIR__ . '/../../src/php/utils/check_admin.php';

$_cmdDAO    = new CommandeDAO($cnx);
$_avisDAO   = new AvisDAO($cnx);
$_varDAO2   = new VarianteDAO($cnx);
$_msgDAO2   = new MessageContactDAO($cnx);

// KPIs du dashboard
$_nbCmdEnAttente     = count($_cmdDAO->getCommandesParStatut('en_attente') ?? []);
$_nbAvisEnAttente    = count($_avisDAO->getAvisEnAttente() ?? []);
$_stockCritique      = $_varDAO2->getStockCritique() ?? [];
$_nbStockCrit        = count($_stockCritique);
$_messagesNonTraites = $_msgDAO2->getMessagesNonTraites() ?? [];
$_nbMessages         = count($_messagesNonTraites);
?>

<div class="container-fluid p-4">
    <h3 class="fw-bold mb-4">Dashboard</h3>

    <!-- KPIs -->
    <div class="row g-3 mb-5">

        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon bg-warning-subtle rounded-circle p-3">
                        <i class="bi bi-bag-check fs-4 text-warning"></i>
                    </div>
                    <div>
                        <div class="h4 fw-bold mb-0"><?= $_nbCmdEnAttente ?></div>
                        <div class="text-muted small">Commandes en attente</div>
                    </div>
                </div>
                <a href="/admin/index_.php?page=gestion_commandes"
                   class="stretched-link"></a>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon bg-info-subtle rounded-circle p-3">
                        <i class="bi bi-star fs-4 text-info"></i>
                    </div>
                    <div>
                        <div class="h4 fw-bold mb-0"><?= $_nbAvisEnAttente ?></div>
                        <div class="text-muted small">Avis à modérer</div>
                    </div>
                </div>
                <a href="/admin/index_.php?page=moderation_avis"
                   class="stretched-link"></a>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon bg-danger-subtle rounded-circle p-3">
                        <i class="bi bi-exclamation-triangle fs-4 text-danger"></i>
                    </div>
                    <div>
                        <div class="h4 fw-bold mb-0"><?= $_nbStockCrit ?></div>
                        <div class="text-muted small">Stocks critiques</div>
                    </div>
                </div>
                <a href="/admin/index_.php?page=gestion_variantes"
                   class="stretched-link"></a>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon bg-primary-subtle rounded-circle p-3">
                        <i class="bi bi-envelope fs-4 text-primary"></i>
                    </div>
                    <div>
                        <div class="h4 fw-bold mb-0"><?= $_nbMessages ?></div>
                        <div class="text-muted small">Messages non traités</div>
                    </div>
                </div>
                <a href="/admin/index_.php?page=messages_contact"
                   class="stretched-link"></a>
            </div>
        </div>

    </div>

    <div class="row g-4">

        <!-- Stocks critiques -->
        <?php if (!empty($_stockCritique)): ?>
            <div class="col-12 col-xl-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-semibold">
                        <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                        Stocks critiques
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Variante</th>
                                    <th class="text-center">Stock</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($_stockCritique, 0, 8) as $_v): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold small">
                                                <?= htmlspecialchars($_v['nom_produit'] ?? '') ?>
                                            </div>
                                            <small class="text-muted">
                                                <?= htmlspecialchars($_v['nom_variante'] ?? '') ?>
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger">
                                                <?= (int) $_v['stock'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="/admin/index_.php?page=gestion_variantes&id_produit=<?= (int) $_v['id_produit'] ?>"
                                               class="btn btn-outline-primary btn-sm">
                                                Gérer
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Messages récents -->
        <?php if (!empty($_messagesNonTraites)): ?>
            <div class="col-12 col-xl-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-semibold">
                        <i class="bi bi-envelope text-primary me-2"></i>
                        Messages non traités
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach (array_slice($_messagesNonTraites, 0, 5) as $_m): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between mb-1">
                                    <strong class="small">
                                        <?= htmlspecialchars($_m->nom_contact) ?>
                                    </strong>
                                    <small class="text-muted">
                                        <?= date('d/m/Y', strtotime($_m->date_envoi ?? 'now')) ?>
                                    </small>
                                </div>
                                <p class="text-muted small mb-0">
                                    <?= htmlspecialchars(mb_substr($_m->sujet, 0, 60)) ?>…
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="card-footer bg-white text-end">
                        <a href="/admin/index_.php?page=messages_contact"
                           class="btn btn-sm btn-outline-primary">
                            Voir tous les messages
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>
