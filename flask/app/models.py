"""Modele SQLAlchemy mappe sur la vue Postgres `vue_catalogue`.

La vue (cf. backups/vues/stone_shop_vues.sql du grand projet) aplatit
deja le JOIN produit + variante_produit + categorie + image_produit
+ promotion. C'est l'equivalent direct de la vue
`vue_produits_categories` du tutoriel pour la patisserie.

Une ligne de la vue = une variante de produit = une "carte" dans les
templates Flask. La cle primaire SQLAlchemy est id_variante : chaque
variante est unique et identifie la ligne sans ambiguite (cf. tuto
p. 36 sur le choix de la cle primaire pour une vue).
"""

from app import db


class VueCatalogue(db.Model):
    """Mapping ORM sur la vue SQL `vue_catalogue`."""

    __tablename__ = "vue_catalogue"

    # Cle primaire : identifie une variante (ligne unique de la vue)
    id_variante = db.Column(db.Integer, primary_key=True)

    # Donnees du produit parent
    id_produit = db.Column(db.Integer)
    nom_produit = db.Column(db.String(255))
    description_courte = db.Column(db.Text)

    # Donnees de la categorie
    id_categorie = db.Column(db.Integer)
    nom_categorie = db.Column(db.String(100))

    # Donnees de la variante
    nom_variante = db.Column(db.String(255))
    sku = db.Column(db.String(100))
    prix = db.Column(db.Numeric(10, 2))
    stock = db.Column(db.Integer)
    couleur = db.Column(db.String(50))
    capacite = db.Column(db.String(50))
    disponible = db.Column(db.Boolean)

    # Promo (optionnel) + prix final apres reduction
    prix_final = db.Column(db.Numeric(10, 2))
    taux_reduction = db.Column(db.Numeric(5, 2))

    # Image principale + alt
    image_principale = db.Column(db.String(500))
    alt_text = db.Column(db.String(255))

    # Avis agreges
    note_moyenne = db.Column(db.Numeric(2, 1))
    nb_avis = db.Column(db.Integer)

    def __repr__(self):
        return (
            f"<VueCatalogue {self.id_variante} : {self.nom_variante} - "
            f"{self.prix_final} EUR - {self.nom_categorie}>"
        )
