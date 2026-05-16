<?php declare(strict_types=1); ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-header admin-navbar">
    <div class="container-fluid">

        <a class="navbar-brand fw-bold" href="/admin/index_.php">
            <i class="bi bi-gem me-1"></i>Stone Admin
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#navAdmin"
                aria-controls="navAdmin" aria-expanded="false">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navAdmin">
            <ul class="navbar-nav me-auto gap-1">

                <li class="nav-item">
                    <a class="nav-link <?= (($_GET['page'] ?? 'accueil') === 'accueil') ? 'active' : '' ?>"
                       href="/admin/index_.php?page=accueil">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                </li>

                <!-- Catalogue -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-box-seam me-1"></i>Catalogue
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item"
                               href="/admin/index_.php?page=gestion_catalogue">
                                Produits
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                               href="/admin/index_.php?page=gestion_variantes">
                                Variantes &amp; stocks
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                               href="/admin/index_.php?page=gestion_categories">
                                Catégories
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                               href="/admin/index_.php?page=gestion_images">
                                Images
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= (($_GET['page'] ?? '') === 'gestion_commandes') ? 'active' : '' ?>"
                       href="/admin/index_.php?page=gestion_commandes">
                        <i class="bi bi-bag-check me-1"></i>Commandes
                    </a>
                </li>

                <!-- Promotions -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-percent me-1"></i>Promotions
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item"
                               href="/admin/index_.php?page=gestion_promotions">
                                Promotions
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                               href="/admin/index_.php?page=gestion_codes_promo">
                                Codes promo
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= (($_GET['page'] ?? '') === 'gestion_transporteurs') ? 'active' : '' ?>"
                       href="/admin/index_.php?page=gestion_transporteurs">
                        <i class="bi bi-truck me-1"></i>Transporteurs
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= (($_GET['page'] ?? '') === 'moderation_avis') ? 'active' : '' ?>"
                       href="/admin/index_.php?page=moderation_avis">
                        <i class="bi bi-star me-1"></i>Avis
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= (($_GET['page'] ?? '') === 'messages_contact') ? 'active' : '' ?>"
                       href="/admin/index_.php?page=messages_contact">
                        <i class="bi bi-envelope me-1"></i>Messages
                    </a>
                </li>

            </ul>

            <!-- Déconnexion -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link text-warning"
                       href="/src/php/ajax/logout_admin.php">
                        <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
