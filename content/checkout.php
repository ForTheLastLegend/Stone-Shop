<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/php/utils/check_connexion.php';

$_idClient   = (int) $_SESSION['client']['id_client'];
$_idSession  = $_SESSION['id_session'];

$_adresseDAO = new AdresseDAO($cnx);
$_panierDAO2 = new PanierDAO($cnx);
$_transDAO   = new TransporteurDAO($cnx);
$_cmdDAO     = new CommandeDAO($cnx);

$_lignes         = $_panierDAO2->getContenuPanier($_idSession) ?? [];
$_adresses       = $_adresseDAO->getAdressesParClient($_idClient) ?? [];
$_transporteurs  = $_transDAO->getTransporteursActifs() ?? [];

$_erreurs = [];
$_succes  = false;

// Traitement POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_csrf();
    $_idAdresseLiv  = isset($_POST['id_adresse_livraison'])  ? (int) $_POST['id_adresse_livraison']  : 0;
    $_idAdresseFact = isset($_POST['id_adresse_facturation']) ? (int) $_POST['id_adresse_facturation'] : 0;
    $_idTrans       = isset($_POST['id_transporteur'])        ? (int) $_POST['id_transporteur']        : 0;

    if ($_idAdresseLiv  === 0) $_erreurs[] = 'Adresse de livraison requise.';
    if ($_idAdresseFact === 0) $_erreurs[] = 'Adresse de facturation requise.';
    if ($_idTrans       === 0) $_erreurs[] = 'Transporteur requis.';
    if (empty($_lignes))      $_erreurs[] = 'Votre panier est vide.';

    // Vérification d'appartenance : les adresses doivent appartenir au client connecté
    $_idsAdresses = array_map(fn($a) => (int) $a->id_adresse, $_adresses);
    if ($_idAdresseLiv  !== 0 && !in_array($_idAdresseLiv,  $_idsAdresses, true)) $_erreurs[] = 'Adresse de livraison invalide.';
    if ($_idAdresseFact !== 0 && !in_array($_idAdresseFact, $_idsAdresses, true)) $_erreurs[] = 'Adresse de facturation invalide.';

    if (empty($_erreurs)) {
        // Total = somme (prix unitaire × quantité) sur toutes les lignes du panier.
        $_totalCmd = array_sum(array_map(
            fn($l) => (float) $l['prix'] * (int) $l['quantite'],
            $_lignes
        ));
        // Sérialisation des lignes pour la fonction plpgsql valider_commande.
        // Le prix figé est celui du panier au moment de l'achat.
        $_lignesJson = json_encode(array_map(fn($l) => [
            'id_variante' => (int)    $l['id_variante'],
            'qte'         => (int)    $l['quantite'],
            'prix'        => (string) $l['prix'],
        ], $_lignes), JSON_THROW_ON_ERROR);

        $_idCmd = $_cmdDAO->validerCommande(
            $_idClient, $_idAdresseLiv, $_idAdresseFact, $_idTrans,
            null, $_totalCmd, 'carte', $_idSession, $_lignesJson
        );

        if ($_idCmd > 0) {
            header('Location: /index_.php?page=confirmation_commande&id=' . $_idCmd);
            exit;
        }
        $_erreurs[] = 'Erreur lors de la création de la commande. Réessayez.';
    }
}
?>

