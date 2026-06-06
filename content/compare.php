<?php

declare(strict_types=1);


$_varDAO3 = new VarianteDAO($cnx);

$_idsCompare = $_SESSION['compare'] ?? [];
$_variantes = [];
foreach ($_idsCompare as $_id) {
    $_row = $_varDAO3->getVarianteCatalogueParId((int) $_id);
    if ($_row !== null) {
        $_variantes[] = $_row;
    }
}
?>

<!-- Breadcrumb -->
<section class="py-3 bg-light border-bottom">
    <div class="container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="/index_.php?page=accueil">Accueil</a>
                </li>
                <li class="breadcrumb-item active">Comparateur</li>
            </ol>
        </nav>
    </div>
</section>

<section class="py-5">
    <div class="container-xl">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">Comparateur de produits</h2>
            <?php if (!empty($_variantes)): ?>
                <button type="button"
                        class="btn btn-outline-danger btn-sm"
                        id="btn-vider-compare">
                    <i class="bi bi-trash me-1"></i>Tout vider
                </button>
            <?php endif; ?>
        </div>

        <?php if (empty($_variantes)): ?>
            <div class="alert alert-info">
                Aucun produit dans votre comparateur. Ajoutez-en depuis le catalogue
                ou les fiches produit (icône <i class="bi bi-bar-chart"></i>).
            </div>
            <a href="/index_.php?page=catalogue" class="btn btn-primary">
                <i class="bi bi-grid-3x3-gap-fill me-1"></i>Voir le catalogue
            </a>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table ss-table-compare align-middle">
                    <tbody>

                        <!-- Image -->
                        <tr>
                            <th scope="row" class="text-muted small">Image</th>
                            <?php foreach ($_variantes as $_v): ?>
                                <td class="text-center">
                                    <?php if (!empty($_v['image_principale'])): ?>
                                        <img src="<?= htmlspecialchars(ImageHelper::urlThumbnail($_v['image_principale'])) ?>"
                                             class="ss-compare-img"
                                             alt="<?= htmlspecialchars($_v['nom_variante']) ?>">
                                    <?php else: ?>
                                        <div class="ss-compare-img d-flex align-items-center justify-content-center bg-light">
                                            <i class="bi bi-image text-secondary fs-2"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>

                        <!-- Nom -->
                        <tr>
                            <th scope="row" class="text-muted small">Produit</th>
                            <?php foreach ($_variantes as $_v): ?>
                                <td>
                                    <a href="/index_.php?page=fiche_produit&id=<?= (int) $_v['id_variante'] ?>"
                                       class="text-decoration-none fw-semibold">
                                        <?= htmlspecialchars($_v['nom_produit']) ?>
                                    </a>
                                    <div class="text-muted small">
                                        <?= htmlspecialchars($_v['nom_variante']) ?>
                                    </div>
                                </td>
                            <?php endforeach; ?>
                        </tr>

                        <!-- Catégorie -->
                        <tr>
                            <th scope="row" class="text-muted small">Catégorie</th>
                            <?php foreach ($_variantes as $_v): ?>
                                <td><?= htmlspecialchars($_v['nom_categorie'] ?? '—') ?></td>
                            <?php endforeach; ?>
                        </tr>

                        <!-- Prix -->
                        <tr>
                            <th scope="row" class="text-muted small">Prix</th>
                            <?php foreach ($_variantes as $_v): ?>
                                <td>
                                    <?php if (!empty($_v['taux_reduction'])): ?>
                                        <div class="text-muted text-decoration-line-through small">
                                            <?= number_format((float) $_v['prix'], 2, ',', ' ') ?> €
                                        </div>
                                    <?php endif; ?>
                                    <div class="fw-bold text-primary fs-5">
                                        <?= number_format((float) $_v['prix_final'], 2, ',', ' ') ?> €
                                    </div>
                                    <?php if (!empty($_v['taux_reduction'])): ?>
                                        <span class="badge bg-danger">
                                            -<?= (int) $_v['taux_reduction'] ?>%
                                        </span>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>

                        <!-- Stock -->
                        <tr>
                            <th scope="row" class="text-muted small">Stock</th>
                            <?php foreach ($_variantes as $_v): ?>
                                <td>
                                    <?php if ((int) $_v['stock'] > 0): ?>
                                        <span class="text-success">
                                            <i class="bi bi-check-circle me-1"></i>
                                            <?= (int) $_v['stock'] ?> en stock
                                        </span>
                                    <?php else: ?>
                                        <span class="text-danger">
                                            <i class="bi bi-x-circle me-1"></i>Rupture
                                        </span>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>

                        <!-- Note -->
                        <tr>
                            <th scope="row" class="text-muted small">Note</th>
                            <?php foreach ($_variantes as $_v): ?>
                                <td>
                                    <?php if ((int) ($_v['nb_avis'] ?? 0) > 0): ?>
                                        <?php $_n = (float) $_v['note_moyenne']; ?>
                                        <?php for ($_s = 1; $_s <= 5; $_s++): ?>
                                            <i class="bi bi-star<?= $_s <= $_n ? '-fill text-warning' : ' text-secondary' ?>"></i>
                                        <?php endfor; ?>
                                        <span class="text-muted small ms-1">
                                            (<?= (int) $_v['nb_avis'] ?>)
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small">Pas d'avis</span>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>

                        <!-- SKU -->
                        <?php if (array_key_exists('sku', $_variantes[0])): ?>
                            <tr>
                                <th scope="row" class="text-muted small">SKU</th>
                                <?php foreach ($_variantes as $_v): ?>
                                    <td class="text-muted small font-monospace">
                                        <?= htmlspecialchars($_v['sku'] ?? '—') ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endif; ?>

                        <!-- Actions -->
                        <tr>
                            <th scope="row" class="text-muted small">Actions</th>
                            <?php foreach ($_variantes as $_v): ?>
                                <td>
                                    <div class="d-grid gap-2">
                                        <a href="/index_.php?page=fiche_produit&id=<?= (int) $_v['id_variante'] ?>"
                                           class="btn btn-sm btn-primary">
                                            Voir le produit
                                        </a>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger ss-btn-retirer-compare"
                                                data-id-variante="<?= (int) $_v['id_variante'] ?>"
                                                data-reload-on-remove="1"
                                                title="Retirer du comparateur">
                                            <i class="bi bi-trash me-1"></i>Retirer
                                        </button>
                                    </div>
                                </td>
                            <?php endforeach; ?>
                        </tr>

                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>
