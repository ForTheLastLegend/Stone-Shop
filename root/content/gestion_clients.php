<?php

declare(strict_types=1);

require_once __DIR__ . '/../../src/php/utils/check_root.php';

$_clientDAO = new ClientDAO($cnx);
$_succes    = '';
$_erreurs   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::verifier();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_client'])) {
    $_nom    = trim($_POST['nom']       ?? '');
    $_prenom = trim($_POST['prenom']    ?? '');
    $_email  = trim($_POST['email']     ?? '');
    $_mdp    = trim($_POST['mdp']       ?? '');
    $_tel    = trim($_POST['telephone'] ?? '');

    if ($_nom    === '') $_erreurs[] = 'Nom requis.';
    if ($_prenom === '') $_erreurs[] = 'Prénom requis.';
    if (!filter_var($_email, FILTER_VALIDATE_EMAIL)) $_erreurs[] = 'Email invalide.';
    if (strlen($_mdp) < 8) $_erreurs[] = 'Mot de passe : 8 caractères minimum.';

    if (empty($_erreurs)) {
        $_hash = Password::hash($_mdp);
        $_ret  = $_clientDAO->ajouterClient($_nom, $_prenom, $_email, $_hash, $_tel);
        if ($_ret > 0) {
            $_succes = 'Client créé.';
        } elseif ($_ret === -1) {
            $_erreurs[] = 'Cet email est déjà utilisé.';
        } else {
            $_erreurs[] = 'Erreur création.';
        }
    }
}

$_perPage = 100;
$_page    = max(1, (int) ($_GET['p'] ?? 1));
$_offset  = ($_page - 1) * $_perPage;
$_total   = $_clientDAO->compterClients();
$_nbPages = (int) max(1, ceil($_total / $_perPage));
if ($_page > $_nbPages) {
    $_page   = $_nbPages;
    $_offset = ($_page - 1) * $_perPage;
}
$_clients = $_clientDAO->getClientsPagine($_offset, $_perPage) ?? [];
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">
            <i class="bi bi-people me-2"></i>Clients
        </h3>
        <button class="btn btn-primary" data-bs-toggle="modal"
                data-bs-target="#modalAjoutClient">
            <i class="bi bi-plus-circle me-2"></i>Nouveau client
        </button>
    </div>

    <?php if ($_succes): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_succes) ?></div>
    <?php endif; ?>
    <?php foreach ($_erreurs as $_e): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_e) ?></div>
    <?php endforeach; ?>

    <p class="text-muted small mb-3">
        <?= $_total ?> client(s) — page <?= $_page ?> / <?= $_nbPages ?>
    </p>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Inscription</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($_clients)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Aucun client.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($_clients as $_c): ?>
                            <tr>
                                <td class="text-muted small">
                                    <?= $_c->id_client ?>
                                </td>
                                <td><?= htmlspecialchars($_c->nom_client) ?></td>
                                <td><?= htmlspecialchars($_c->prenom_client) ?></td>
                                <td><?= htmlspecialchars($_c->email_client) ?></td>
                                <td class="text-muted">
                                    <?= htmlspecialchars($_c->telephone ?? '—') ?>
                                </td>
                                <td class="text-muted small">
                                    <?= htmlspecialchars(
                                        date('d/m/Y', strtotime($_c->date_inscription))
                                    ) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($_nbPages > 1): ?>
        <nav class="mt-3">
            <ul class="pagination justify-content-center mb-0">
                <li class="page-item <?= $_page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link"
                       href="/root/index_.php?page=gestion_clients&p=<?= $_page - 1 ?>">
                        Précédent
                    </a>
                </li>
                <li class="page-item disabled">
                    <span class="page-link">
                        <?= $_page ?> / <?= $_nbPages ?>
                    </span>
                </li>
                <li class="page-item <?= $_page >= $_nbPages ? 'disabled' : '' ?>">
                    <a class="page-link"
                       href="/root/index_.php?page=gestion_clients&p=<?= $_page + 1 ?>">
                        Suivant
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<!-- Modal ajout client -->
<div class="modal fade" id="modalAjoutClient" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <input type="hidden" name="csrf_token"
                   value="<?= $_SESSION['csrf_token'] ?>">
            <div class="modal-header">
                <h5 class="modal-title">Nouveau client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Prénom</label>
                        <input type="text" name="prenom" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Nom</label>
                        <input type="text" name="nom" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Téléphone <span class="text-muted small">(optionnel)</span>
                    </label>
                    <input type="tel" name="telephone" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Mot de passe</label>
                    <input type="password" name="mdp" class="form-control"
                           required minlength="8">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Annuler</button>
                <button type="submit" name="ajouter_client"
                        class="btn btn-primary">Créer</button>
            </div>
        </form>
    </div>
</div>
