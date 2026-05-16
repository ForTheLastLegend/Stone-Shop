<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/php/utils/_images.php';

$_varDAO  = new VarianteDAO($cnx);
$_catDAO2 = new CategorieDAO($cnx);

$_catalogue = $_varDAO->getCatalogueComplet() ?? [];

// Produit aléatoire pour la hero banner (parmi ceux avec une image)
$_heroCandidats = array_values(array_filter(
    $_catalogue,
    static fn(array $v) => !empty($v['image_principale'])
));
$_heroProduit = $_heroCandidats !== [] ? $_heroCandidats[array_rand($_heroCandidats)] : null;

$_vedettes = array_slice($_catalogue, 0, 5);

$_categories = $_catDAO2->getCategoriesRacine() ?? [];
$_categories = array_slice($_categories, 0, 5);

$_listeDAO   = new ListeEnvieDAO($cnx);
$_idsEnListe = [];
foreach ($_vedettes as $_v) {
    if ($_listeDAO->estEnListe($_SESSION['id_session'], (int) $_v['id_variante'])) {
        $_idsEnListe[(int) $_v['id_variante']] = true;
    }
}

$_iconeCategorie = function (string $nom): string {
    $n = mb_strtolower($nom);
    if (str_contains($n, 'tele') && (str_contains($n, 'phone') || str_contains($n, 'mobile'))) return 'bi-phone';
    if (str_contains($n, 'phone'))      return 'bi-phone';
    if (str_contains($n, 'tele') || str_contains($n, 'tv')) return 'bi-tv';
    if (str_contains($n, 'ordinateur') || str_contains($n, 'pc') || str_contains($n, 'laptop')) return 'bi-pc-display';
    if (str_contains($n, 'audio') || str_contains($n, 'casque')) return 'bi-headphones';
    if (str_contains($n, 'gaming') || str_contains($n, 'jeu'))   return 'bi-controller';
    if (str_contains($n, 'accessoire')) return 'bi-plug';
    if (str_contains($n, 'domotique') || str_contains($n, 'maison')) return 'bi-house-gear';
    if (str_contains($n, 'photo') || str_contains($n, 'camera')) return 'bi-camera';
    return 'bi-tag';
};
?>

<!-- Hero Banner -->
<section class="hero-banner text-white d-flex align-items-center">
    <div class="container-xl">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <span class="badge bg-danger mb-3 text-uppercase fs-6">Nouveauté</span>
                <h1 class="display-4 fw-bold mb-3">
                    La tech que tu mérites,<br>au prix que tu veux.
                </h1>
                <p class="lead mb-4">
                    Smartphones, PC, gaming, audio — tout ce qu'il faut,
                    livré vite, au bon prix.
                </p>
                <a href="/index_.php?page=catalogue" class="btn btn-light btn-lg me-3">
                    <i class="bi bi-grid me-2"></i>Voir le catalogue
                </a>
                <a href="/index_.php?page=contact" class="btn btn-outline-light btn-lg">
                    Nous contacter
                </a>
            </div>
            <div class="col-lg-6 text-center d-none d-lg-block">
                <?php if ($_heroProduit !== null): ?>
                    <a href="/index_.php?page=fiche_produit&id=<?= (int) $_heroProduit['id_variante'] ?>"
                       class="hero-product-link"
                       title="<?= htmlspecialchars($_heroProduit['nom_produit'] . ' — ' . $_heroProduit['nom_variante']) ?>">
                        <div class="hero-product-frame">
                            <span class="hero-product-tag">
                                <i class="bi bi-stars me-1"></i>
                                <?= htmlspecialchars($_heroProduit['nom_produit']) ?>
                            </span>
                            <img src="<?= htmlspecialchars($_heroProduit['image_principale']) ?>"
                                 alt="<?= htmlspecialchars($_heroProduit['nom_produit']) ?>"
                                 class="hero-product-img">
                        </div>
                    </a>
                <?php else: ?>
                    <i class="bi bi-pc-display-horizontal hero-icon"></i>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Produits vedettes : 1 carte-label + 5 produits -->
