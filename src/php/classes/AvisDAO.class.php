<?php

declare(strict_types=1);

class AvisDAO
{
    public function __construct(private PDO $_cnx) {}

    /** Avis publics approuvés pour une variante (via vue_avis_approuves). */
    public function getAvisApprouvesByVariante(int $idVariante): ?array
    {
        $sql  = 'SELECT * FROM vue_avis_approuves WHERE id_variante = :id ORDER BY date_avis DESC';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $idVariante, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    /** Note moyenne approuvée d'une variante. */
    public function getNoteMoyenne(int $idVariante): float
    {
        $sql  = 'SELECT COALESCE(AVG(note), 0) FROM avis WHERE id_variante = :id AND modere = \'approuve\'';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $idVariante, PDO::PARAM_INT);
        $stmt->execute();
        return round((float) $stmt->fetchColumn(0), 1);
    }

    /** Tous les avis d'un client (pour son espace compte). Retour tableau assoc brut (cf. RULES_V2 §6). */
    public function getAvisByClient(int $idClient): ?array
    {
        $sql  = 'SELECT a.*, v.nom_variante, p.nom_produit
                   FROM avis a
                   JOIN variante_produit v ON v.id_variante = a.id_variante
                   JOIN produit p          ON p.id_produit  = v.id_produit
                  WHERE a.id_client = :id
                  ORDER BY a.date_avis DESC';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $idClient, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    /** Avis en attente de modération (admin). Retour tableau assoc brut (cf. RULES_V2 §6). */
    public function getAvisEnAttente(): ?array
    {
        $sql  = "SELECT a.*, v.nom_variante, p.nom_produit
                   FROM avis a
                   JOIN variante_produit v ON v.id_variante = a.id_variante
                   JOIN produit p          ON p.id_produit  = v.id_produit
                  WHERE a.modere = 'en_attente'
                  ORDER BY a.date_avis";
        $stmt = $this->_cnx->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    /**
     * Vérifie qu'un client a bien acheté la variante avant de laisser un avis.
     */
    public function clientACommande(int $idClient, int $idVariante): bool
    {
        $sql  = 'SELECT COUNT(*) FROM commande co
                   JOIN commande_variante cv ON cv.id_commande = co.id_commande
                  WHERE co.id_client = :id_client AND cv.id_variante = :id_variante';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id_client',  $idClient,  PDO::PARAM_INT);
        $stmt->bindParam(':id_variante', $idVariante, PDO::PARAM_INT);
        $stmt->execute();
        return ((int) $stmt->fetchColumn(0)) > 0;
    }

    public function ajouterAvis(int $idClient, int $idVariante, int $note, ?string $titre, string $commentaire): int
    {
        $sql  = 'SELECT ajout_avis(:id_client, :id_variante, :note, :titre, :commentaire) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id_client',  $idClient,     PDO::PARAM_INT);
        $stmt->bindParam(':id_variante', $idVariante,  PDO::PARAM_INT);
        $stmt->bindParam(':note',        $note,        PDO::PARAM_INT);
        $stmt->bindParam(':titre',       $titre);
        $stmt->bindParam(':commentaire', $commentaire);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    /** statut : 'approuve' | 'refuse' */
    public function modererAvis(int $id, string $statut): int
    {
        $sql  = 'SELECT moderer_avis(:id, :statut) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id',     $id,     PDO::PARAM_INT);
        $stmt->bindParam(':statut', $statut);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

}
