# Stone Shop Flask — Mini-projet Python Flask

Adaptation au projet **Stone Shop** du tutoriel *"Initiation au micro-framework Flask Python et à Docker"* (S. De Luca, HEPH-Condorcet 2025-2026).

Version Python Flask minimaliste du projet PHP de la racine du repo, **branchée sur la même base de données PostgreSQL**, conformément à la consigne de mutualisation prof.

## Stack technique

- **Python 3.13** + **Flask 3.0** + **Flask-SQLAlchemy 3.1**
- **PostgreSQL 18** (instance partagée avec le projet PHP via le réseau Docker `stoneshop_net`)
- **Bootstrap 5** (CDN) + CSS personnalisé minimaliste
- **Docker Compose**

## Architecture — base de données partagée

```
                ┌─────────────────────────────┐
                │  réseau Docker stoneshop_net │
                │                              │
   localhost ───┼──> php-site (8080)           │
   :8080        │       │                      │
                │       │ JOIN                 │
                │       ▼                      │
                │      db ◄──── flask          │
                │  (postgres:18)               │
   localhost ───┼─────────────────┐            │
   :5001        │                 │            │
                │             flask (5000)     │
                └─────────────────────────────┘
                  racine du repo     flask/ (ce sous-dossier)
                  docker-compose     docker-compose
```

Le service Postgres (`db`) tourne dans le compose à la racine du repo. Ce conteneur Flask se rattache au même réseau Docker (`stoneshop_net`, déclaré dans le compose racine) pour accéder à `db:5432` directement, en évitant toute duplication de données.

## Lancement

**Ordre obligatoire** : le projet PHP d'abord (il crée le réseau Docker `stoneshop_net` et la base de données), puis Flask.

```powershell
# 1. Démarrer le projet PHP + Postgres (depuis la racine du repo)
docker compose up -d

# 2. Démarrer Flask (depuis le sous-dossier flask/)
cd flask
docker compose up --build
```

Puis ouvrez <http://localhost:5001>.

Le projet PHP reste accessible en parallèle sur <http://localhost:8080>. Les deux applications lisent **les mêmes données en temps réel** : un produit ajouté via l'admin PHP apparaît immédiatement (au rafraîchissement) dans Flask, et inversement.

## Arrêt et reset

```powershell
# Arrêter Flask seul (laisse le PHP et la base de données en cours)
cd flask
docker compose down

# Tout arrêter (depuis flask/ puis depuis la racine)
docker compose down
cd ..
docker compose down

# Reset complet (purge le volume Postgres pour rejouer initdb/)
cd flask && docker compose down
cd .. && docker compose down -v && docker compose up -d
cd flask && docker compose up --build
```

## Pages exposées

Conformément au tutoriel, l'application propose exactement **3 routes** :

| Route | Description |
|---|---|
| `/` ou `/accueil` | Liste des catégories distinctes (cartes cliquables) |
| `/tous_produits` | Catalogue complet (toutes les variantes actives) |
| `/produits_categorie?id_cat=N` | Produits filtrés sur la catégorie `N` |

## Architecture du code (MVC, cf. tutoriel p. 14-17)

```
flask/
├── app/
│   ├── __init__.py     # Initialisation Flask + SQLAlchemy + configuration
│   ├── routes.py       # Les 3 routes
│   └── models.py       # VueCatalogue (mappée sur la vue SQL vue_catalogue)
├── templates/          # 5 fichiers Jinja2 (layout, menu, accueil, tous_produits, produits_categorie)
├── static/
│   ├── css/style.css   # CSS personnalisé minimaliste
│   └── images/         # Catégories (default.jpg) + produits (montés depuis le projet PHP)
├── main.py             # Entrypoint canonique du tutoriel
├── app.py              # Alias entrypoint pour la Run Configuration PyCharm
├── Dockerfile
├── docker-compose.yml
├── requirements.txt
└── .env(.example)
```

### Points d'adaptation par rapport au tutoriel

Quelques choix divergent du tutoriel — chacun est justifié par la consigne de mutualisation ou par une bonne pratique :

1. **`template_folder` et `static_folder` explicites** dans [`app/__init__.py`](app/__init__.py) — les dossiers `templates/` et `static/` sont à la racine du sous-projet (comme dans le tutoriel) mais le package Flask vit dans `app/`. Flask cherche par défaut ces dossiers dans le package, donc il faut les pointer explicitement.
2. **URI de base de données lue depuis l'environnement** (`os.environ.get('SQLALCHEMY_DATABASE_URI', …)`) — permet d'utiliser la même configuration que ce soit dans Docker (`db:5432`) ou en local hors Docker via PyCharm (`localhost:5433`).
3. **Réseau Docker externe `stoneshop_net`** — nécessaire pour atteindre le conteneur Postgres du projet PHP au lieu d'avoir un Postgres dédié.
4. **Bind-mount des images produits du projet PHP** dans `static/images/produits/` (lecture seule) — comme la base est partagée, les images le sont aussi pour éviter toute duplication.
5. **Modèle mappé sur la vue `vue_catalogue`** — cette vue aplatit `produit + variante_produit + categorie + image_produit + promotion + avis`. Une ligne de la vue = une variante de produit = une carte affichée dans les templates.

## Ce qui n'est volontairement pas implémenté

Conformément au scope du tutoriel (et aux courriels de cadrage des 30/04 et 06/05/2026), l'application ne propose pas :

- ❌ Pas de panier
- ❌ Pas de connexion utilisateur
- ❌ Pas d'AJAX ni de JavaScript personnalisé
- ❌ Pas de page de détail produit
- ❌ Pas de formulaires d'ajout ou de modification

Le périmètre se limite donc strictement aux trois pages du tutoriel.

## Données

La base est celle du projet PHP, alimentée par [`../initdb/init.sql`](../initdb/init.sql) (schéma + fonctions plpgsql + vues + seeds). La vue `vue_catalogue` sert de source unique pour le modèle SQLAlchemy `VueCatalogue` : elle aplatit le JOIN entre `produit`, `variante_produit`, `categorie`, `image_produit`, `promotion` et les avis agrégés.

## Notes

- Le port **5001** est utilisé côté hôte pour éviter toute collision avec le projet PHP (qui occupe le port **8080**).
- Le dossier `.venv/` local et `.idea/` PyCharm sont exclus de l'image Docker via `.dockerignore`. Pour exécuter l'application hors Docker via PyCharm, créez un venv local et lancez `pip install -r requirements.txt`.
- L'image Docker utilise `psycopg2-binary` (wheel précompilée) : aucune dépendance système (gcc, libpq-dev) n'est nécessaire dans le Dockerfile.

---

<sub>Documentation co-rédigée avec [![Claude](https://img.shields.io/badge/Claude-D97757?style=flat&logo=anthropic&logoColor=white)](https://claude.ai)</sub>
