<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/php/utils/_images.php';

$_idVariante = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$_varDAO2 = new VarianteDAO($cnx);
$_imgDAO = new ImageProduitDAO($cnx);
$_avisDAO = new AvisDAO($cnx);
$_panierDAO2 = new PanierDAO($cnx);
$_listeDAO = new ListeEnvieDAO($cnx);
$_enListe = $_listeDAO->estEnListe($_SESSION['id_session'], $_idVariante);

$_catalogue = $_varDAO2->getCatalogueComplet() ?? [];
$_variante = null;
foreach ($_catalogue as $_row) {
    if ((int) $_row['id_variante'] === $_idVariante) {
        $_variante = $_row;
        break;
    }
}

if ($_variante === null) {
    echo '<div class="container py-5"><div class="alert alert-danger">Produit introuvable.</div></div>';
    return;
}

$_images = $_imgDAO->getImagesByVariante($_idVariante) ?? [];
$_avis = $_avisDAO->getAvisApprouvesByVariante($_idVariante) ?? [];

$_variantesGroupe = [];
foreach ($_catalogue as $_row) {
    if ((int) $_row['id_produit'] === (int) $_variante['id_produit']) {
        $_variantesGroupe[] = $_row;
    }
}

// Produits similaires : même catégorie, produit différent (les autres variantes
// du même produit sont déjà couvertes par le sélecteur ci-dessus).
// Déduplication par id_produit : une seule variante affichée par produit.
$_similaires = [];
$_idsProdVus = [(int) $_variante['id_produit'] => true];
foreach ($_catalogue as $_row) {
    $_idProd = (int) $_row['id_produit'];
    if ((int) $_row['id_categorie'] === (int) $_variante['id_categorie']
        && !isset($_idsProdVus[$_idProd])) {
        $_similaires[] = $_row;
        $_idsProdVus[$_idProd] = true;
        if (count($_similaires) === 4) {
            break;
        }
    }
}

$_msgPanier = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_panier'])) {
    verifier_csrf();
    $_qte = max(1, (int) ($_POST['qte'] ?? 1));
    $_panierDAO2->ajouterOuMaj($_SESSION['id_session'], $_idVariante, $_qte,
        isset($_SESSION['client']) ? (int) $_SESSION['client']['id_client'] : null);
    $_msgPanier = 'success';
}

$_noteMoy = 0;
if (!empty($_avis)) {
    $_noteMoy = round(array_sum(array_column($_avis, 'note')) / count($_avis), 1);
}
?>

<!-- Breadcrumb -->
<section class="py-3 bg-light border-bottom">
    <div class="container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/index_.php?page=accueil">Accueil</a></li>
                <li class="breadcrumb-item"><a href="/index_.php?page=catalogue">Catalogue</a></li>
                <li class="breadcrumb-item active">
                    <?= htmlspecialchars($_variante['nom_produit']) ?>
                </li>
            </ol>
        </nav>
    </div>
</section>

