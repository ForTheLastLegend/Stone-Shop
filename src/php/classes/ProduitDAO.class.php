<?php

declare(strict_types=1);

class ProduitDAO
{
    public function __construct(private PDO $_cnx) {}

    public function getAllProduits(): ?array
    {
        $sql  = 'SELECT * FROM produit ORDER BY nom_produit';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ? array_map(fn($d) => $this->_hydrate($d), $data) : null;
    }

    public function getProduitParId(int $id): ?Produit
    {
        $sql  = 'SELECT * FROM produit WHERE id_produit = :id';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $this->_hydrate($data) : null;
    }

    public function getProduitsByCategorie(int $idCat): ?array
    {
        $sql  = 'SELECT * FROM produit WHERE id_categorie = :id AND actif = true ORDER BY nom_produit';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $idCat, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ? array_map(fn($d) => $this->_hydrate($d), $data) : null;
    }

    /** Recherche full-text dans nom + description. */
    public function rechercherProduits(string $terme): ?array
    {
        $like = '%' . $terme . '%';
        $sql  = 'SELECT * FROM produit
                  WHERE actif = true
                    AND (nom_produit ILIKE :terme OR description_courte ILIKE :terme)
                  ORDER BY nom_produit';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':terme', $like);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ? array_map(fn($d) => $this->_hydrate($d), $data) : null;
    }

    public function ajouterProduit(int $idCat, string $nom, string $desc, ?string $fiche): int
    {
        $sql  = 'SELECT ajout_produit(:id_cat, :nom, :desc, :fiche) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id_cat', $idCat, PDO::PARAM_INT);
        $stmt->bindParam(':nom',    $nom);
        $stmt->bindParam(':desc',   $desc);
        $stmt->bindParam(':fiche',  $fiche);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    public function modifierProduit(int $id, int $idCat, string $nom, string $desc, ?string $fiche, bool $actif): int
    {
        $actifStr = $actif ? 'true' : 'false';
        $sql  = 'SELECT modifier_produit(:id, :id_cat, :nom, :desc, :fiche, :actif) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id',     $id,      PDO::PARAM_INT);
        $stmt->bindParam(':id_cat', $idCat,   PDO::PARAM_INT);
        $stmt->bindParam(':nom',    $nom);
        $stmt->bindParam(':desc',   $desc);
        $stmt->bindParam(':fiche',  $fiche);
        $stmt->bindParam(':actif',  $actifStr);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    // Champs acceptés par update_champ_produit : nom_produit, description_courte, actif, id_categorie.
    public function updateChamp(int $id, string $champ, string $valeur): int
    {
        $sql  = 'SELECT update_champ_produit(:id, :champ, :valeur) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id',     $id,    PDO::PARAM_INT);
        $stmt->bindParam(':champ',  $champ);
        $stmt->bindParam(':valeur', $valeur);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    public function effacerProduit(int $id): int
    {
        $sql  = 'SELECT effacer_produit(:id) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    private function _hydrate(array $data): Produit
    {
        return new Produit(
            id_produit:          (int) $data['id_produit'],
            id_categorie:        isset($data['id_categorie']) ? (int) $data['id_categorie'] : null,
            nom_produit:         $data['nom_produit'],
            description_courte:  $data['description_courte'] ?? null,
            fiche_technique_url: $data['fiche_technique_url'] ?? null,
            actif:               (bool) $data['actif']
        );
    }
}
