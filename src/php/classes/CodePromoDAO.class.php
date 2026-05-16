<?php

declare(strict_types=1);

class CodePromoDAO
{
    public function __construct(private PDO $_cnx) {}

    public function getAllCodePromos(): ?array
    {
        $sql  = 'SELECT * FROM code_promo ORDER BY date_fin DESC';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ? array_map(fn($d) => $this->_hydrate($d), $data) : null;
    }

    /** Vérifie et retourne un code promo valide (actif + dates + usage). */
    public function getCodePromoParCode(string $code): ?CodePromo
    {
        $sql  = "SELECT * FROM code_promo
                  WHERE code = :code
                    AND actif = true
                    AND now() BETWEEN date_debut AND date_fin
                    AND (usage_max IS NULL OR usage_actuel < usage_max)";
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $this->_hydrate($data) : null;
    }

    public function ajouterCodePromo(
        string $code,
        float  $taux,
        string $debut,
        string $fin,
        ?int   $usageMax
    ): int {
        $sql  = 'SELECT ajout_code_promo(:code, :taux, :debut, :fin, :usage_max) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':code',      $code);
        $stmt->bindParam(':taux',      $taux);
        $stmt->bindParam(':debut',     $debut);
        $stmt->bindParam(':fin',       $fin);
        $stmt->bindParam(':usage_max', $usageMax, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    public function incrementerUsage(int $id): int
    {
        $sql  = 'SELECT incrementer_usage_code_promo(:id) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    private function _hydrate(array $data): CodePromo
    {
        return new CodePromo(
            id_code_promo:  (int)   $data['id_code_promo'],
            code:           $data['code'],
            taux_reduction: (float) $data['taux_reduction'],
            date_debut:     $data['date_debut'],
            date_fin:       $data['date_fin'],
            usage_max:      isset($data['usage_max']) ? (int) $data['usage_max'] : null,
            usage_actuel:   (int)   $data['usage_actuel'],
            actif:          (bool)  $data['actif']
        );
    }
}
