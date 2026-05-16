<?php

declare(strict_types=1);

class CommandeDAO
{
    public function __construct(private PDO $_cnx) {}

    public function getAllCommandes(): ?array
    {
        $sql = 'SELECT * FROM commande ORDER BY date_commande DESC';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ? array_map(fn($d) => $this->_hydrate($d), $data) : null;
    }

    public function getCommandesByClient(int $idClient): ?array
    {
        $sql = 'SELECT * FROM commande WHERE id_client = :id ORDER BY date_commande DESC';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $idClient, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ? array_map(fn($d) => $this->_hydrate($d), $data) : null;
    }

    public function getCommandeParId(int $id): ?Commande
    {
        $sql = 'SELECT * FROM commande WHERE id_commande = :id';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $this->_hydrate($data) : null;
    }

    // Renvoie un tableau assoc — la vue contient nom_transporteur, prenom, nom, etc.
    public function getDetailCommande(int $id): ?array
    {
        $sql = 'SELECT * FROM vue_commande_detail WHERE id_commande = :id';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function creerCommande(
        int $idClient,
        int $idAddrLiv,
        int $idAddrFact,
        int $idTransp,
        ?int $idCodePromo,
        float $total,
        string $methode
    ): int {
        $sql = 'SELECT creer_commande(:id_client, :addr_liv, :addr_fact, :transp, :code_promo, :total, :methode) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id_client', $idClient, PDO::PARAM_INT);
        $stmt->bindParam(':addr_liv', $idAddrLiv, PDO::PARAM_INT);
        $stmt->bindParam(':addr_fact', $idAddrFact, PDO::PARAM_INT);
        $stmt->bindParam(':transp', $idTransp, PDO::PARAM_INT);
        $stmt->bindParam(':code_promo', $idCodePromo, PDO::PARAM_INT);
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':methode', $methode);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    /**
     * Checkout atomique via plpgsql valider_commande() :
     * INSERT commande + lignes + UPDATE stock + DELETE panier_variante en un
     * seul bloc. Toute exception SQL → rollback complet, retour 0.
     *
     * $lignesJson : tableau JSON des lignes panier au format
     *   [{"id_variante":2,"qte":1,"prix":"99.99"}, ...]
     */
    public function validerCommande(
        int $idClient,
        int $idAddrLiv,
        int $idAddrFact,
        int $idTransp,
        ?int $idCodePromo,
        float $total,
        string $methode,
        string $idSession,
        string $lignesJson
    ): int {
        $sql = 'SELECT valider_commande(:id_client, :addr_liv, :addr_fact, :transp,
                                          :code_promo, :total, :methode,
                                          :id_session, :lignes::jsonb) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id_client', $idClient, PDO::PARAM_INT);
        $stmt->bindParam(':addr_liv', $idAddrLiv, PDO::PARAM_INT);
        $stmt->bindParam(':addr_fact', $idAddrFact, PDO::PARAM_INT);
        $stmt->bindParam(':transp', $idTransp, PDO::PARAM_INT);
        $stmt->bindParam(':code_promo', $idCodePromo, PDO::PARAM_INT);
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':methode', $methode);
        $stmt->bindParam(':id_session', $idSession);
        $stmt->bindParam(':lignes', $lignesJson);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    public function ajouterLigneCommande(int $idCommande, int $idVariante, int $qte, float $prix): int
    {
        $sql = 'SELECT ajouter_ligne_commande(:id_commande, :id_variante, :qte, :prix) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id_commande', $idCommande, PDO::PARAM_INT);
        $stmt->bindParam(':id_variante', $idVariante, PDO::PARAM_INT);
        $stmt->bindParam(':qte', $qte, PDO::PARAM_INT);
        $stmt->bindParam(':prix', $prix);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    public function getCommandesParStatut(string $statut): ?array
    {
        $sql = 'SELECT * FROM commande WHERE statut_commande = :statut ORDER BY date_commande DESC';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':statut', $statut);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ? array_map(fn($d) => $this->_hydrate($d), $data) : null;
    }

    public function updateStatut(int $id, string $statut): int
    {
        return $this->updateChamp($id, 'statut_commande', $statut);
    }

    public function getVariantesAchetees(int $idClient): ?array
    {
        $sql = 'SELECT DISTINCT vp.id_variante, vp.nom_variante, p.nom_produit
                   FROM commande co
                   JOIN commande_variante cv ON cv.id_commande = co.id_commande
                   JOIN variante_produit vp  ON vp.id_variante = cv.id_variante
                   JOIN produit p            ON p.id_produit   = vp.id_produit
                  WHERE co.id_client = :id
                    AND co.statut_commande NOT IN (\'annulee\')
                  ORDER BY p.nom_produit';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $idClient, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    // Champs acceptés par update_champ_commande : statut_commande, numero_suivi, statut_paiement.
    public function updateChamp(int $id, string $champ, string $valeur): int
    {
        $sql = 'SELECT update_champ_commande(:id, :champ, :valeur) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':champ', $champ);
        $stmt->bindParam(':valeur', $valeur);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    private function _hydrate(array $data): Commande
    {
        return new Commande(
            id_commande: (int) $data['id_commande'],
            id_client: (int) $data['id_client'],
            id_adresse_livraison: (int) $data['id_adresse_livraison'],
            id_adresse_facturation: (int) $data['id_adresse_facturation'],
            id_transporteur: (int) $data['id_transporteur'],
            id_code_promo: isset($data['id_code_promo']) ? (int) $data['id_code_promo'] : null,
            date_commande: $data['date_commande'],
            total_commande: (float) $data['total_commande'],
            methode_paiement: $data['methode_paiement'],
            statut_paiement: (bool) $data['statut_paiement'],
            statut_commande: $data['statut_commande'],
            numero_suivi: $data['numero_suivi'] ?? null
        );
    }
}
