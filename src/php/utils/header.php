<?php

declare(strict_types=1);

// Catégories racines pour le menu déroulant
$_catDAO  = new CategorieDAO($cnx);
$_cats    = $_catDAO->getCategoriesRacine() ?? [];

// Compteur panier pour le badge dans le header
$_panierDAO    = new PanierDAO($cnx);
$_nbPanier     = $_panierDAO->getNbArticles($_SESSION['id_session']);

// Compteur comparateur (stockage session-only, pas de DAO).
$_nbCompare = isset($_SESSION['compare']) && is_array($_SESSION['compare'])
    ? count($_SESSION['compare'])
    : 0;
?>
<header class="site-header">
    <nav class="navbar navbar-expand-lg navbar-dark bg-header">
        <div class="container-xl">

            <!-- Logo -->
            <a class="navbar-brand fw-bold fs-4" href="/index_.php">
                <i class="bi bi-gem me-1"></i>Stone Shop
            </a>

            <!-- Toggler mobile -->
            <button class="navbar-toggler" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navPublic"
                    aria-controls="navPublic" aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navPublic">

                <!-- Menu principal -->
                <?php require_once __DIR__ . '/menu_public.php'; ?>

                <!-- Barre de recherche -->
                <form class="d-flex mx-auto my-2 my-lg-0 search-form"
                      action="/index_.php" method="get">
                    <input type="hidden" name="page" value="recherche">
                    <div class="input-group">
                        <input class="form-control form-control-sm"
                               type="search" name="q"
                               placeholder="Rechercher un produit…"
                               value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                               aria-label="Recherche">
                        <button class="btn btn-outline-light btn-sm" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>

                <!-- Icônes droite -->
                <ul class="navbar-nav ms-auto align-items-center gap-2">

                    <!-- Comparateur -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="/index_.php?page=compare"
                           title="Comparateur de produits">
                            <i class="bi bi-bar-chart fs-5"></i>
                            <span class="position-absolute top-0 start-100 translate-middle
                                         badge rounded-pill bg-danger <?= ($_nbCompare ?? 0) > 0 ? '' : 'd-none' ?>"
                                  id="badge-compare">
                                <?= (int) ($_nbCompare ?? 0) ?>
                            </span>
                        </a>
                    </li>

                    <!-- Panier -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="/index_.php?page=panier">
                            <i class="bi bi-cart3 fs-5"></i>
                            <?php if (!empty($_nbPanier) && $_nbPanier > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle
                                             badge rounded-pill bg-danger" id="badge-panier">
                                    <?= $_nbPanier ?>
                                </span>
                            <?php else: ?>
                                <span class="position-absolute top-0 start-100 translate-middle
                                             badge rounded-pill bg-danger d-none" id="badge-panier">
                                    0
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>

                    <!-- Compte -->
                    <?php if (isset($_SESSION['client'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle fs-5"></i>
                                <?= htmlspecialchars($_SESSION['client']['prenom']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item"
                                       href="/index_.php?page=compte/profil">
                                        <i class="bi bi-person me-2"></i>Mon profil
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                       href="/index_.php?page=compte/historique_commandes">
                                        <i class="bi bi-bag me-2"></i>Mes commandes
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                       href="/index_.php?page=compte/liste_envie">
                                        <i class="bi bi-heart me-2"></i>Liste d'envie
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger"
                                       href="/src/php/ajax/logout.php">
                                        <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/index_.php?page=compte/login">
                                <i class="bi bi-person-circle fs-5"></i>
                            </a>
                        </li>
                    <?php endif; ?>

                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-xl -->
    </nav>
</header>
