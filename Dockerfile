# Stone Shop — image PHP+Apache pour conteneurisation locale
FROM php:8.2-apache

# Dépendances système pour pdo_pgsql + GD (uploads d'images)
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libpq-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_pgsql gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# mod_rewrite pour les URLs Stone Shop (ex. /index_.php)
RUN a2enmod rewrite

# Stone Shop utilise index_.php (avec underscore) comme entrypoint — pas le
# index.php standard. Sans ce DirectoryIndex, Apache renvoie 403 sur la racine.
RUN echo 'DirectoryIndex index_.php index.php index.html' \
    > /etc/apache2/conf-enabled/stoneshop-dirindex.conf

# Interdiction d'accès HTTP aux répertoires sensibles
RUN printf '<Directory /var/www/html/exports>\n    Require all denied\n</Directory>\n<Directory /var/www/html/backups>\n    Require all denied\n</Directory>\n<Directory /var/www/html/initdb>\n    Require all denied\n</Directory>\n<Directory /var/www/html/tools>\n    Require all denied\n</Directory>\n' \
    > /etc/apache2/conf-enabled/stoneshop-protected.conf

# Flags de sécurité sur les cookies de session
RUN printf 'session.cookie_httponly = 1\nsession.cookie_samesite = Strict\n' \
    > /usr/local/etc/php/conf.d/stone-session.ini

# Code source monté en volume via docker-compose (./:/var/www/html)
WORKDIR /var/www/html

# Apache écoute en 80 par défaut ; mappé sur 8080 dans compose pour
# éviter la collision avec XAMPP côté hôte.
EXPOSE 80
