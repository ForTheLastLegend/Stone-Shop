<?php

declare(strict_types=1);

class ListeEnvieDAO
{
    public function __construct(private PDO $_cnx) {}

    /** Liste d'envie enrichie (joint sur vue_catalogue pour avoir prix + image). */
    public function getListeParSession(string $idSession): ?array
    {
        $sql  = 'SELECT le.id_liste_envie, le.date_ajout, vc.*
                   FROM liste_envie le
                   JOIN vue_catalogue vc ON vc.id_variante = le.id_variante
                  WHERE le.id_session = :id
                  ORDER BY le.date_ajout DESC';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $idSession);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function estEnListe(string $idSession, int $idVariante): bool
    {
        $sql  = 'SELECT COUNT(*) FROM liste_envie WHERE id_session = :id AND id_variante = :id_variante';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id',          $idSession);
        $stmt->bindParam(':id_variante', $idVariante, PDO::PARAM_INT);
        $stmt->execute();
        return ((int) $stmt->fetchColumn(0)) > 0;
    }

    public function ajouterVariante(string $idSession, int $idVariante, ?int $idClient): int
    {
        $sql  = 'SELECT ajout_liste_envie(:session, :id_variante, :id_client) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':session',    $idSession);
        $stmt->bindParam(':id_variante', $idVariante, PDO::PARAM_INT);
        $stmt->bindParam(':id_client',   $idClient,   PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    public function retirerVariante(int $id): int
    {
        $sql  = 'SELECT retirer_liste_envie(:id) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    public function retirerVariantePourSession(string $idSession, int $idVariante): int
    {
        $sql  = 'SELECT retirer_liste_envie_var(:session, :id_variante) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':session',     $idSession);
        $stmt->bindParam(':id_variante', $idVariante, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }
}
