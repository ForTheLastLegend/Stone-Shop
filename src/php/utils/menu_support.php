<?php declare(strict_types=1); ?>
<nav class="navbar navbar-dark bg-header admin-navbar">
    <div class="container-fluid">

        <a class="navbar-brand fw-bold" href="/admin/index_.php">
            <i class="bi bi-gem me-1"></i>Stone Support
        </a>

        <ul class="navbar-nav flex-row gap-3 ms-auto align-items-center">
            <li class="nav-item">
                <a class="nav-link <?= (($_GET['page'] ?? '') === 'support_chat') ? 'active' : '' ?>"
                   href="/admin/index_.php?page=support_chat">
                    <i class="bi bi-chat-dots me-1"></i>Conversations
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-warning"
                   href="/src/php/ajax/logout_admin.php">
                    <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
                </a>
            </li>
        </ul>

    </div>
</nav>
