<?php

declare(strict_types=1);

class PromotionDAO
{
    public function __construct(private PDO $_cnx) {}

    public function getAllPromotions(): ?array
    {
        $sql  = 'SELECT * FROM promotion ORDER BY date_debut DESC';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ? array_map(fn($d) => $this->_hydrate($d), $data) : null;
    }

    public function getPromotionsActives(): ?array
    {
        $sql  = "SELECT * FROM promotion WHERE actif = true AND now() BETWEEN date_debut AND date_fin";
        $stmt = $this->_cnx->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ? array_map(fn($d) => $this->_hydrate($d), $data) : null;
    }

    /** Variantes liées à une promotion (table de liaison). */
    public function getVariantesPromotion(int $idPromotion): ?array
    {
        $sql  = 'SELECT id_variante FROM promotion_variante WHERE id_promotion = :id';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $idPromotion, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: null;
    }

    public function ajouterPromotion(string $nom, float $taux, string $debut, string $fin): int
    {
        $sql  = 'SELECT ajout_promotion(:nom, :taux, :debut, :fin) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':nom',   $nom);
        $stmt->bindParam(':taux',  $taux);
        $stmt->bindParam(':debut', $debut);
        $stmt->bindParam(':fin',   $fin);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    public function lierVariante(int $idPromotion, int $idVariante): int
    {
        $sql  = 'SELECT lier_promotion_variante(:id_promo, :id_variante) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id_promo',   $idPromotion, PDO::PARAM_INT);
        $stmt->bindParam(':id_variante', $idVariante,  PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    public function supprimerPromotion(int $id): int
    {
        $sql  = 'SELECT supprimer_promotion(:id) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    private function _hydrate(array $data): Promotion
    {
        return new Promotion(
            id_promotion:   (int)   $data['id_promotion'],
            nom_promotion:  $data['nom_promotion'],
            taux_reduction: (float) $data['taux_reduction'],
            date_debut:     $data['date_debut'],
            date_fin:       $data['date_fin'],
            actif:          (bool)  $data['actif']
        );
    }
}
