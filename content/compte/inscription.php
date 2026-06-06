<?php

declare(strict_types=1);

if (isset($_SESSION['client'])) {
    header('Location: /index_.php?page=compte/profil');
    exit;
}

$_clientDAO = new ClientDAO($cnx);
$_erreurs   = [];
$_succes    = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::verifier();
    $_nom      = trim($_POST['nom']      ?? '');
    $_prenom   = trim($_POST['prenom']   ?? '');
    $_email    = trim($_POST['email']    ?? '');
    $_mdp      = trim($_POST['mdp']      ?? '');
    $_mdpConf  = trim($_POST['mdp_conf'] ?? '');
    $_tel      = trim($_POST['telephone'] ?? '');

    if ($_nom    === '') $_erreurs[] = 'Nom requis.';
    if ($_prenom === '') $_erreurs[] = 'Prénom requis.';
    if (!filter_var($_email, FILTER_VALIDATE_EMAIL)) $_erreurs[] = 'Email invalide.';
    if (strlen($_mdp) < 8)  $_erreurs[] = 'Mot de passe : 8 caractères minimum.';
    if ($_mdp !== $_mdpConf) $_erreurs[] = 'Les mots de passe ne correspondent pas.';

    if (empty($_erreurs)) {
        $_hash = Password::hash($_mdp);
        $_ret  = $_clientDAO->ajouterClient($_nom, $_prenom, $_email, $_hash, $_tel);
        if ($_ret > 0) {
            $_succes = true;
        } elseif ($_ret === -1) {
            $_erreurs[] = 'Cet email est déjà utilisé.';
        } else {
            $_erreurs[] = 'Erreur lors de l\'inscription. Réessayez.';
        }
    }
}
?>

<section class="py-5">
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-9 col-md-7 col-lg-5">

                <h2 class="fw-bold mb-1 text-center">Créer un compte</h2>
                <p class="text-muted text-center mb-4">
                    Déjà un compte ?
                    <a href="/index_.php?page=compte/login">Se connecter</a>
                </p>

                <?php if ($_succes): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>
                        Compte créé ! <a href="/index_.php?page=compte/login">Se connecter</a>
                    </div>
                <?php else: ?>

                    <?php foreach ($_erreurs as $_e): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($_e) ?></div>
                    <?php endforeach; ?>

                    <div class="card border-0 shadow-sm p-4">
                        <form method="post">
                            <input type="hidden" name="csrf_token"
                                   value="<?= $_SESSION['csrf_token'] ?>">
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-semibold" for="prenom">Prénom</label>
                                    <input type="text" id="prenom" name="prenom"
                                           class="form-control" required
                                           value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold" for="nom">Nom</label>
                                    <input type="text" id="nom" name="nom"
                                           class="form-control" required
                                           value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="email">Email</label>
                                <input type="email" id="email" name="email"
                                       class="form-control" required
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="telephone">
                                    Téléphone <span class="text-muted small">(optionnel)</span>
                                </label>
                                <input type="tel" id="telephone" name="telephone"
                                       class="form-control"
                                       value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="mdp">
                                    Mot de passe
                                </label>
                                <input type="password" id="mdp" name="mdp"
                                       class="form-control" required minlength="8" autocomplete="new-password">
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold" for="mdp_conf">
                                    Confirmer le mot de passe
                                </label>
                                <input type="password" id="mdp_conf" name="mdp_conf"
                                       class="form-control" required autocomplete="new-password">
                            </div>
                            <button type="submit" class="btn btn-primary w-100 btn-lg">
                                <i class="bi bi-person-plus me-2"></i>Créer mon compte
                            </button>
                        </form>
                    </div>

                <?php endif; ?>

            </div>
        </div>
    </div>
</section>
