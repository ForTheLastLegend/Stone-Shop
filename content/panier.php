<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/php/utils/_images.php';

$_panierDAO2 = new PanierDAO($cnx);
$_transDAO = new TransporteurDAO($cnx);
$_promoDAO = new CodePromoDAO($cnx);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_csrf();
}

$_idSession = $_SESSION['id_session'];
$_idClient = isset($_SESSION['client']) ? (int) $_SESSION['client']['id_client'] : null;

$_lignes = $_panierDAO2->getContenuPanier($_idSession) ?? [];
$_transporteurs = $_transDAO->getTransporteursActifs() ?? [];

$_sousTotal = 0.0;
foreach ($_lignes as $_l) {
    $_sousTotal += (float) $_l['prix'] * (int) $_l['quantite'];
}

$_idTrans = isset($_POST['id_transporteur']) ? (int) $_POST['id_transporteur'] : null;
$_fraisTrans = 0.0;
foreach ($_transporteurs as $_t) {
    if ($_idTrans !== null && $_t->id_transporteur === $_idTrans) {
        $_fraisTrans = $_t->frais_livraison;
        break;
    }
}

$_remisePromo = 0.0;
$_msgPromo = '';
$_codePromo = trim($_POST['code_promo'] ?? '');
if ($_codePromo !== '') {
    $_cp = $_promoDAO->getCodePromoParCode($_codePromo);
    if ($_cp !== null) {
        $_remisePromo = round($_sousTotal * ($_cp->taux_reduction / 100), 2);
        $_msgPromo = 'Code appliqué : -' . (int) $_cp->taux_reduction . '%';
    } else {
        $_msgPromo = 'Code invalide ou expiré.';
    }
}

$_total = max(0.0, $_sousTotal - $_remisePromo + $_fraisTrans);
?>

<section class="py-5">
    <div class="container-xl">
        <h2 class="fw-bold mb-4"><i class="bi bi-cart3 me-2"></i>Mon panier</h2>

        <?php if (empty($_lignes)): ?>
            <div class="text-center py-5">
                <i class="bi bi-cart-x fs-1 text-secondary mb-3 d-block"></i>
                <p class="text-muted">Votre panier est vide.</p>
                <a href="/index_.php?page=catalogue" class="btn btn-primary">
                    Voir le catalogue
                </a>
            </div>
        <?php else: ?>
            <div class="row g-4">

                <!-- Tableau des articles -->
                <div class="col-12 col-lg-8">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Produit</th>
                                    <th class="text-center">Quantité</th>
                                    <th class="text-end">Prix unit.</th>
                                    <th class="text-end">Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($_lignes as $_l): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <?php if (!empty($_l['image_principale'])): ?>
                                                    <img src="<?= htmlspecialchars(url_thumbnail($_l['image_principale'])) ?>"
                                                         alt="" class="rounded ss-thumb-55-cover">
                                                <?php else: ?>
                                                    <div class="bg-light rounded d-flex align-items-center
                                                                justify-content-center ss-thumb-55">
                                                        <i class="bi bi-image text-secondary"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <div class="fw-semibold">
                                                        <?= htmlspecialchars($_l['nom_produit']) ?>
                                                    </div>
                                                    <small class="text-muted">
                                                        <?= htmlspecialchars($_l['nom_variante']) ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="number"
                                                   class="form-control form-control-sm text-center panier-qte ss-input-qte"
                                                   data-id-panier="<?= (int) $_l['id_panier'] ?>"
                                                   data-id-variante="<?= (int) $_l['id_variante'] ?>"
                                                   value="<?= (int) $_l['quantite'] ?>"
                                                   min="1">
                                        </td>
                                        <td class="text-end">
                                            <span class="prix-unitaire" data-prix="<?= (float)$_l['prix'] ?>">
                                                <?= number_format((float) $_l['prix'], 2, ',', ' ') ?> €
                                            </span>
                                        </td>
                                        <td class="text-end fw-semibold">
                                            <span class="sous-total-ligne">
                                                <?= number_format((float) $_l['prix'] * (int) $_l['quantite'], 2, ',', ' ') ?> €
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-danger btn-sm btn-remove-panier"
                                                    data-id-panier="<?= (int) $_l['id_panier'] ?>"
                                                    data-id-variante="<?= (int) $_l['id_variante'] ?>"
                                                    title="Retirer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Récapitulatif -->
                <div class="col-12 col-lg-4">
                    <div class="card border-0 shadow-sm p-4">
                        <h5 class="fw-bold mb-3">Récapitulatif</h5>

                        <!-- Transporteur -->
                        <form method="post" class="mb-3">
                            <input type="hidden" name="csrf_token"
                                   value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="code_promo"
                                   value="<?= htmlspecialchars($_codePromo) ?>">
                            <label class="form-label small fw-semibold">
                                Mode de livraison
                            </label>
                            <select name="id_transporteur" class="form-select form-select-sm mb-2"
                                    onchange="this.form.submit()">
                                <option value="">— Choisir —</option>
                                <?php foreach ($_transporteurs as $_t): ?>
                                    <option value="<?= $_t->id_transporteur ?>"
                                            <?= $_idTrans === $_t->id_transporteur ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($_t->nom_transporteur) ?>
                                        (<?= number_format($_t->frais_livraison, 2, ',', ' ') ?> €
                                        — <?= htmlspecialchars($_t->delai_estime ?? '') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <!-- Code promo -->
                            <label class="form-label small fw-semibold mt-2">Code promo</label>
                            <div class="input-group input-group-sm">
                                <input type="text" name="code_promo" class="form-control"
                                       value="<?= htmlspecialchars($_codePromo) ?>"
                                       placeholder="CODE123">
                                <button class="btn btn-outline-secondary" type="submit">
                                    Appliquer
                                </button>
                            </div>
                            <?php if ($_msgPromo): ?>
                                <small class="<?= str_contains($_msgPromo, 'invalide') ? 'text-danger' : 'text-success' ?> mt-1 d-block">
                                    <?= htmlspecialchars($_msgPromo) ?>
                                </small>
                            <?php endif; ?>
                        </form>

                        <hr>
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Sous-total</span>
                            <span class="sous-total-global"><?= number_format($_sousTotal, 2, ',', ' ') ?> €</span>
                        </div>
                        <?php if ($_remisePromo > 0): ?>
                            <div class="d-flex justify-content-between small mb-1 text-success">
                                <span>Remise code promo</span>
                                <span>-<?= number_format($_remisePromo, 2, ',', ' ') ?> €</span>
                            </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between small mb-3">
                            <span>Livraison</span>
                            <span>
                                <?= $_fraisTrans > 0
                                    ? number_format($_fraisTrans, 2, ',', ' ') . ' €'
                                    : '—' ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold fs-5 mb-4">
                            <span>Total</span>
                            <span class="text-primary total-panier">
                                <?= number_format($_total, 2, ',', ' ') ?> €
                            </span>
                        </div>

                        <?php if (isset($_SESSION['client'])): ?>
                            <a href="/index_.php?page=checkout<?= $_idTrans ? '&id_transporteur=' . $_idTrans : '' ?>"
                               class="btn btn-primary w-100">
                                <i class="bi bi-lock me-2"></i>Commander
                            </a>
                        <?php else: ?>
                            <a href="/index_.php?page=compte/login?redirect=checkout"
                               class="btn btn-primary w-100">
                                <i class="bi bi-person me-2"></i>Se connecter pour commander
                            </a>
                        <?php endif; ?>

                    </div>
                </div>

            </div><!-- /.row -->
        <?php endif; ?>
    </div>
</section>
