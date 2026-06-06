<?php

declare(strict_types=1);

/**
 * Whitelists des pages autorisées par interface.
 *
 * Extraites des routeurs (index_.php public, admin/index_.php, root/index_.php)
 * pour centraliser la configuration du routing. Chaque routeur inclut ce fichier
 * et utilise les variables dont il a besoin ; la logique de dispatch reste dans
 * le routeur.
 */

// Pages publiques autorisées (whitelist)
$_pages = [
    'accueil', 'catalogue', 'fiche_produit', 'panier',
    'checkout', 'confirmation_commande', 'contact', 'recherche',
    'compare',
    'compte/login', 'compte/inscription', 'compte/profil',
    'compte/adresses', 'compte/historique_commandes',
    'compte/detail_commande', 'compte/mes_avis',
    'compte/liste_envie', 'compte/chat',
];

// Pages réservées à l'administrateur
$_adminPages = [
    'accueil', 'login', 'page_404',
    'gestion_catalogue', 'gestion_variantes', 'gestion_categories',
    'gestion_images', 'gestion_commandes', 'gestion_promotions',
    'gestion_codes_promo', 'gestion_transporteurs',
    'moderation_avis', 'messages_contact',
];

// Pages réservées au support (sous-ensemble)
$_supportPages = ['accueil', 'login', 'page_404', 'support_chat'];

// Pages réservées au compte root
$_rootPages = [
    'accueil', 'login', 'page_404',
    'gestion_admins', 'gestion_supports', 'gestion_clients',
];

// Pages sans vérification d'authentification (admin & root)
$_openPages = ['login', 'page_404'];
