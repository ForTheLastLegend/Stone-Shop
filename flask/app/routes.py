"""Routes Flask pour le mini-projet Stone Shop.

Suit le tutoriel S. De Luca (p. 38-44) avec adaptation au schema Stone Shop.

3 routes au total (consigne prof : pas plus que le tuto) :
- /            (= /accueil)         : liste des categories
- /tous_produits                    : catalogue complet
- /produits_categorie?id_cat=N      : produits filtres sur la categorie N
"""

from flask import render_template, request

from app import app, db
from app.models import VueCatalogue


@app.route("/")
@app.route("/accueil")
def accueil():
    """Page d'accueil : une carte par categorie distincte.

    DISTINCT ON cote SQL (Postgres) : on garde la premiere ligne
    rencontree pour chaque id_categorie, qui sert de "representant"
    pour l'image et le nom de categorie.
    """
    liste_cat = (
        db.session.query(VueCatalogue)
        .distinct(VueCatalogue.id_categorie)
        .order_by(VueCatalogue.id_categorie)
        .all()
    )

    return render_template(
        "accueil.html",
        title="Bienvenue sur Stone Shop",
        liste_cat=liste_cat,
    )


@app.route("/tous_produits")
def tous_produits():
    """Catalogue complet : toutes les variantes actives."""
    liste_prod = VueCatalogue.query.all()
    return render_template(
        "tous_produits.html",
        title="Nos produits",
        liste_prod=liste_prod,
    )


@app.route("/produits_categorie")
def produits_categorie():
    """Produits filtres par categorie via ?id_cat=N (cf. tuto p. 43).

    Si id_cat absent ou invalide, la liste reste vide et le template
    affiche un message neutre.
    """
    id_categ = request.args.get("id_cat", type=int)
    if id_categ is None:
        liste_prod = []
    else:
        liste_prod = (
            VueCatalogue.query.filter_by(id_categorie=id_categ).all()
        )

    return render_template(
        "produits_categorie.html",
        title="Produits filtres",
        produits=liste_prod,
    )
