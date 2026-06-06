<?php

declare(strict_types=1);

$_msgDAO = new MessageContactDAO($cnx);

$_succes  = false;
$_erreurs = [];

// Pré-remplissage si client connecté
$_nomDef   = '';
$_emailDef = '';
$_idClient = null;

if (isset($_SESSION['client'])) {
    $_idClient = (int)    $_SESSION['client']['id_client'];
    $_nomDef   = trim(($_SESSION['client']['prenom'] ?? '') . ' ' . ($_SESSION['client']['nom'] ?? ''));
    $_emailDef = $_SESSION['client']['email'] ?? '';
}

// Traitement POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::verifier();
    $_nom     = trim($_POST['nom']     ?? '');
    $_email   = trim($_POST['email']   ?? '');
    $_sujet   = trim($_POST['sujet']   ?? '');
    $_contenu = trim($_POST['contenu'] ?? '');

    if ($_nom     === '') $_erreurs[] = 'Le nom est requis.';
    if ($_email   === '' || !filter_var($_email, FILTER_VALIDATE_EMAIL)) {
        $_erreurs[] = 'Email invalide.';
    }
    if ($_sujet   === '') $_erreurs[] = 'Le sujet est requis.';
    if ($_contenu === '') $_erreurs[] = 'Le message est requis.';

    if (empty($_erreurs)) {
        $_ret = $_msgDAO->ajouterMessage($_idClient, $_nom, $_email, $_sujet, $_contenu);
        if ($_ret > 0) {
            $_succes = true;
        } else {
            $_erreurs[] = 'Erreur lors de l\'envoi. Réessayez.';
        }
    }
}
?>

<section class="py-5">
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-12 col-md-7">

                <h2 class="fw-bold mb-1">Contactez-nous</h2>
                <p class="text-muted mb-4">
                    Une question ? Notre équipe vous répond sous 24h.
                </p>

                <?php if ($_succes): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>
                        Message envoyé ! Nous vous répondrons dans les plus brefs délais.
                    </div>
                <?php else: ?>

                    <?php foreach ($_erreurs as $_e): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($_e) ?></div>
                    <?php endforeach; ?>

                    <div class="card border-0 shadow-sm p-4">
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="nom">Nom</label>
                                <input type="text" id="nom" name="nom" class="form-control"
                                       required
                                       value="<?= htmlspecialchars($_POST['nom'] ?? $_nomDef) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control"
                                       required
                                       value="<?= htmlspecialchars($_POST['email'] ?? $_emailDef) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="sujet">Sujet</label>
                                <input type="text" id="sujet" name="sujet" class="form-control"
                                       required
                                       value="<?= htmlspecialchars($_POST['sujet'] ?? '') ?>">
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold" for="contenu">Message</label>
                                <textarea id="contenu" name="contenu" class="form-control"
                                          rows="6" required><?= htmlspecialchars($_POST['contenu'] ?? '') ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-send me-2"></i>Envoyer le message
                            </button>
                        </form>
                    </div>

                <?php endif; ?>

            </div>
        </div>
    </div>
</section>
