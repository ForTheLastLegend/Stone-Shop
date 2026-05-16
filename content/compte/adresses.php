<?php

declare(strict_types=1);

require_once __DIR__ . '/../../src/php/utils/check_connexion.php';

$_idClient   = (int) $_SESSION['client']['id_client'];
$_adresseDAO = new AdresseDAO($cnx);
$_adresses   = $_adresseDAO->getAdressesParClient($_idClient) ?? [];
$_succes     = '';
$_erreurs    = [];

// Un seul appel couvre les deux branches POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_csrf();
}

// Ajout adresse
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_adresse'])) {
    $_nomDest = trim($_POST['nom_destinataire'] ?? '');
    $_rue     = trim($_POST['rue']              ?? '');
    $_numero  = trim($_POST['numero']           ?? '');
    $_boite   = trim($_POST['boite']            ?? '') ?: null;
    $_cp      = trim($_POST['code_postal']      ?? '');
    $_ville   = trim($_POST['ville']            ?? '');
    $_pays    = trim($_POST['pays']             ?? '');
    $_type    = $_POST['type_adresse']          ?? 'livraison';

    if ($_nomDest === '' || $_rue === '' || $_numero === '' || $_cp === '' || $_ville === '' || $_pays === '') {
        $_erreurs[] = 'Tous les champs obligatoires sont requis.';
    } else {
        $_ret = $_adresseDAO->ajouterAdresse(
            $_idClient, $_type, $_nomDest, $_rue, $_numero, $_boite, $_cp, $_ville, $_pays
        );
        if ($_ret > 0) {
            $_succes   = 'Adresse ajoutée.';
            $_adresses = $_adresseDAO->getAdressesParClient($_idClient) ?? [];
        }
    }
}

// Suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_adresse'])) {
    $_idAdr = (int) ($_POST['id_adresse'] ?? 0);
    if ($_idAdr > 0) {
        $_adresseDAO->supprimerAdresse($_idAdr);
        $_succes   = 'Adresse supprimée.';
        $_adresses = $_adresseDAO->getAdressesParClient($_idClient) ?? [];
    }
}
?>

<section class="py-5">
    <div class="container-xl">
        <div class="row">
            <nav class="col-12 col-md-3 mb-4">
                <?php require_once __DIR__ . '/../../src/php/utils/menu_compte.php'; ?>
            </nav>

            <div class="col-12 col-md-9">
                <h3 class="fw-bold mb-4">Mes adresses</h3>

                <?php if ($_succes): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($_succes) ?></div>
                <?php endif; ?>
                <?php foreach ($_erreurs as $_e): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($_e) ?></div>
                <?php endforeach; ?>

                <!-- Liste des adresses -->
                <?php if (empty($_adresses)): ?>
                    <p class="text-muted">Aucune adresse enregistrée.</p>
                <?php else: ?>
                    <div class="row g-3 mb-4">
                        <?php foreach ($_adresses as $_adr): ?>
                            <div class="col-12 col-md-6">
                                <div class="card border-0 shadow-sm p-3 h-100">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="badge bg-secondary">
                                            <?= htmlspecialchars($_adr->type_adresse) ?>
                                        </span>
                                        <form method="post" class="d-inline"
                                              data-confirm="Supprimer cette adresse ?">
                                            <input type="hidden" name="csrf_token"
                                                   value="<?= $_SESSION['csrf_token'] ?>">
                                            <input type="hidden" name="id_adresse"
                                                   value="<?= (int) $_adr->id_adresse ?>">
                                            <button type="submit" name="supprimer_adresse"
                                                    class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                     <p class="mb-0">
                                         <strong><?= htmlspecialchars($_adr->nom_destinataire) ?></strong><br>
                                         <?= htmlspecialchars($_adr->rue) ?>
                                         <?= htmlspecialchars($_adr->numero) ?>
                                         <?php if ($_adr->boite): ?>
                                             / bte <?= htmlspecialchars($_adr->boite) ?>
                                         <?php endif; ?><br>
                                         <?= htmlspecialchars($_adr->code_postal) ?>
                                         <?= htmlspecialchars($_adr->ville) ?><br>
                                         <?= htmlspecialchars($_adr->pays) ?>
                                     </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Formulaire ajout -->
                <div class="card border-0 shadow-sm p-4">
                    <h5 class="fw-bold mb-3">Ajouter une adresse</h5>
                    <form method="post">
                        <input type="hidden" name="csrf_token"
                               value="<?= $_SESSION['csrf_token'] ?>">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Type</label>
                            <select name="type_adresse" class="form-select">
                                <option value="livraison">Livraison</option>
                                <option value="facturation">Facturation</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nom du destinataire <span class="text-danger">*</span></label>
                            <input type="text" name="nom_destinataire" class="form-control" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-8">
                                <label class="form-label fw-semibold">Rue <span class="text-danger">*</span></label>
                                <input type="text" name="rue" class="form-control" required>
                            </div>
                            <div class="col-3">
                                <label class="form-label fw-semibold">N° <span class="text-danger">*</span></label>
                                <input type="text" name="numero" class="form-control" required>
                            </div>
                            <div class="col-1">
                                <label class="form-label fw-semibold">Bte</label>
                                <input type="text" name="boite" class="form-control">
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-4">
                                <label class="form-label fw-semibold">Code postal <span class="text-danger">*</span></label>
                                <input type="text" name="code_postal" class="form-control" required>
                            </div>
                            <div class="col-8">
                                <label class="form-label fw-semibold">Ville <span class="text-danger">*</span></label>
                                <input type="text" name="ville" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Pays <span class="text-danger">*</span></label>
                            <input type="text" name="pays" class="form-control"
                                   value="Belgique" required>
                        </div>
                        <button type="submit" name="ajouter_adresse" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Ajouter
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</section>
