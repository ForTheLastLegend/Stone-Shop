<?php

declare(strict_types=1);

class AdminDAO
{
    public function __construct(private PDO $_cnx) {}

    /**
     * Retourne la row admin complète (incluant mot_de_passe hashé) pour le login.
     * Utilisé en duo avec Password::verify côté PHP.
     */
    public function getAdminCompletParEmail(string $email): ?array
    {
        $sql  = 'SELECT * FROM get_admin_complet_par_email(:email)';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    /**
     * Crée un administrateur via plpgsql ajout_admin().
     * Retourne l'id inséré, -1 si email déjà existant, 0 si erreur.
     */
    public function ajouterAdmin(string $nom, string $prenom, string $email, string $mdp): int
    {
        $sql  = 'SELECT ajout_admin(:nom, :prenom, :email, :mdp) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':nom',    $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':email',  $email);
        $stmt->bindParam(':mdp',    $mdp);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    /** Liste paginée des admins. */
    public function getAdminsPagine(int $offset, int $limit): ?array
    {
        $sql  = 'SELECT id_admin, nom_admin, prenom_admin, email_admin
                   FROM admin
                  ORDER BY id_admin
                  LIMIT :limit OFFSET :offset';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function compterAdmins(): int
    {
        $sql  = 'SELECT COUNT(*) FROM admin';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }
}
