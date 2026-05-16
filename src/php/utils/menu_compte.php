<?php declare(strict_types=1); ?>
<div class="list-group list-group-flush shadow-sm rounded">
    <a href="/index_.php?page=compte/profil"
       class="list-group-item list-group-item-action
              <?= (($_GET['page'] ?? '') === 'compte/profil') ? 'active' : '' ?>">
        <i class="bi bi-person me-2"></i>Mon profil
    </a>
    <a href="/index_.php?page=compte/adresses"
       class="list-group-item list-group-item-action
              <?= (($_GET['page'] ?? '') === 'compte/adresses') ? 'active' : '' ?>">
        <i class="bi bi-geo-alt me-2"></i>Mes adresses
    </a>
    <a href="/index_.php?page=compte/historique_commandes"
       class="list-group-item list-group-item-action
              <?= (($_GET['page'] ?? '') === 'compte/historique_commandes') ? 'active' : '' ?>">
        <i class="bi bi-bag me-2"></i>Mes commandes
    </a>
    <a href="/index_.php?page=compte/mes_avis"
       class="list-group-item list-group-item-action
              <?= (($_GET['page'] ?? '') === 'compte/mes_avis') ? 'active' : '' ?>">
        <i class="bi bi-star me-2"></i>Mes avis
    </a>
    <a href="/index_.php?page=compte/liste_envie"
       class="list-group-item list-group-item-action
              <?= (($_GET['page'] ?? '') === 'compte/liste_envie') ? 'active' : '' ?>">
        <i class="bi bi-heart me-2"></i>Liste d'envie
    </a>
    <a href="/index_.php?page=compte/chat"
       class="list-group-item list-group-item-action
              <?= (($_GET['page'] ?? '') === 'compte/chat') ? 'active' : '' ?>">
        <i class="bi bi-chat-dots me-2"></i>Support chat
    </a>
    <a href="/src/php/ajax/logout.php"
       class="list-group-item list-group-item-action text-danger">
        <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
    </a>
</div>