<section class="py-5">
    <div class="container-xl">
        <h2 class="fw-bold mb-4">
            <i class="bi bi-bag-check me-2"></i>Finaliser ma commande
        </h2>

        <?php foreach ($_erreurs as $_e): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_e) ?></div>
        <?php endforeach; ?>

        <?php if (empty($_lignes)): ?>
            <div class="alert alert-info">
                Votre panier est vide.
                <a href="/index_.php?page=catalogue">Voir le catalogue</a>
            </div>
        <?php else: ?>

            <form method="post">
                <input type="hidden" name="csrf_token"
                       value="<?= $_SESSION['csrf_token'] ?>">
                <div class="row g-4">

                    <!-- Adresses -->
                    <div class="col-12 col-lg-7">

                        <div class="card border-0 shadow-sm p-4 mb-4">
                            <h5 class="fw-bold mb-3">Adresse de livraison</h5>
                            <?php if (empty($_adresses)): ?>
                                <p class="text-muted small">
                                    Aucune adresse enregistrée.
                                    <a href="/index_.php?page=compte/adresses">
                                        Ajouter une adresse
                                    </a>
                                </p>
                            <?php else: ?>
                                <?php foreach ($_adresses as $_adr): ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio"
                                               name="id_adresse_livraison"
                                               id="liv_<?= (int) $_adr->id_adresse ?>"
                                               value="<?= (int) $_adr->id_adresse ?>"
                                               <?= ((int) ($_POST['id_adresse_livraison'] ?? 0)) === (int) $_adr->id_adresse ? 'checked' : '' ?>>
                                        <label class="form-check-label"
                                               for="liv_<?= (int) $_adr->id_adresse ?>">
                                            <?= htmlspecialchars($_adr->rue) ?>,
                                            <?= htmlspecialchars($_adr->code_postal) ?>
                                            <?= htmlspecialchars($_adr->ville) ?>,
                                            <?= htmlspecialchars($_adr->pays) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div class="card border-0 shadow-sm p-4 mb-4">
                            <h5 class="fw-bold mb-3">Adresse de facturation</h5>
                            <?php if (!empty($_adresses)): ?>
                                <?php foreach ($_adresses as $_adr): ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio"
                                               name="id_adresse_facturation"
                                               id="fact_<?= (int) $_adr->id_adresse ?>"
                                               value="<?= (int) $_adr->id_adresse ?>"
                                               <?= ((int) ($_POST['id_adresse_facturation'] ?? 0)) === (int) $_adr->id_adresse ? 'checked' : '' ?>>
                                        <label class="form-check-label"
                                               for="fact_<?= (int) $_adr->id_adresse ?>">
                                            <?= htmlspecialchars($_adr->rue) ?>,
                                            <?= htmlspecialchars($_adr->code_postal) ?>
                                            <?= htmlspecialchars($_adr->ville) ?>,
                                            <?= htmlspecialchars($_adr->pays) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div class="card border-0 shadow-sm p-4">
                            <h5 class="fw-bold mb-3">Transporteur</h5>
                            <?php foreach ($_transporteurs as $_t): ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio"
                                           name="id_transporteur"
                                           id="trans_<?= (int) $_t->id_transporteur ?>"
                                           value="<?= (int) $_t->id_transporteur ?>"
                                           <?= ((int) ($_POST['id_transporteur'] ?? 0)) === (int) $_t->id_transporteur ? 'checked' : '' ?>>
                                    <label class="form-check-label"
                                           for="trans_<?= (int) $_t->id_transporteur ?>">
                                        <strong>
                                            <?= htmlspecialchars($_t->nom_transporteur) ?>
                                        </strong>
                                        — <?= number_format($_t->frais_livraison, 2, ',', ' ') ?> €
                                        (<?= htmlspecialchars($_t->delai_estime ?? '') ?>)
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    </div>

                    <!-- Récapitulatif -->
                    <div class="col-12 col-lg-5">
                        <div class="card border-0 shadow-sm p-4 sticky-top ss-sticky-80">
                            <h5 class="fw-bold mb-3">Récapitulatif</h5>
                            <?php $_total = 0.0; ?>
                            <?php foreach ($_lignes as $_l): ?>
                                <div class="d-flex justify-content-between small mb-2">
                                    <span>
                                        <?= htmlspecialchars($_l['nom_produit']) ?>
                                        × <?= (int) $_l['quantite'] ?>
                                    </span>
                                    <span>
                                        <?php
                                        $_st = (float) $_l['prix'] * (int) $_l['quantite'];
                                        $_total += $_st;
                                        echo number_format($_st, 2, ',', ' ') . ' €';
                                        ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold fs-5 mb-4">
                                <span>Total articles</span>
                                <span class="text-primary">
                                    <?= number_format($_total, 2, ',', ' ') ?> €
                                </span>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 btn-lg">
                                <i class="bi bi-check-circle me-2"></i>Confirmer la commande
                            </button>
                        </div>
                    </div>

                </div>
            </form>

        <?php endif; ?>
    </div>
</section>
