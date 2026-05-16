<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/php/utils/check_connexion.php';

$_idCmd  = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$_cmdDAO = new CommandeDAO($cnx);
$_cmd    = $_idCmd > 0 ? $_cmdDAO->getCommandeParId($_idCmd) : null;
?>

<section class="py-5">
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-12 col-md-7 text-center">

                <i class="bi bi-check-circle-fill text-success mb-4 d-block ss-icon-5rem"></i>

                <h2 class="fw-bold mb-2">Commande confirmée !</h2>
                <p class="text-muted mb-4">
                    Merci pour votre achat. Vous recevrez un email de confirmation.
                </p>

                <?php if ($_cmd !== null): ?>
                    <div class="card border-0 shadow-sm p-4 text-start mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Numéro de commande</span>
                            <strong>#<?= (int) $_cmd->id_commande ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Date</span>
                            <strong>
                                <?= htmlspecialchars($_cmd->date_commande ?? date('d/m/Y')) ?>
                            </strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Statut</span>
                            <span class="badge bg-warning text-dark">
                                <?= htmlspecialchars($_cmd->statut_commande) ?>
                            </span>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="d-flex gap-3 justify-content-center">
                    <a href="/index_.php?page=compte/historique_commandes"
                       class="btn btn-outline-primary">
                        <i class="bi bi-bag me-2"></i>Mes commandes
                    </a>
                    <a href="/index_.php?page=catalogue" class="btn btn-primary">
                        <i class="bi bi-grid me-2"></i>Continuer mes achats
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>
