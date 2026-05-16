# Stone Shop Flask - alias entrypoint pour PyCharm Run Configuration.
# L'entrypoint canonique du tutoriel est main.py (p. 16 du PDF).
# Ce fichier reste pour preserver la Run Config Flask creee automatiquement
# par le template PyCharm. Les deux fichiers sont equivalents : ils
# importent l'app depuis le package app/ (cf. app/__init__.py).
from app import app

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000, debug=True)
