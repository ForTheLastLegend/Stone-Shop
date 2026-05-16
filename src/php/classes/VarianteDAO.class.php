<?php

declare(strict_types=1);

class VarianteDAO
{
    public function __construct(private PDO $_cnx) {}

    public function getCatalogueComplet(): ?array
    {
        $sql = 'SELECT * FROM vue_catalogue ORDER BY nom_produit, id_variante';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function getCatalogueByCategorie(int $idCat): ?array
    {
        $sql = 'SELECT * FROM vue_catalogue WHERE id_categorie = :id ORDER BY nom_produit';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $idCat, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function rechercherCatalogue(string $terme): ?array
    {
        $like = '%' . $terme . '%';
        $sql = 'SELECT * FROM vue_catalogue
                  WHERE nom_produit ILIKE :terme OR nom_variante ILIKE :terme
                  ORDER BY nom_produit';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':terme', $like);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function getVariantesByProduit(int $idProduit): ?array
    {
        $sql = 'SELECT * FROM variante_produit WHERE id_produit = :id ORDER BY prix';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $idProduit, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ? array_map(fn($d) => $this->_hydrate($d), $data) : null;
    }

    public function getVarianteParId(int $id): ?Variante
    {
        $sql = 'SELECT * FROM variante_produit WHERE id_variante = :id';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $this->_hydrate($data) : null;
    }

    // Renvoie un tableau assoc (vue mixte) — pas un DTO Variante.
    public function getVarianteCatalogueParId(int $id): ?array
    {
        $sql = 'SELECT * FROM vue_catalogue WHERE id_variante = :id';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function ajouterVariante(
        int $idProduit,
        string $nom,
        string $sku,
        float $prix,
        int $stock,
        ?string $couleur,
        ?string $capacite
    ): int {
        $sql = 'SELECT ajout_variante(:id_produit, :nom, :sku, :prix, :stock, :couleur, :capacite) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id_produit', $idProduit, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':sku', $sku);
        $stmt->bindParam(':prix', $prix);
        $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);
        $stmt->bindParam(':couleur', $couleur);
        $stmt->bindParam(':capacite', $capacite);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    // Champs acceptés par update_champ_variante : prix, stock, nom_variante, sku, couleur, capacite.
    public function updateChamp(int $id, string $champ, string $valeur): int
    {
        $sql = 'SELECT update_champ_variante(:id, :champ, :valeur) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':champ', $champ);
        $stmt->bindParam(':valeur', $valeur);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    public function getStockCritique(int $seuil = 5): ?array
    {
        $sql = 'SELECT vp.*, p.nom_produit
                   FROM variante_produit vp
                   JOIN produit p ON p.id_produit = vp.id_produit
                  WHERE vp.stock <= :seuil
                  ORDER BY vp.stock ASC';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':seuil', $seuil, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function effacerVariante(int $id): int
    {
        $sql = 'SELECT effacer_variante(:id) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    private function _hydrate(array $data): Variante
    {
        return new Variante(
            id_variante: (int) $data['id_variante'],
            id_produit: (int) $data['id_produit'],
            nom_variante: $data['nom_variante'],
            sku: $data['sku'],
            prix: (float) $data['prix'],
            stock: (int) $data['stock'],
            couleur: $data['couleur'] ?? null,
            capacite: $data['capacite'] ?? null
        );
    }
}
