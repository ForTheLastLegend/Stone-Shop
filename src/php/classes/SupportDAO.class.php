<?php

declare(strict_types=1);

class SupportDAO
{
    public function __construct(private PDO $_cnx) {}

    /**
     * Retourne la row support complète (incluant mot_de_passe hashé) pour le login.
     * Utilisé en duo avec Password::verify côté PHP.
     */
    public function getSupportCompletParEmail(string $email): ?array
    {
        $sql  = 'SELECT * FROM get_support_complet_par_email(:email)';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    /**
     * Crée un support via plpgsql ajout_support().
     * Retourne l'id inséré, -1 si email déjà existant, 0 si erreur.
     */
    public function ajouterSupport(string $nom, string $prenom, string $email, string $mdp): int
    {
        $sql  = 'SELECT ajout_support(:nom, :prenom, :email, :mdp) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':nom',    $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':email',  $email);
        $stmt->bindParam(':mdp',    $mdp);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    /** Liste paginée des supports. */
    public function getSupportsPagine(int $offset, int $limit): ?array
    {
        $sql  = 'SELECT id_support, nom_support, prenom_support, email_support
                   FROM support
                  ORDER BY id_support
                  LIMIT :limit OFFSET :offset';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function compterSupports(): int
    {
        $sql  = 'SELECT COUNT(*) FROM support';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }
}
