<?php

declare(strict_types=1);

require_once __DIR__ . '/../../src/php/utils/check_connexion.php';

$_idClient  = (int) $_SESSION['client']['id_client'];
$_clientDAO = new ClientDAO($cnx);
$_client    = $_clientDAO->getClientParId($_idClient);

$_erreurs = [];
$_succes  = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::verifier();
    $_nom    = trim($_POST['nom']    ?? '');
    $_prenom = trim($_POST['prenom'] ?? '');
    $_email  = trim($_POST['email']  ?? '');
    $_tel    = trim($_POST['telephone'] ?? '');
    $_mdp    = trim($_POST['mdp']    ?? '');

    if ($_nom    === '') $_erreurs[] = 'Nom requis.';
    if ($_prenom === '') $_erreurs[] = 'Prénom requis.';
    if (!filter_var($_email, FILTER_VALIDATE_EMAIL)) $_erreurs[] = 'Email invalide.';

    if (empty($_erreurs)) {
        $_clientDAO->updateChamp($_idClient, 'nom_client',    $_nom);
        $_clientDAO->updateChamp($_idClient, 'prenom_client', $_prenom);
        $_clientDAO->updateChamp($_idClient, 'email_client',  $_email);
        if ($_tel !== '') {
            $_clientDAO->updateChamp($_idClient, 'telephone', $_tel);
        }
        if ($_mdp !== '') {
            $_hash = Password::hash($_mdp);
            $_clientDAO->updateChamp($_idClient, 'mot_de_passe', $_hash);
        }
        // Mise à jour session
        $_SESSION['client']['nom']    = $_nom;
        $_SESSION['client']['prenom'] = $_prenom;
        $_SESSION['client']['email']  = $_email;
        $_succes = true;
        $_client = $_clientDAO->getClientParId($_idClient);
    }
}
?>

<section class="py-5">
    <div class="container-xl">
        <div class="row">
            <!-- Sidebar compte -->
            <nav class="col-12 col-md-3 mb-4">
                <?php require_once __DIR__ . '/../../src/php/utils/menu_compte.php'; ?>
            </nav>

            <div class="col-12 col-md-9">
                <h3 class="fw-bold mb-4">Mon profil</h3>

                <?php if ($_succes): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>Profil mis à jour.
                    </div>
                <?php endif; ?>
                <?php foreach ($_erreurs as $_e): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($_e) ?></div>
                <?php endforeach; ?>

                <div class="card border-0 shadow-sm p-4">
                    <form method="post">
                        <input type="hidden" name="csrf_token"
                               value="<?= $_SESSION['csrf_token'] ?>">
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Prénom</label>
                                <input type="text" name="prenom" class="form-control"
                                       value="<?= htmlspecialchars($_client?->prenom_client ?? '') ?>"
                                       required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Nom</label>
                                <input type="text" name="nom" class="form-control"
                                       value="<?= htmlspecialchars($_client?->nom_client ?? '') ?>"
                                       required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="<?= htmlspecialchars($_client?->email_client ?? '') ?>"
                                   required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Téléphone <span class="text-muted small">(optionnel)</span>
                            </label>
                            <input type="tel" name="telephone" class="form-control"
                                   value="<?= htmlspecialchars($_client?->telephone ?? '') ?>">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Nouveau mot de passe
                                <span class="text-muted small">(laisser vide pour ne pas changer)</span>
                            </label>
                            <input type="password" name="mdp" class="form-control"
                                   minlength="8" autocomplete="new-password" id="mdp">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Enregistrer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
