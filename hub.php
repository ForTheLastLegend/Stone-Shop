<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stone Shop — Hub</title>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/custom.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold mb-2">
            <i class="bi bi-grid-3x3-gap-fill me-2"></i>Stone Shop
        </h1>
        <p class="text-muted">Accès rapide aux différentes interfaces</p>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-12 col-sm-6 col-lg-3">
            <a href="/index_.php" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 text-center p-4">
                    <i class="bi bi-shop display-4 text-primary mb-3"></i>
                    <h5 class="fw-bold mb-1">Site public</h5>
                    <p class="text-muted small mb-0">Boutique en ligne</p>
                </div>
            </a>
        </div>

        <div class="col-12 col-lg-6">
            <a href="/admin/index_.php?page=login" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 text-center p-4">
                    <div class="d-flex justify-content-center align-items-center gap-4 mb-3">
                        <i class="bi bi-headset display-4 text-success"></i>
                        <i class="bi bi-person-badge display-4 text-warning"></i>
                    </div>
                    <h5 class="fw-bold mb-1">Support / Admin</h5>
                    <p class="text-muted small mb-0">Espace agent &amp; panneau administrateur</p>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <a href="/root/index_.php?page=login" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 text-center p-4">
                    <i class="bi bi-shield-lock display-4 text-danger mb-3"></i>
                    <h5 class="fw-bold mb-1">Root</h5>
                    <p class="text-muted small mb-0">Console super-admin</p>
                </div>
            </a>
        </div>
    </div>
</div>

</body>
</html>
