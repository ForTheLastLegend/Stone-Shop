# Stone Shop Flask - entrypoint canonique (cf. tutoriel PDF p. 16).
# Lance le serveur de developpement Flask.
# - En local : `python main.py` (ou bouton Run de PyCharm si Run Config pointe ici)
# - En conteneur : la commande Docker CMD utilise ce fichier
from app import app

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000, debug=True)
