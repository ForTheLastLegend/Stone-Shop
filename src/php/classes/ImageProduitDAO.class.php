<?php

declare(strict_types=1);

class ImageProduitDAO
{
    public function __construct(private PDO $_cnx) {}

    public function getImagesByVariante(int $idVariante): ?array
    {
        $sql  = 'SELECT * FROM image_produit WHERE id_variante = :id ORDER BY ordre';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $idVariante, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ? array_map(fn($d) => $this->_hydrate($d), $data) : null;
    }

    public function getImagePrincipale(int $idVariante): ?ImageProduit
    {
        $sql  = 'SELECT * FROM image_produit WHERE id_variante = :id ORDER BY ordre LIMIT 1';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $idVariante, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $this->_hydrate($data) : null;
    }

    public function ajouterImage(int $idVariante, string $url, int $ordre, ?string $alt): int
    {
        $sql  = 'SELECT ajout_image(:id_variante, :url, :ordre, :alt) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id_variante', $idVariante, PDO::PARAM_INT);
        $stmt->bindParam(':url',         $url);
        $stmt->bindParam(':ordre',       $ordre,      PDO::PARAM_INT);
        $stmt->bindParam(':alt',         $alt);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    public function supprimerImage(int $id): int
    {
        $sql  = 'SELECT supprimer_image(:id) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    /** URLs de toutes les images d'une variante — utile avant cascade DB. */
    public function getUrlsByVariante(int $idVariante): array
    {
        $sql  = 'SELECT url_image FROM image_produit WHERE id_variante = :id';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $idVariante, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0) ?: [];
    }

    /** URLs de toutes les images d'un produit (toutes variantes confondues). */
    public function getUrlsByProduit(int $idProduit): array
    {
        $sql  = 'SELECT ip.url_image
                   FROM image_produit ip
                   JOIN variante_produit v ON v.id_variante = ip.id_variante
                  WHERE v.id_produit = :id';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $idProduit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0) ?: [];
    }

    /** Toutes les URLs d'images stockées en DB — utilisé par le script de nettoyage. */
    public function getAllUrls(): array
    {
        $sql  = 'SELECT url_image FROM image_produit';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0) ?: [];
    }

    private function _hydrate(array $data): ImageProduit
    {
        return new ImageProduit(
            id_image:    (int) $data['id_image'],
            id_variante: (int) $data['id_variante'],
            url_image:   $data['url_image'],
            ordre:       (int) $data['ordre'],
            alt_text:    $data['alt_text'] ?? null
        );
    }
}