<section class="py-5 bg-light">
    <div class="container-xl">
        <div class="row g-3 row-cols-2 row-cols-sm-3 row-cols-md-3 row-cols-lg-6">

            <!-- Carte-label -->
            <div class="col">
                <div class="label-card h-100">
                    <h3>Nos produits<br>favoris</h3>
                    <a href="/index_.php?page=catalogue" class="label-cta">
                        Voir tout <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            <?php foreach ($_vedettes as $v): ?>
                <div class="col">
                    <div class="card h-100 product-card shadow-sm border-0">
                        <?php if ((int) $v['stock'] > 0): ?>
                            <span class="badge bg-success-subtle text-success position-absolute top-0 start-0 m-2 z-1 small">
                                <i class="bi bi-check-circle me-1"></i>En stock
                            </span>
                        <?php endif; ?>
                        <?php $_actif = !empty($_idsEnListe[(int) $v['id_variante']]); ?>
                        <button type="button"
                                class="btn-wishlist position-absolute top-0 end-0 m-2 ss-z-2 <?= $_actif ? 'is-active' : '' ?>"
                                data-id-variante="<?= (int) $v['id_variante'] ?>"
                                title="<?= $_actif ? 'Retirer de la liste d\'envie' : 'Ajouter à la liste d\'envie' ?>">
                            <i class="bi <?= $_actif ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                        </button>
                        <?php if (!empty($v['taux_reduction'])): ?>
                            <span class="badge bg-danger position-absolute m-2 z-1 ss-pos-tr-46">
                                -<?= (int) $v['taux_reduction'] ?>%
                            </span>
                        <?php endif; ?>
                        <div class="product-img-wrap">
                            <?php if (!empty($v['image_principale'])): ?>
                                <img src="<?= htmlspecialchars(url_thumbnail($v['image_principale'])) ?>"
                                     class="product-img"
                                     alt="<?= htmlspecialchars($v['nom_variante']) ?>">
                            <?php else: ?>
                                <div class="product-img-placeholder d-flex align-items-center
                                            justify-content-center h-100">
                                    <i class="bi bi-image text-secondary fs-1"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <?php if ((int) ($v['nb_avis'] ?? 0) > 0): ?>
                                <div class="small mb-1">
                                    <?php $_n = (float) $v['note_moyenne']; ?>
                                    <?php for ($_s = 1; $_s <= 5; $_s++): ?>
                                        <i class="bi bi-star<?= $_s <= $_n ? '-fill text-warning' : ' text-secondary' ?>"></i>
                                    <?php endfor; ?>
                                    <span class="text-muted ms-1">(<?= (int) $v['nb_avis'] ?>)</span>
                                </div>
                            <?php else: ?>
                                <div class="small mb-1 text-muted">
                                    <?php for ($_s = 1; $_s <= 5; $_s++): ?>
                                        <i class="bi bi-star"></i>
                                    <?php endfor; ?>
                                    <span class="ms-1">(0)</span>
                                </div>
                            <?php endif; ?>
                            <h6 class="card-title fw-semibold mb-1 small">
                                <?= htmlspecialchars($v['nom_produit']) ?>
                            </h6>
                            <div class="mt-auto">
                                <?php if (!empty($v['taux_reduction'])): ?>
                                    <span class="text-muted text-decoration-line-through small me-1">
                                        <?= number_format((float) $v['prix'], 2, ',', ' ') ?> €
                                    </span>
                                <?php endif; ?>
                                <div class="fw-bold text-primary fs-6">
                                    <?= number_format((float) $v['prix_final'], 2, ',', ' ') ?> €
                                </div>
                            </div>
                            <a href="/index_.php?page=fiche_produit&id=<?= (int) $v['id_variante'] ?>"
                               class="stretched-link" aria-label="Voir le produit"></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>
</section>

<!-- Nos catégories : 1 carte-label + 5 catégories -->
<?php if (!empty($_categories)): ?>
<section class="py-5">
    <div class="container-xl">
        <div class="row g-3 row-cols-2 row-cols-sm-3 row-cols-md-3 row-cols-lg-6">

            <div class="col">
                <div class="label-card h-100">
                    <h3>Nos<br>catégories</h3>
                    <a href="/index_.php?page=catalogue" class="label-cta">
                        Voir tout <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            <?php foreach ($_categories as $cat): ?>
                <div class="col">
                    <a href="/index_.php?page=catalogue&id_categorie=<?= (int) $cat->id_categorie ?>"
                       class="card border-0 shadow-sm text-center p-4 h-100 text-decoration-none
                              category-card d-flex flex-column align-items-center justify-content-center">
                        <i class="bi <?= $_iconeCategorie($cat->nom_categorie) ?> fs-1 text-primary mb-3"></i>
                        <span class="fw-semibold text-dark">
                            <?= htmlspecialchars($cat->nom_categorie) ?>
                        </span>
                    </a>
                </div>
            <?php endforeach; ?>

        </div>

        <div class="text-center mt-5">
            <a href="/index_.php?page=catalogue" class="btn-pill-cta">
                Voir le catalogue en entier
            </a>
        </div>
    </div>
</section>
<?php endif; ?>
