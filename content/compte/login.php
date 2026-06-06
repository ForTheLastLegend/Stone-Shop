<?php

declare(strict_types=1);

// Si déjà connecté → accueil
if (isset($_SESSION['client'])) {
    header('Location: /index_.php?page=accueil');
    exit;
}

$_clientDAO = new ClientDAO($cnx);
$_panierDAO2 = new PanierDAO($cnx);
$_erreur    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::verifier();
    $_email = trim($_POST['email'] ?? '');
    $_mdp   = trim($_POST['mdp']   ?? '');

    if ($_email === '' || $_mdp === '') {
        $_erreur = 'Email et mot de passe requis.';
    } else {
        // getClientCompletParEmail retourne un tableau avec la colonne mot_de_passe (hash Argon2id)
        $_client = $_clientDAO->getClientCompletParEmail($_email);
        if ($_client !== null && Password::verify($_mdp, $_client['mot_de_passe'])) {
            // Renouvellement de l'ID de session pour éviter la fixation
            session_regenerate_id(true);
            $_SESSION['client'] = [
                'id_client' => (int) $_client['id_client'],
                'nom'       => $_client['nom_client'],
                'prenom'    => $_client['prenom_client'],
                'email'     => $_client['email_client'],
            ];
            // Fusion panier anonyme → panier client
            $_panierDAO2->fusionnerPanier($_SESSION['id_session'], $_client['id_client']);

            $redirect = $_GET['redirect'] ?? 'accueil';
            header('Location: /index_.php?page=' . urlencode($redirect));
            exit;
        } else {
            $_erreur = 'Email ou mot de passe incorrect.';
        }
    }
}
?>

<section class="py-5">
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-9 col-md-6 col-lg-5">

                <h2 class="fw-bold mb-1 text-center">Se connecter</h2>
                <p class="text-muted text-center mb-4">
                    Pas encore de compte ?
                    <a href="/index_.php?page=compte/inscription">S'inscrire</a>
                </p>

                <?php if ($_erreur): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($_erreur) ?></div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm p-4">
                    <form method="post" id="form-login">
                        <input type="hidden" name="csrf_token"
                               value="<?= $_SESSION['csrf_token'] ?>">
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="email">
                                Adresse email
                            </label>
                            <input type="email" id="email" name="email"
                                   class="form-control" required autocomplete="username"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold" for="mdp">
                                Mot de passe
                            </label>
                            <input type="password" id="mdp" name="mdp"
                                   class="form-control" required autocomplete="current-password">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</section>

