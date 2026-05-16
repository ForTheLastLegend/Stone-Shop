# Stone Shop — Boutique high-tech

Projet web de fin de module **Technologies Internet 2** (TI2, 2025-2026, HEPH-Condorcet).

Site e-commerce multi-rôles (visiteur, client, support, administrateur) couvrant le catalogue, la fiche produit, le panier persistant, la commande, les avis modérés, la liste d'envies et un chat support↔client en temps réel.

Un mini-projet Python Flask compagnon ([`flask/`](flask/)) se branche sur la **même base de données** et propose une vue minimaliste du catalogue, conformément à la consigne de mutualisation prof.

## Stack technique

- **PHP 8.2** + Apache 2.4
- **PostgreSQL 18** — toutes les mutations passent par des fonctions plpgsql (pas de SQL direct côté PHP)
- **Bootstrap 5** (CDN) + CSS personnalisé
- **jQuery 3.7** pour les interactions AJAX (panier, édition stock, liste d'envies, chat)
- **Docker Compose** pour le packaging complet (PHP + Postgres)

## Lancement du projet

Deux chemins sont possibles selon votre environnement. **Le chemin Docker est recommandé** car il ne nécessite aucune installation au-delà de Docker Desktop.

### Option A — Docker (recommandée)

**Pré-requis** : [Docker Desktop](https://www.docker.com/products/docker-desktop) installé (avec WSL2 activé sous Windows).

```powershell
cd D:\Dev\Stone-Shop
docker compose up --build
```

L'image PHP+Apache se construit, le conteneur Postgres bootstrap la base de données à partir de `initdb/init.sql`, puis le site est accessible :

- Boutique publique : <http://localhost:8080>
- Console administrateur : <http://localhost:8080/admin/>
- Console root : <http://localhost:8080/root/>
- Hub de navigation : <http://localhost:8080/hub.php>

Pour lancer en arrière-plan (terminal libéré) : ajoutez `-d` à la commande (`docker compose up --build -d`).

Pour arrêter : `docker compose down`. Pour repartir d'une base de données vierge : `docker compose down -v` (purge le volume Postgres avant le prochain `up`).

### Option B — Installation native (sans Docker)

**Pré-requis** :

- **PostgreSQL ≥ 17.5** installé localement (le dump utilise la directive `\restrict`, indisponible avant cette version — voir la section *Notes* en bas de document si vous êtes sur une version antérieure).
- Apache + PHP 8.2 avec les extensions `pdo_pgsql` et `gd` (un environnement type XAMPP convient).

**Étapes** :

1. **Restaurer la base de données** :

   ```powershell
   psql -U postgres -f exports\stone_shop.sql
   ```

   Le fichier contient `DROP DATABASE IF EXISTS stone_shop` + `CREATE DATABASE stone_shop` + l'ensemble du schéma, des fonctions plpgsql, des vues et des données seedées. Aucune préparation manuelle requise.

2. **Configurer la connexion PHP** dans [`src/php/db/db_pg_connect.php`](src/php/db/db_pg_connect.php) si vos identifiants Postgres diffèrent (par défaut : `postgres` / mot de passe à adapter, base `stone_shop`).

3. **Servir le projet** via votre serveur Apache local en pointant le `DocumentRoot` sur la racine du projet. L'entrée principale est `index_.php` (avec underscore — pensez à ajouter `DirectoryIndex index_.php` dans votre configuration Apache si nécessaire).

## Comptes de test pré-seedés

| Rôle | URL | Identifiant | Mot de passe |
|---|---|---|---|
| **Administrateur** | `/admin/` | `admin@stoneshop.be` | `Admin1234!` |
| **Support** | `/admin/` | `support@stoneshop.be` | `Support1234!` |
| **Client** | `/index_.php?page=compte/login` | `art@gmail.com` | `Arthur22!` |
| **Console root** | `/root/` | `root` | `root1234!` |

Tous les mots de passe sont stockés hachés en **Argon2id** dans la base.

## Projet Flask compagnon

Le sous-dossier [`flask/`](flask/) contient une version minimaliste du catalogue en Python Flask. Il lit la même base de données via le réseau Docker partagé `stoneshop_net`.

**Ordre de lancement obligatoire** : le projet PHP de la racine **d'abord** (il crée le réseau Docker et la base de données), puis Flask.

```powershell
# 1. Démarrer le projet PHP (depuis la racine du repo)
docker compose up -d

# 2. Démarrer Flask
cd flask
docker compose up --build
```

Le site Flask est ensuite accessible sur <http://localhost:5001>. Voir le [README du sous-projet Flask](flask/README.md) pour le détail.

## Structure du projet

```
Stone-Shop/
├── index_.php              # Routeur public (whitelist de pages)
├── hub.php                 # Page d'accès aux 4 espaces (exception hors-routeur)
├── admin/
│   ├── index_.php          # Routeur admin/support
│   └── content/            # Pages d'administration
├── root/
│   ├── index_.php          # Routeur console root
│   └── content/            # Gestion des admins, supports, clients
├── content/                # Pages publiques (accueil, fiche produit, panier, …)
├── src/
│   ├── php/
│   │   ├── classes/        # DTO + DAO (un fichier par table)
│   │   ├── ajax/           # Endpoints AJAX (panier, stock, avis, chat, …)
│   │   ├── utils/          # Helpers (CSRF, Password, ImageHelper, …)
│   │   └── db/             # Connexion PostgreSQL
│   ├── css/                # Sources CSS (style.css, custom.css)
│   └── js/                 # Sources JS (panier, stocks, wishlist, chat)
├── assets/
│   ├── css/                # Miroir des sources CSS chargé par les pages
│   ├── images/             # Images produits, catégories, contenu statique
│   └── js/                 # Miroir des sources JS (si applicable)
├── initdb/
│   └── init.sql            # Bootstrap automatique du conteneur Docker Postgres
├── exports/
│   └── stone_shop.sql      # Dump complet (avec CREATE DATABASE) — pour Option B
├── flask/                  # Mini-projet Python Flask compagnon (BD partagée)
│   └── README.md           # Voir ce fichier pour le détail du sous-projet
├── tools/                  # Utilitaires admin (cleanup d'images, génération de thumbs)
├── docker-compose.yaml
├── Dockerfile
└── composer.json
```

## Base de données — détails

### Deux fichiers SQL distincts

| Fichier | Utilisé pour | Contenu |
|---|---|---|
| [`initdb/init.sql`](initdb/init.sql) | Bootstrap Docker (joué automatiquement) | Schéma + fonctions plpgsql + vues + seeds, **sans** `CREATE DATABASE` (Postgres conteneur crée déjà la base via `POSTGRES_DB`) |
| [`exports/stone_shop.sql`](exports/stone_shop.sql) | Restauration sur Postgres natif (Option B) | Même contenu, **avec** `DROP DATABASE IF EXISTS` + `CREATE DATABASE` pour une restauration en une commande |

Les deux fichiers sont des dumps `pg_dump` du même état de la base.

### Régénérer les deux fichiers après modification de la base

Si vous modifiez la base de données et souhaitez propager l'état dans les deux dumps :

```powershell
$env:PGPASSWORD = '<votre mot de passe postgres>'

# Pour Docker (sans --create)
& "C:\Program Files\PostgreSQL\18\bin\pg_dump.exe" `
    -h localhost -p 5432 -U postgres -d stone_shop `
    -f "initdb\init.sql"

# Pour Option B (avec --create --clean)
& "C:\Program Files\PostgreSQL\18\bin\pg_dump.exe" `
    -h localhost -p 5432 -U postgres -d stone_shop `
    --create --clean --if-exists `
    -f "exports\stone_shop.sql"
```

### Architecture des données

- **21 tables** PostgreSQL (admin, support, client, adresse, categorie, produit, variante_produit, image_produit, promotion, code_promo, panier, commande, transporteur, avis, conversation, message_chat, message_contact, liste_envie, et 3 tables N-N).
- **57 fonctions plpgsql** : toutes les opérations d'écriture (INSERT/UPDATE/DELETE) passent par ces fonctions, conformément à la consigne du cours.
- **Vues** : notamment `vue_catalogue` (aplatissement produit + variante + catégorie + image + promo + avis), utilisée par le PHP **et** par le projet Flask.
- **Règle métier clé** : la logique stock / prix / image vit sur `variante_produit`, jamais sur `produit`. Le panier anonyme est porté par un `id_session` TEXT, fusionné au panier client à la connexion. Le prix de chaque ligne de commande est figé dans `commande_variante.prix_unitaire` au moment de l'achat.

## Notes et dépannage

### Postgres < 17.5

Les deux dumps SQL contiennent en tête `\restrict <token>` et en pied `\unrestrict <token>` — il s'agit d'une directive de sécurité introduite dans Postgres 17.5. Si vous utilisez une version antérieure de Postgres pour l'Option B, ouvrez le fichier `.sql` dans un éditeur de texte et supprimez ces deux lignes avant l'import.

### Apache + `index_.php`

Le projet utilise `index_.php` (avec underscore final) comme entrée du routeur — c'est une convention du cours. Le Dockerfile configure Apache pour reconnaître ce nom via une directive `DirectoryIndex` ajoutée. Si vous servez le projet depuis un Apache externe (Option B, XAMPP), pensez à ajouter cette directive dans votre `httpd.conf` ou un `.htaccess` racine :

```apacheconf
DirectoryIndex index_.php index.php index.html
```

### Collision de ports

Le `docker-compose.yaml` expose Postgres sur le port **5433** côté hôte (et non 5432) pour ne pas entrer en conflit avec une éventuelle installation Postgres native sur la même machine. L'application PHP utilise `db:5432` en interne (réseau Docker), ce qui rend l'exposition `5433` purement optionnelle — utile uniquement si vous souhaitez vous connecter à la base du conteneur depuis pgAdmin sur la machine hôte.

### Inspecter la base de données du conteneur

Pour inspecter la base Postgres du conteneur Docker depuis pgAdmin :

- **Host** : `localhost`
- **Port** : `5433`
- **Database** : `stone_shop`
- **User** : `postgres`
- **Password** : valeur de `POSTGRES_PASSWORD` dans votre `.env` (par défaut `changeme`)

Cette base est **distincte** d'une éventuelle base `stone_shop` que vous auriez installée nativement — les deux ne se synchronisent pas automatiquement.

## Contexte académique

Projet réalisé dans le cadre du cours **Technologies Internet 2** (HEPH-Condorcet, 2025-2026). Conformité au cahier des charges :

- Architecture multipage non-MVC respectant le pattern routeur + whitelist
- Mutations BD exclusivement via fonctions plpgsql
- Hashing Argon2id sur les trois tables d'utilisateurs
- Protection CSRF sur tous les formulaires sensibles
- Pas de JavaScript ni de styles inline (extraits dans des fichiers dédiés)
- Authentification multi-rôles (admin, support, client, root) avec sessions cloisonnées
