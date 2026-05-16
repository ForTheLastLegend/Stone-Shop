<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/php/utils/_images.php';

$_varDAO2  = new VarianteDAO($cnx);
$_catDAO2  = new CategorieDAO($cnx);
$_listeDAO = new ListeEnvieDAO($cnx);

$_idCat    = isset($_GET['id_categorie']) ? (int) $_GET['id_categorie'] : null;

// Catalogue depuis la vue (contient prix_final, taux_reduction, image_principale)
$_produits = $_varDAO2->getCatalogueComplet() ?? [];

// Filtrage catégorie côté PHP
if ($_idCat !== null) {
    $_produits = array_values(array_filter(
        $_produits,
        fn($p) => (int) $p['id_categorie'] === $_idCat
    ));
}

$_idsEnListe = [];
foreach ($_produits as $_p) {
    if ($_listeDAO->estEnListe($_SESSION['id_session'], (int) $_p['id_variante'])) {
        $_idsEnListe[(int) $_p['id_variante']] = true;
    }
}

$_categories = $_catDAO2->getCategoriesRacine() ?? [];
$_nomCat     = '';
foreach ($_categories as $_c) {
    if ($_idCat !== null && (int) $_c->id_categorie === $_idCat) {
        $_nomCat = $_c->nom_categorie;
        break;
    }
}
$_titrePage = $_nomCat ?: 'Catalogue';
?>

<!-- Breadcrumb -->
<section class="py-3 bg-light border-bottom">
    <div class="container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="/index_.php?page=accueil">Accueil</a>
                </li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($_titrePage) ?></li>
            </ol>
        </nav>
    </div>
</section>

<section class="py-5">
    <div class="container-xl">
        <div class="row g-4">

            <!-- Sidebar catégories -->
            <aside class="col-12 col-lg-2">
                <h6 class="fw-bold text-uppercase text-secondary mb-3">Catégories</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="/index_.php?page=catalogue"
                           class="text-decoration-none
                                  <?= $_idCat === null ? 'fw-bold text-primary' : 'text-secondary' ?>">
                            Toutes
                        </a>
                    </li>
                    <?php foreach ($_categories as $_cat): ?>
                        <li class="mb-2">
                            <a href="/index_.php?page=catalogue&id_categorie=<?= (int) $_cat->id_categorie ?>"
                               class="list-group-item list-group-item-action d-flex align-items-center
                                      <?= $_idCat === (int) $_cat->id_categorie
                                            ? 'fw-bold text-primary'
                                            : 'text-secondary' ?>">
                                <?= htmlspecialchars($_cat->nom_categorie) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </aside>

            <!-- Grille produits -->
            <div class="col-12 col-lg-10">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold mb-0"><?= htmlspecialchars($_titrePage) ?></h4>
                    <span class="text-muted small"><?= count($_produits) ?> article(s)</span>
                </div>

                <?php if (empty($_produits)): ?>
                    <div class="alert alert-info">Aucun produit dans cette catégorie.</div>
                <?php else: ?>
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-3">
                        <?php foreach ($_produits as $_v): ?>
                            <div class="col">
                                <div class="card h-100 product-card shadow-sm border-0 position-relative">
                                    <?php $_actif = !empty($_idsEnListe[(int) $_v['id_variante']]); ?>
                                    <button type="button"
                                            class="btn-wishlist position-absolute top-0 end-0 m-2 ss-z-2 <?= $_actif ? 'is-active' : '' ?>"
                                            data-id-variante="<?= (int) $_v['id_variante'] ?>"
                                            title="<?= $_actif ? 'Retirer de la liste d\'envie' : 'Ajouter à la liste d\'envie' ?>">
                                        <i class="bi <?= $_actif ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                                    </button>
                                    <?php if (!empty($_v['taux_reduction'])): ?>
                                        <span class="badge bg-danger position-absolute m-2 z-1 ss-pos-tr-46">
                                            -<?= (int) $_v['taux_reduction'] ?>%
                                        </span>
                                    <?php endif; ?>
                                    <div class="product-img-wrap">
                                        <?php if (!empty($_v['image_principale'])): ?>
                                            <img src="<?= htmlspecialchars(url_thumbnail($_v['image_principale'])) ?>"
                                                 class="product-img"
                                                 alt="<?= htmlspecialchars($_v['nom_variante']) ?>">
                                        <?php else: ?>
                                            <div class="product-img-placeholder d-flex align-items-center
                                                        justify-content-center bg-light">
                                                <i class="bi bi-image text-secondary fs-1"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <p class="text-muted small mb-1">
                                            <?= htmlspecialchars($_v['nom_categorie'] ?? '') ?>
                                        </p>
                                        <h6 class="card-title fw-semibold mb-1">
                                            <?= htmlspecialchars($_v['nom_produit']) ?>
                                        </h6>
                                        <p class="text-muted small mb-1">
                                            <?= htmlspecialchars($_v['nom_variante']) ?>
                                        </p>
                                        <?php if ((int) ($_v['nb_avis'] ?? 0) > 0): ?>
                                            <div class="small mb-2">
                                                <?php $_n = (float) $_v['note_moyenne']; ?>
                                                <?php for ($_s = 1; $_s <= 5; $_s++): ?>
                                                    <i class="bi bi-star<?= $_s <= $_n ? '-fill text-warning' : ' text-secondary' ?>"></i>
                                                <?php endfor; ?>
                                                <span class="text-muted ms-1">(<?= (int) $_v['nb_avis'] ?>)</span>
                                            </div>
                                        <?php else: ?>
                                            <div class="small mb-2 text-muted">
                                                <?php for ($_s = 1; $_s <= 5; $_s++): ?>
                                                    <i class="bi bi-star"></i>
                                                <?php endfor; ?>
                                                <span class="ms-1">(0)</span>
                                            </div>
                                        <?php endif; ?>
                                        <div class="mt-auto">
                                            <?php if (!empty($_v['taux_reduction'])): ?>
                                                <span class="text-muted text-decoration-line-through small me-1">
                                                    <?= number_format((float) $_v['prix'], 2, ',', ' ') ?> €
                                                </span>
                                            <?php endif; ?>
                                            <span class="fw-bold text-primary fs-5">
                                                <?= number_format((float) $_v['prix_final'], 2, ',', ' ') ?> €
                                            </span>
                                            <?php if ((int) $_v['stock'] === 0): ?>
                                                <span class="badge bg-secondary-subtle text-secondary mt-2 d-inline-block">
                                                    Rupture de stock
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <a href="/index_.php?page=fiche_produit&id=<?= (int) $_v['id_variante'] ?>"
                                           class="stretched-link"
                                           aria-label="Voir <?= htmlspecialchars($_v['nom_produit']) ?>"></a>
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
