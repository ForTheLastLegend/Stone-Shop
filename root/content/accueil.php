<?php

declare(strict_types=1);

require_once __DIR__ . '/../../src/php/utils/check_root.php';

$_adminDAO   = new AdminDAO($cnx);
$_supportDAO = new SupportDAO($cnx);
$_clientDAO  = new ClientDAO($cnx);

$_nbAdmins   = $_adminDAO->compterAdmins();
$_nbSupports = $_supportDAO->compterSupports();
$_nbClients  = $_clientDAO->compterClients();
?>

<div class="container-fluid p-4">
    <h3 class="fw-bold mb-4">
        <i class="bi bi-shield-lock me-2"></i>Console root
    </h3>
    <p class="text-muted mb-4">
        Bonjour <?= htmlspecialchars($_SESSION['root']['login_root']) ?>,
        gérez les comptes du site depuis cette interface.
    </p>

    <div class="row g-3">

        <div class="col-12 col-md-4">
            <a href="/root/index_.php?page=gestion_admins"
               class="card border-0 shadow-sm p-4 h-100 text-decoration-none">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon bg-primary-subtle rounded-circle p-3">
                        <i class="bi bi-person-badge fs-3 text-primary"></i>
                    </div>
                    <div>
                        <div class="h4 fw-bold mb-0"><?= $_nbAdmins ?></div>
                        <div class="text-muted small">Administrateurs</div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-md-4">
            <a href="/root/index_.php?page=gestion_supports"
               class="card border-0 shadow-sm p-4 h-100 text-decoration-none">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon bg-info-subtle rounded-circle p-3">
                        <i class="bi bi-headset fs-3 text-info"></i>
                    </div>
                    <div>
                        <div class="h4 fw-bold mb-0"><?= $_nbSupports ?></div>
                        <div class="text-muted small">Supports</div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-md-4">
            <a href="/root/index_.php?page=gestion_clients"
               class="card border-0 shadow-sm p-4 h-100 text-decoration-none">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon bg-success-subtle rounded-circle p-3">
                        <i class="bi bi-people fs-3 text-success"></i>
                    </div>
                    <div>
                        <div class="h4 fw-bold mb-0"><?= $_nbClients ?></div>
                        <div class="text-muted small">Clients</div>
                    </div>
                </div>
            </a>
        </div>

    </div>
</div>
