<?php

declare(strict_types=1);

// Déconnexion sur ?logout=1
if (isset($_GET['logout'])) {
    unset($_SESSION['root']);
    session_regenerate_id(true);
    header('Location: /root/index_.php?page=login');
    exit;
}

if (isset($_SESSION['root'])) {
    header('Location: /root/index_.php?page=accueil');
    exit;
}

$_rootDAO = new RootDAO($cnx);
$_erreur  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_csrf();
    $_login = trim($_POST['login'] ?? '');
    $_mdp   = trim($_POST['mdp']   ?? '');

    if ($_login === '' || $_mdp === '') {
        $_erreur = 'Identifiant et mot de passe requis.';
    } else {
        $_rootRow = $_rootDAO->getRootCompletParLogin($_login);
        if ($_rootRow !== null && Password::verify($_mdp, $_rootRow['mot_de_passe'])) {
            session_regenerate_id(true);
            $_SESSION['root'] = [
                'id_root'    => (int) $_rootRow['id_root'],
                'login_root' => $_rootRow['login_root'],
            ];
            header('Location: /root/index_.php?page=accueil');
            exit;
        }
        $_erreur = 'Identifiants incorrects.';
    }
}
?>

<div class="card border-0 shadow-lg p-5 ss-card-login">
    <div class="text-center mb-4">
        <i class="bi bi-shield-lock fs-1 text-primary"></i>
        <h4 class="fw-bold mt-2">Stone Shop Root</h4>
        <p class="text-muted small">Accès super-administrateur.</p>
    </div>

    <?php if ($_erreur): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_erreur) ?></div>
    <?php endif; ?>

    <form method="post" action="/root/index_.php?page=login">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <div class="mb-3">
            <label class="form-label fw-semibold" for="login">Identifiant</label>
            <input type="text" id="login" name="login" class="form-control"
                   required value="<?= htmlspecialchars($_POST['login'] ?? '') ?>">
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold" for="mdp">Mot de passe</label>
            <input type="password" id="mdp" name="mdp" class="form-control" required>
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
