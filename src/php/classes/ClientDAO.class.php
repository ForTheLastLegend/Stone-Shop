<?php

declare(strict_types=1);

class ClientDAO
{
    public function __construct(private PDO $_cnx) {}

    // Retour : ['id_client' => int, 'prenom_client' => string] ou null.
    public function getClientParEmail(string $email): ?array
    {
        $sql = 'SELECT * FROM get_client_par_email(:email)';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    // Inclut mot_de_passe hashé — à utiliser avec Password::verify côté PHP.
    public function getClientCompletParEmail(string $email): ?array
    {
        $sql = 'SELECT * FROM get_client_complet_par_email(:email)';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function getClientParId(int $id): ?Client
    {
        $sql = 'SELECT * FROM client WHERE id_client = :id';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $this->_hydrate($data) : null;
    }

    // Champs acceptés par update_champ_client : nom_client, prenom_client, email_client, telephone, mot_de_passe.
    public function updateChamp(int $id, string $champ, string $valeur): int
    {
        $sql = 'SELECT update_champ_client(:id, :champ, :valeur) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':champ', $champ);
        $stmt->bindParam(':valeur', $valeur);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    public function ajouterClient(
        string $nom,
        string $prenom,
        string $email,
        string $mdp,
        string $tel
    ): int {
        $sql = 'SELECT ajout_client(:nom, :prenom, :email, :mdp, :tel) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mdp', $mdp);
        $stmt->bindParam(':tel', $tel);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    public function modifierClient(int $id, string $nom, string $prenom, string $email, string $tel): int
    {
        $sql = 'SELECT modifier_client(:id, :nom, :prenom, :email, :tel) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':tel', $tel);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    public function modifierMotDePasse(int $id, string $mdp): int
    {
        $sql = 'SELECT modifier_mdp_client(:id, :mdp) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':mdp', $mdp);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    public function getClientsPagine(int $offset, int $limit): ?array
    {
        $sql = 'SELECT id_client, nom_client, prenom_client, email_client, telephone, date_inscription
                   FROM client
                  ORDER BY id_client
                  LIMIT :limit OFFSET :offset';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ? array_map(fn($d) => $this->_hydrate($d), $data) : null;
    }

    public function compterClients(): int
    {
        $sql = 'SELECT COUNT(*) FROM client';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    private function _hydrate(array $data): Client
    {
        return new Client(
            id_client: (int) $data['id_client'],
            nom_client: $data['nom_client'],
            prenom_client: $data['prenom_client'],
            email_client: $data['email_client'],
            telephone: $data['telephone'] ?? null,
            date_inscription: $data['date_inscription']
        );
    }
}
