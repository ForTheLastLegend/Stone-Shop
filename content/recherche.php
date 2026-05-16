<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/php/utils/_images.php';

$_q       = trim($_GET['q'] ?? '');
$_varDAO2 = new VarianteDAO($cnx);

$_resultats = [];
if ($_q !== '') {
    $_catalogue = $_varDAO2->getCatalogueComplet() ?? [];
    foreach ($_catalogue as $_row) {
        if (stripos($_row['nom_produit'], $_q) !== false
            || stripos($_row['nom_variante'], $_q) !== false) {
            $_resultats[] = $_row;
        }
    }
}
?>

<section class="py-5">
    <div class="container-xl">

        <h2 class="fw-bold mb-1">Résultats de recherche</h2>
        <p class="text-muted mb-4">
            <?= $_q !== ''
                ? count($_resultats) . ' résultat(s) pour « ' . htmlspecialchars($_q) . ' »'
                : 'Entrez un terme de recherche.' ?>
        </p>

        <?php if (empty($_resultats) && $_q !== ''): ?>
            <div class="alert alert-info">
                Aucun produit trouvé pour « <?= htmlspecialchars($_q) ?> ».
            </div>
            <a href="/index_.php?page=catalogue" class="btn btn-outline-primary">
                Voir tout le catalogue
            </a>
        <?php elseif (!empty($_resultats)): ?>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-3">
                <?php foreach ($_resultats as $_v): ?>
                    <div class="col">
                        <div class="card h-100 product-card shadow-sm border-0 position-relative">
                            <?php if (!empty($_v['taux_reduction'])): ?>
                                <span class="badge bg-danger position-absolute top-0 end-0 m-2 z-1">
                                    -<?= (int) $_v['taux_reduction'] ?>%
                                </span>
                            <?php endif; ?>
                            <div class="product-img-wrap">
                                <?php if (!empty($_v['image_principale'])): ?>
                                    <img src="<?= htmlspecialchars(url_thumbnail($_v['image_principale'])) ?>"
                                         class="card-img-top product-img"
                                         alt="<?= htmlspecialchars($_v['nom_variante']) ?>">
                                <?php else: ?>
                                    <div class="product-img-placeholder d-flex align-items-center
                                                justify-content-center bg-light">
                                        <i class="bi bi-image text-secondary fs-1"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title fw-semibold mb-1">
                                    <?= htmlspecialchars($_v['nom_produit']) ?>
                                </h6>
                                <p class="text-muted small mb-2">
                                    <?= htmlspecialchars($_v['nom_variante']) ?>
                                </p>
                                <div class="mt-auto">
                                    <?php if (!empty($_v['taux_reduction'])): ?>
                                        <span class="text-muted text-decoration-line-through small me-1">
                                            <?= number_format((float) $_v['prix'], 2, ',', ' ') ?> €
                                        </span>
                                    <?php endif; ?>
                                    <span class="fw-bold text-primary">
                                        <?= number_format((float) $_v['prix_final'], 2, ',', ' ') ?> €
                                    </span>
                                </div>
                                <a href="/index_.php?page=fiche_produit&id=<?= (int) $_v['id_variante'] ?>"
                                   class="btn btn-primary btn-sm mt-2 w-100">
                                    Voir le produit
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>
