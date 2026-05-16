<?php

declare(strict_types=1);

require_once __DIR__ . '/../../src/php/utils/check_connexion.php';
require_once __DIR__ . '/../../src/php/utils/_images.php';

$_idClient   = (int) $_SESSION['client']['id_client'];
$_idSession  = $_SESSION['id_session'];
$_listeDAO   = new ListeEnvieDAO($cnx);
$_panierDAO2 = new PanierDAO($cnx);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_csrf();
}

// Suppression d'un article
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['retirer'])) {
    $_idListe = (int) ($_POST['id_liste_envie'] ?? 0);
    if ($_idListe > 0) {
        $_listeDAO->retirerVariante($_idListe);
    }
}

// Ajout au panier depuis la liste
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_panier'])) {
    $_idVar = (int) ($_POST['id_variante'] ?? 0);
    if ($_idVar > 0) {
        $_panierDAO2->ajouterOuMaj($_idSession, $_idVar, 1, $_idClient);
    }
}

$_liste = $_listeDAO->getListeParSession($_idSession) ?? [];
?>

<section class="py-5">
    <div class="container-xl">
        <div class="row">
            <nav class="col-12 col-md-3 mb-4">
                <?php require_once __DIR__ . '/../../src/php/utils/menu_compte.php'; ?>
            </nav>

            <div class="col-12 col-md-9">
                <h3 class="fw-bold mb-4">
                    <i class="bi bi-heart me-2 text-danger"></i>Ma liste d'envie
                </h3>

                <?php if (empty($_liste)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-heart fs-1 text-secondary mb-3 d-block"></i>
                        <p class="text-muted">Votre liste d'envie est vide.</p>
                        <a href="/index_.php?page=catalogue" class="btn btn-primary">
                            Voir le catalogue
                        </a>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($_liste as $_item): ?>
                            <div class="col-12 col-md-6">
                                <div class="card border-0 shadow-sm p-3 h-100">
                                    <div class="d-flex gap-3">
                                        <?php if (!empty($_item['image_principale'])): ?>
                                            <img src="<?= htmlspecialchars(url_thumbnail($_item['image_principale'])) ?>"
                                                 alt="" class="rounded ss-thumb-70-cover">
                                        <?php else: ?>
                                            <div class="bg-light rounded d-flex align-items-center
                                                        justify-content-center ss-thumb-70">
                                                <i class="bi bi-image text-secondary"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="flex-grow-1">
                                            <h6 class="fw-semibold mb-0">
                                                <?= htmlspecialchars($_item['nom_produit']) ?>
                                            </h6>
                                            <small class="text-muted">
                                                <?= htmlspecialchars($_item['nom_variante']) ?>
                                            </small>
                                            <div class="fw-bold text-primary mt-1">
                                                <?= number_format((float) $_item['prix_final'], 2, ',', ' ') ?> €
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2 mt-3">
                                        <form method="post" class="flex-grow-1">
                                            <input type="hidden" name="csrf_token"
                                                   value="<?= $_SESSION['csrf_token'] ?>">
                                            <input type="hidden" name="id_variante"
                                                   value="<?= (int) $_item['id_variante'] ?>">
                                            <button type="submit" name="ajouter_panier"
                                                    class="btn btn-primary btn-sm w-100">
                                                <i class="bi bi-cart-plus me-1"></i>Panier
                                            </button>
                                        </form>
                                        <form method="post">
                                            <input type="hidden" name="csrf_token"
                                                   value="<?= $_SESSION['csrf_token'] ?>">
                                            <input type="hidden" name="id_liste_envie"
                                                   value="<?= (int) $_item['id_liste_envie'] ?>">
                                            <button type="submit" name="retirer"
                                                    class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
