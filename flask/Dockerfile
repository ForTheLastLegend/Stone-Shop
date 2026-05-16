# Stone Shop Flask - image Python pour le mini-projet
# Suit la recette du tutoriel S. De Luca (PDF p. 10-11).

FROM python:3.13-slim

WORKDIR /app

# psycopg2-binary ships en wheels precompilees, donc pas besoin de gcc/libpq-dev.
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY . .

EXPOSE 5000

CMD ["python", "main.py"]
