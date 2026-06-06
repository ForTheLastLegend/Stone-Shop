<?php

declare(strict_types=1);

require_once __DIR__ . '/../../src/php/utils/check_admin.php';

$_msgDAO = new MessageContactDAO($cnx);
$_succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::verifier();
}

// Marquer traité
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marquer_traite'])) {
    $_id = (int) ($_POST['id_message'] ?? 0);
    if ($_id > 0) {
        $_msgDAO->marquerTraite($_id);
        $_succes = 'Message marqué comme traité.';
    }
}

$_messages = $_msgDAO->getAllMessages() ?? [];
?>

<div class="container-fluid p-4">
    <h3 class="fw-bold mb-4">Messages de contact</h3>

    <?php if ($_succes): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_succes) ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Sujet</th>
                        <th class="text-center">Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($_messages)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Aucun message.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($_messages as $_m): ?>
                            <tr class="<?= !$_m->traite ? 'table-warning' : '' ?>">
                                <td class="text-muted small">
                                    <?= date('d/m/Y H:i', strtotime($_m->date_envoi ?? 'now')) ?>
                                </td>
                                <td><?= htmlspecialchars($_m->nom_contact) ?></td>
                                <td>
                                    <a href="mailto:<?= htmlspecialchars($_m->email_contact) ?>">
                                        <?= htmlspecialchars($_m->email_contact) ?>
                                    </a>
                                </td>
                                <td>
                                    <?= htmlspecialchars(mb_substr($_m->sujet, 0, 40)) ?>
                                    <?= mb_strlen($_m->sujet) > 40 ? '…' : '' ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($_m->traite): ?>
                                        <span class="badge bg-success">Traité</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Non traité</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-primary btn-sm me-1" 
                                            data-bs-toggle="modal" data-bs-target="#msgModal<?= $_m->id_message ?>">
                                        <i class="bi bi-eye"></i> Lire
                                    </button>

                                    <?php if (!$_m->traite): ?>
                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="csrf_token"
                                                   value="<?= $_SESSION['csrf_token'] ?>">
                                            <input type="hidden" name="id_message"
                                                   value="<?= (int) $_m->id_message ?>">
                                            <button type="submit" name="marquer_traite"
                                                    class="btn btn-outline-success btn-sm">
                                                <i class="bi bi-check me-1"></i>Traité
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <!-- Modal Lecture Message -->
                                    <div class="modal fade" id="msgModal<?= $_m->id_message ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        Message de <?= htmlspecialchars($_m->nom_contact) ?>
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Sujet :</strong> <?= htmlspecialchars($_m->sujet) ?></p>
                                                    <hr>
                                                    <div class="p-3 bg-light rounded text-break ss-pre-wrap"><?= htmlspecialchars($_m->contenu) ?></div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                                    <?php if (!$_m->traite): ?>
                                                        <form method="post" class="m-0">
                                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                            <input type="hidden" name="id_message" value="<?= (int) $_m->id_message ?>">
                                                            <button type="submit" name="marquer_traite" class="btn btn-success">
                                                                <i class="bi bi-check"></i> Marquer comme traité
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