<section class="py-5">
    <div class="container-xl">

        <?php if ($_msgPanier === 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-cart-check me-2"></i>Article ajouté au panier !
                <a href="/index_.php?page=panier" class="alert-link ms-2">Voir le panier</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-5">

            <!-- Galerie -->
            <div class="col-12 col-md-5">
                <?php if (!empty($_images)): ?>
                    <div id="carouselProduit" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner rounded shadow-sm">
                            <?php foreach ($_images as $_i => $_img): ?>
                                <div class="carousel-item <?= $_i === 0 ? 'active' : '' ?>">
                                    <img src="<?= htmlspecialchars($_img->url_image) ?>"
                                         class="d-block w-100 product-detail-img"
                                         alt="<?= htmlspecialchars($_img->alt_text ?? '') ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($_images) > 1): ?>
                            <button class="carousel-control-prev" type="button"
                                    data-bs-target="#carouselProduit" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button"
                                    data-bs-target="#carouselProduit" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="product-img-placeholder-lg bg-light rounded d-flex
                                align-items-center justify-content-center shadow-sm">
                        <i class="bi bi-image text-secondary ss-icon-5rem"></i>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Infos produit -->
            <div class="col-12 col-md-7">
                <p class="text-muted mb-1">
                    <?= htmlspecialchars($_variante['nom_categorie'] ?? '') ?>
                </p>
                <h1 class="h2 fw-bold mb-1">
                    <?= htmlspecialchars($_variante['nom_produit']) ?>
                </h1>
                <h2 class="h5 text-muted mb-3">
                    <?= htmlspecialchars($_variante['nom_variante']) ?>
                </h2>

                <!-- Note -->
                <?php if (!empty($_avis)): ?>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <?php for ($_s = 1; $_s <= 5; $_s++): ?>
                            <i class="bi bi-star<?= $_s <= $_noteMoy ? '-fill text-warning' : ' text-secondary' ?>"></i>
                        <?php endfor; ?>
                        <span class="text-muted small">
                            (<?= count($_avis) ?> avis)
                        </span>
                    </div>
                <?php endif; ?>

                <!-- Prix -->
                <div class="mb-4">
                    <?php if (!empty($_variante['taux_reduction'])): ?>
                        <span class="text-muted text-decoration-line-through me-2 fs-5">
                            <?= number_format((float) $_variante['prix'], 2, ',', ' ') ?> €
                        </span>
                        <span class="badge bg-danger me-2">
                            -<?= (int) $_variante['taux_reduction'] ?>%
                        </span>
                    <?php endif; ?>
                    <span class="fw-bold text-primary fs-2">
                        <?= number_format((float) $_variante['prix_final'], 2, ',', ' ') ?> €
                    </span>
                </div>

                <!-- Stock -->
                <p class="mb-3">
                    <?php if ((int) $_variante['stock'] > 0): ?>
                        <span class="text-success fw-semibold">
                            <i class="bi bi-check-circle me-1"></i>
                            En stock (<?= (int) $_variante['stock'] ?> disponibles)
                        </span>
                    <?php else: ?>
                        <span class="text-danger fw-semibold">
                            <i class="bi bi-x-circle me-1"></i>Rupture de stock
                        </span>
                    <?php endif; ?>
                </p>

                <!-- Sélecteur variantes -->
                <?php if (count($_variantesGroupe) > 1): ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Variante :</label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($_variantesGroupe as $_vg): ?>
                                <a href="/index_.php?page=fiche_produit&id=<?= (int) $_vg['id_variante'] ?>"
                                   class="btn btn-sm
                                          <?= (int) $_vg['id_variante'] === $_idVariante
                                                ? 'btn-primary'
                                                : 'btn-outline-secondary' ?>">
                                    <?= htmlspecialchars($_vg['nom_variante']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Formulaire ajout panier + wishlist -->
                <?php if ((int) $_variante['stock'] > 0): ?>
                    <div class="d-flex align-items-center gap-3 mt-4">
                        <form method="post" class="d-flex align-items-center gap-3 flex-grow-1">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <div class="ss-thumb-90">
                                <input type="number" name="qte" class="form-control"
                                       value="1" min="1" max="<?= (int) $_variante['stock'] ?>">
                            </div>
                            <button type="submit" name="ajouter_panier"
                                    class="btn btn-primary btn-lg flex-grow-1">
                                <i class="bi bi-cart-plus me-2"></i>Ajouter au panier
                            </button>
                        </form>
                        <button type="button"
                                class="btn-wishlist btn-wishlist-lg <?= $_enListe ? 'is-active' : '' ?>"
                                data-id-variante="<?= $_idVariante ?>"
                                title="<?= $_enListe ? 'Retirer de la liste d\'envie' : 'Ajouter à la liste d\'envie' ?>">
                            <i class="bi <?= $_enListe ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                        </button>
                    </div>
                <?php else: ?>
                    <div class="d-flex align-items-center gap-3 mt-4">
                        <button class="btn btn-secondary btn-lg flex-grow-1" disabled>
                            Rupture de stock
                        </button>
                        <button type="button"
                                class="btn-wishlist btn-wishlist-lg <?= $_enListe ? 'is-active' : '' ?>"
                                data-id-variante="<?= $_idVariante ?>"
                                title="<?= $_enListe ? 'Retirer de la liste d\'envie' : 'Ajouter à la liste d\'envie' ?>">
                            <i class="bi <?= $_enListe ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                        </button>
                    </div>
                <?php endif; ?>

            </div><!-- /.col -->
        </div><!-- /.row -->

        <!-- Produits similaires -->
        <?php if (!empty($_similaires)): ?>
            <div class="row mt-5">
                <div class="col-12">
                    <h3 class="fw-bold mb-4">Produits similaires</h3>
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
                        <?php foreach ($_similaires as $_s): ?>
                            <div class="col">
                                <div class="card h-100 product-card shadow-sm border-0 position-relative">
                                    <?php if (!empty($_s['taux_reduction'])): ?>
                                        <span class="badge bg-danger position-absolute m-2 z-1 ss-pos-tr-46">
                                            -<?= (int) $_s['taux_reduction'] ?>%
                                        </span>
                                    <?php endif; ?>
                                    <div class="product-img-wrap">
                                        <?php if (!empty($_s['image_principale'])): ?>
                                            <img src="<?= htmlspecialchars(url_thumbnail($_s['image_principale'])) ?>"
                                                 class="product-img"
                                                 alt="<?= htmlspecialchars($_s['nom_variante']) ?>">
                                        <?php else: ?>
                                            <div class="product-img-placeholder d-flex align-items-center
                                                        justify-content-center bg-light">
                                                <i class="bi bi-image text-secondary fs-1"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h6 class="card-title fw-semibold mb-1">
                                            <?= htmlspecialchars($_s['nom_produit']) ?>
                                        </h6>
                                        <p class="text-muted small mb-2">
                                            <?= htmlspecialchars($_s['nom_variante']) ?>
                                        </p>
                                        <div class="mt-auto">
                                            <?php if (!empty($_s['taux_reduction'])): ?>
                                                <span class="text-muted text-decoration-line-through small me-1">
                                                    <?= number_format((float) $_s['prix'], 2, ',', ' ') ?> €
                                                </span>
                                            <?php endif; ?>
                                            <span class="fw-bold text-primary">
                                                <?= number_format((float) $_s['prix_final'], 2, ',', ' ') ?> €
                                            </span>
                                        </div>
                                        <a href="/index_.php?page=fiche_produit&id=<?= (int) $_s['id_variante'] ?>"
                                           class="stretched-link"
                                           aria-label="Voir <?= htmlspecialchars($_s['nom_produit']) ?>"></a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Avis clients -->
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="fw-bold mb-4">Avis clients</h3>
                <?php if (empty($_avis)): ?>
                    <p class="text-muted">Soyez le premier à laisser un avis !</p>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($_avis as $_av): ?>
                            <div class="col-12 col-md-6">
                                <div class="card border-0 shadow-sm p-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <strong>
                                            <?= htmlspecialchars($_av['titre'] ?? '') ?>
                                        </strong>
                                        <span class="text-warning">
                                            <?php for ($_s = 1; $_s <= 5; $_s++): ?>
                                                <i class="bi bi-star<?= $_s <= (int) $_av['note'] ? '-fill' : ' text-secondary' ?>"></i>
                                            <?php endfor; ?>
                                        </span>
                                    </div>
                                    <p class="text-muted small mb-1">
                                        <?= htmlspecialchars($_av['commentaire'] ?? '') ?>
                                    </p>
                                    <small class="text-secondary">
                                        <?= htmlspecialchars($_av['date_avis'] ?? '') ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div><!-- /.container-xl -->
</section>
