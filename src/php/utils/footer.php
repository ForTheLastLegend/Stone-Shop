<?php declare(strict_types=1); ?>
<footer class="site-footer bg-footer text-light mt-auto py-5">
    <div class="container-xl">
        <div class="row g-4">

            <!-- Logo + réseaux -->
            <div class="col-12 col-md-4">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-gem me-2"></i>Stone Shop
                </h5>
                <p class="text-secondary small">
                    Votre boutique high-tech de confiance.<br>
                    Qualité, réactivité, satisfaction garantie.
                </p>
                <div class="d-flex gap-3 mt-3 fs-5">
                    <a href="#" class="text-secondary footer-social" aria-label="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="#" class="text-secondary footer-social" aria-label="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" class="text-secondary footer-social" aria-label="X (Twitter)">
                        <i class="bi bi-twitter-x"></i>
                    </a>
                </div>
            </div>

            <!-- Service client -->
            <div class="col-6 col-md-4">
                <h6 class="fw-semibold text-uppercase text-secondary mb-3">
                    Service Client
                </h6>
                <ul class="list-unstyled small">
                    <li class="mb-2">
                        <a class="text-secondary footer-link"
                           href="/index_.php?page=contact">
                            <i class="bi bi-envelope me-2"></i>Nous contacter
                        </a>
                    </li>
                    <li class="mb-2">
                        <a class="text-secondary footer-link"
                           href="/index_.php?page=compte/chat">
                            <i class="bi bi-chat-dots me-2"></i>Chat en ligne
                        </a>
                    </li>
                    <li class="mb-2">
                        <a class="text-secondary footer-link" href="#">
                            <i class="bi bi-question-circle me-2"></i>FAQ
                        </a>
                    </li>
                    <li class="mb-2">
                        <a class="text-secondary footer-link" href="#">
                            <i class="bi bi-truck me-2"></i>Livraison &amp; retours
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Informations légales -->
            <div class="col-6 col-md-4">
                <h6 class="fw-semibold text-uppercase text-secondary mb-3">
                    Informations
                </h6>
                <ul class="list-unstyled small">
                    <li class="mb-2">
                        <a class="text-secondary footer-link" href="#">
                            <i class="bi bi-shield-check me-2"></i>Mentions légales
                        </a>
                    </li>
                    <li class="mb-2">
                        <a class="text-secondary footer-link" href="#">
                            <i class="bi bi-lock me-2"></i>Politique de confidentialité
                        </a>
                    </li>
                    <li class="mb-2">
                        <a class="text-secondary footer-link" href="#">
                            <i class="bi bi-file-text me-2"></i>CGV
                        </a>
                    </li>
                    <li class="mb-2">
                        <a class="text-secondary footer-link" href="#">
                            <i class="bi bi-cookie me-2"></i>Cookies (RGPD)
                        </a>
                    </li>
                </ul>
            </div>

        </div><!-- /.row -->

        <hr class="border-secondary mt-4">
        <p class="text-center text-secondary small mb-0">
            &copy; <?= date('Y') ?> Stone Shop — Tous droits réservés.
        </p>
    </div><!-- /.container-xl -->
</footer>
