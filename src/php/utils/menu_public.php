<?php

declare(strict_types=1);

// $_cats et $_catDAO sont définis dans header.php (parent)
// $_cats = liste des catégories racines (id_categorie, nom_categorie, id_parent)
?>
<ul class="navbar-nav me-auto mb-2 mb-lg-0">

    <li class="nav-item">
        <a class="nav-link <?= (($_GET['page'] ?? 'accueil') === 'accueil') ? 'active' : '' ?>"
           href="/index_.php?page=accueil">Accueil</a>
    </li>

    <!-- Catalogue avec sous-menu catégories -->
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle
                  <?= (($_GET['page'] ?? '') === 'catalogue') ? 'active' : '' ?>"
           href="#" data-bs-toggle="dropdown" aria-expanded="false">
            Catalogue
        </a>
        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item fw-semibold"
                   href="/index_.php?page=catalogue">
                    <i class="bi bi-grid me-2"></i>Tout voir
                </a>
            </li>
            <?php if (!empty($_cats)): ?>
                <li><hr class="dropdown-divider"></li>
                <?php foreach ($_cats as $cat): ?>
                    <li>
                        <a class="dropdown-item"
                           href="/index_.php?page=catalogue&id_categorie=<?= (int) $cat->id_categorie ?>">
                            <?= htmlspecialchars($cat->nom_categorie) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </li>

    <li class="nav-item">
        <a class="nav-link <?= (($_GET['page'] ?? '') === 'contact') ? 'active' : '' ?>"
           href="/index_.php?page=contact">Contact</a>
    </li>

</ul>
