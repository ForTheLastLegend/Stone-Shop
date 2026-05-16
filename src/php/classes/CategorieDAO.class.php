<?php

declare(strict_types=1);

class CategorieDAO
{
    public function __construct(private PDO $_cnx) {}

    public function getAllCategories(): ?array
    {
        $sql = 'SELECT * FROM categorie ORDER BY id_categorie_parent NULLS FIRST, nom_categorie';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ? array_map(fn($d) => $this->_hydrate($d), $data) : null;
    }

    public function getCategoriesRacine(): ?array
    {
        $sql = 'SELECT * FROM categorie WHERE id_categorie_parent IS NULL ORDER BY nom_categorie';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ? array_map(fn($d) => $this->_hydrate($d), $data) : null;
    }

    public function getSousCategories(int $idParent): ?array
    {
        $sql = 'SELECT * FROM categorie WHERE id_categorie_parent = :id ORDER BY nom_categorie';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $idParent, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ? array_map(fn($d) => $this->_hydrate($d), $data) : null;
    }

    public function getCategorieParId(int $id): ?Categorie
    {
        $sql = 'SELECT * FROM categorie WHERE id_categorie = :id';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $this->_hydrate($data) : null;
    }

    public function ajouterCategorie(string $nom, ?int $idParent, ?string $image): int
    {
        $sql = 'SELECT ajout_categorie(:nom, :parent, :image) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':parent', $idParent, PDO::PARAM_INT);
        $stmt->bindParam(':image', $image);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    public function modifierCategorie(int $id, string $nom, ?int $idParent, ?string $image): int
    {
        $sql = 'SELECT modifier_categorie(:id, :nom, :parent, :image) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':parent', $idParent, PDO::PARAM_INT);
        $stmt->bindParam(':image', $image);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    public function supprimerCategorie(int $id): int
    {
        $sql = 'SELECT supprimer_categorie(:id) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    private function _hydrate(array $data): Categorie
    {
        return new Categorie(
            (int) $data['id_categorie'],
            $data['nom_categorie'],
            isset($data['id_categorie_parent']) ? (int) $data['id_categorie_parent'] : null,
            $data['image_categorie'] ?? null
        );
    }
}
