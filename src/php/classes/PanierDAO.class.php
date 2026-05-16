<?php

declare(strict_types=1);

class PanierDAO
{
    public function __construct(private PDO $_cnx) {}

    public function getPanierParSession(string $idSession): ?Panier
    {
        $sql  = 'SELECT * FROM panier WHERE id_session = :id';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $idSession);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $this->_hydrate($data) : null;
    }

    /**
     * Contenu complet du panier via vue_panier_complet.
     * Retourne un tableau associatif (pas de DTO — la vue mixe plusieurs entités).
     */
    public function getContenuPanier(string $idSession): ?array
    {
        $sql  = 'SELECT * FROM vue_panier_complet WHERE id_session = :id';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $idSession);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function getNbArticles(string $idSession): int
    {
        $sql  = 'SELECT COALESCE(SUM(pv.quantite), 0) AS nb
                   FROM panier pa
                   JOIN panier_variante pv ON pv.id_panier = pa.id_panier
                  WHERE pa.id_session = :id';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $idSession);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    /** Crée ou récupère un panier (ON CONFLICT via plpgsql ajout_panier()). */
    public function ajouterPanier(string $idSession, ?int $idClient): int
    {
        $sql  = 'SELECT ajout_panier(:session, :id_client) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':session',   $idSession);
        $stmt->bindParam(':id_client', $idClient, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    /** Ajoute ou incrémente une variante dans le panier. */
    public function ajouterVariante(int $idPanier, int $idVariante, int $qte): int
    {
        $sql  = 'SELECT ajout_variante_panier(:id_panier, :id_variante, :qte) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id_panier',   $idPanier,   PDO::PARAM_INT);
        $stmt->bindParam(':id_variante', $idVariante, PDO::PARAM_INT);
        $stmt->bindParam(':qte',         $qte,        PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    public function updateQuantite(int $idPanier, int $idVariante, int $qte): int
    {
        $sql  = 'SELECT update_quantite_panier(:id_panier, :id_variante, :qte) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id_panier',   $idPanier,   PDO::PARAM_INT);
        $stmt->bindParam(':id_variante', $idVariante, PDO::PARAM_INT);
        $stmt->bindParam(':qte',         $qte,        PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    public function retirerVariante(int $idPanier, int $idVariante): int
    {
        $sql  = 'SELECT retirer_variante_panier(:id_panier, :id_variante) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id_panier',   $idPanier,   PDO::PARAM_INT);
        $stmt->bindParam(':id_variante', $idVariante, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    /**
     * Crée le panier si inexistant, puis ajoute/incrémente la variante.
     * Raccourci utilisé par les pages publiques.
     */
    public function ajouterOuMaj(string $idSession, int $idVariante, int $qte, ?int $idClient): int
    {
        $idPanier = $this->ajouterPanier($idSession, $idClient);
        if ($idPanier <= 0) {
            return 0;
        }
        return $this->ajouterVariante($idPanier, $idVariante, $qte);
    }

    /** Vide toutes les lignes du panier (après validation commande). */
    public function viderPanier(string $idSession): int
    {
        $sql  = 'SELECT vider_panier(:session) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':session', $idSession);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    /**
     * Fusionne le panier anonyme avec le panier client lors de la connexion.
     * Logique dans plpgsql fusionner_panier().
     */
    public function fusionnerPanier(string $idSession, int $idClient): int
    {
        $sql  = 'SELECT fusionner_panier(:session, :id_client) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':session',   $idSession);
        $stmt->bindParam(':id_client', $idClient, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    private function _hydrate(array $data): Panier
    {
        return new Panier(
            id_panier:         (int) $data['id_panier'],
            id_session:        $data['id_session'],
            id_client:         isset($data['id_client']) ? (int) $data['id_client'] : null,
            date_creation:     $data['date_creation'],
            date_modification: $data['date_modification']
        );
    }
}
