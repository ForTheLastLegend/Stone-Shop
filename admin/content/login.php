<?php

declare(strict_types=1);

if (isset($_GET['logout'])) {
    unset($_SESSION['admin'], $_SESSION['support']);
    session_regenerate_id(true);
    header('Location: index_.php?page=login');
    exit;
}

if (isset($_SESSION['admin'])) {
    header('Location: index_.php?page=accueil');
    exit;
}
if (isset($_SESSION['support'])) {
    header('Location: index_.php?page=support_chat');
    exit;
}

$_adminDAO   = new AdminDAO($cnx);
$_supportDAO = new SupportDAO($cnx);
$_erreur     = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::verifier();
    $_email = trim($_POST['email'] ?? '');
    $_mdp   = trim($_POST['mdp']   ?? '');

    if ($_email === '' || $_mdp === '') {
        $_erreur = 'Email et mot de passe requis.';
    } else {
        // Tentative admin : SELECT par email puis Password::verify côté PHP.
        $_adminRow = $_adminDAO->getAdminCompletParEmail($_email);
        if ($_adminRow !== null && Password::verify($_mdp, $_adminRow['mot_de_passe'])) {
            session_regenerate_id(true);
            $_SESSION['admin'] = [
                'id_admin' => (int) $_adminRow['id_admin'],
                'nom'      => $_adminRow['nom_admin'],
                'prenom'   => $_adminRow['prenom_admin'],
                'email'    => $_adminRow['email_admin'],
            ];
            header('Location: index_.php?page=accueil');
            exit;
        }

        // Tentative support : même pattern.
        $_supportRow = $_supportDAO->getSupportCompletParEmail($_email);
        if ($_supportRow !== null && Password::verify($_mdp, $_supportRow['mot_de_passe'])) {
            session_regenerate_id(true);
            $_SESSION['support'] = [
                'id_support' => (int) $_supportRow['id_support'],
                'nom'        => $_supportRow['nom_support'],
                'prenom'     => $_supportRow['prenom_support'],
                'email'      => $_supportRow['email_support'],
            ];
            header('Location: index_.php?page=support_chat');
            exit;
        }

        $_erreur = 'Identifiants incorrects.';
    }
}
?>

<div class="card border-0 shadow-lg p-5 ss-card-login">
    <div class="text-center mb-4">
        <i class="bi bi-gem fs-1 text-primary"></i>
        <h4 class="fw-bold mt-2">Stone Shop Admin</h4>
        <p class="text-muted small">Accès réservé aux administrateurs et au support.</p>
    </div>

    <?php if ($_erreur): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_erreur) ?></div>
    <?php endif; ?>

    <form method="post" action="index_.php?page=login">
        <input type="hidden" name="csrf_token"
               value="<?= $_SESSION['csrf_token'] ?>">
        <div class="mb-3">
            <label class="form-label fw-semibold" for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control"
                   required autocomplete="username" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold" for="mdp">Mot de passe</label>
            <input type="password" id="mdp" name="mdp" class="form-control" required autocomplete="current-password">
        </div>
        <button type="submit" class="btn btn-primary w-100 btn-lg">
            <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
        </button>
    </form>

    <div class="text-center mt-4">
        <a href="/index_.php?page=accueil" class="text-muted small">
            <i class="bi bi-arrow-left me-1"></i>Retour au site
        </a>
    </div>
</div>
