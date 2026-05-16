"""Initialisation de l'application Flask Stone Shop.

Suit l'architecture MVC du tutoriel S. De Luca (p. 14-17) :
- ce module instancie l'objet Flask et l'objet SQLAlchemy
- routes.py est importe en bas (apres instanciation) pour eviter
  les imports circulaires
- models.py contient la classe mappee sur la vue SQL `vue_catalogue`
"""

import os

from flask import Flask
from flask_sqlalchemy import SQLAlchemy

# 1. Instanciation de l'objet Flask (cf. tuto p. 16)
#    On force template_folder et static_folder vers la racine du projet
#    parce que `Flask(__name__)` cherche par defaut dans `app/templates/`
#    et `app/static/` (a l'interieur du package). Le tuto place ces
#    dossiers a la racine du projet, donc on les pointe explicitement.
_BASE_DIR = os.path.abspath(os.path.join(os.path.dirname(__file__), os.pardir))
app = Flask(
    __name__,
    template_folder=os.path.join(_BASE_DIR, "templates"),
    static_folder=os.path.join(_BASE_DIR, "static"),
)

# 2. Configuration : SECRET_KEY (anti-CSRF/XSS) + URI Postgres
#    Les valeurs viennent du fichier .env (charge par Docker via env_file
#    ou par python-dotenv en local).
app.config["SECRET_KEY"] = os.environ.get(
    "SECRET_KEY",
    "dev-fallback-key-do-not-use-in-prod",
)
app.config["SQLALCHEMY_DATABASE_URI"] = os.environ.get(
    "SQLALCHEMY_DATABASE_URI",
    "postgresql://postgres:changeme@db:5432/stone_shop",
)
# Desactive les notifications inutiles de SQLAlchemy (cf. tuto p. 32)
app.config["SQLALCHEMY_TRACK_MODIFICATIONS"] = False

# 3. Instanciation de l'objet SQLAlchemy lie a l'app
db = SQLAlchemy(app)

# 4. Import des routes APRES instanciation de Flask et de db.
#    Le tuto (p. 17) insiste sur l'ordre pour eviter les imports circulaires.
from app import routes  # noqa: E402, F401
