<?php

declare(strict_types=1);

class RootDAO
{
    public function __construct(private PDO $_cnx) {}

    /**
     * Retourne la row root complète (incluant mot_de_passe hashé) pour le login.
     * Utilisé en duo avec Password::verify côté PHP.
     */
    public function getRootCompletParLogin(string $login): ?array
    {
        $sql  = 'SELECT * FROM get_root_complet_par_login(:login)';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    /** Crée un compte root via plpgsql ajout_root(). Retour : id, -1 si login pris, 0 si erreur. */
    public function ajouterRoot(string $login, string $mdp): int
    {
        $sql  = 'SELECT ajout_root(:login, :mdp) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':mdp',   $mdp);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }
}
