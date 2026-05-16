<?php

declare(strict_types=1);

class AdresseDAO
{
    public function __construct(private PDO $_cnx) {}

    public function getAdressesClient(int $idClient): ?array
    {
        $sql = 'SELECT * FROM adresse WHERE id_client = :id ORDER BY type_adresse';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $idClient, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        return array_map(fn($d) => $this->_hydrate($d), $data);
    }

    public function getAdressesParClient(int $idClient): ?array
    {
        return $this->getAdressesClient($idClient);
    }

    public function getAdresseParId(int $id): ?Adresse
    {
        $sql = 'SELECT * FROM adresse WHERE id_adresse = :id';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $this->_hydrate($data) : null;
    }

    public function ajouterAdresse(
        int $idClient,
        string $type,
        string $nomDest,
        string $rue,
        string $numero,
        ?string $boite,
        string $cp,
        string $ville,
        string $pays
    ): int {
        $sql = 'SELECT ajout_adresse(:id_client, :type, :nom, :rue, :num, :boite, :cp, :ville, :pays) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id_client', $idClient, PDO::PARAM_INT);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':nom', $nomDest);
        $stmt->bindParam(':rue', $rue);
        $stmt->bindParam(':num', $numero);
        $stmt->bindParam(':boite', $boite);
        $stmt->bindParam(':cp', $cp);
        $stmt->bindParam(':ville', $ville);
        $stmt->bindParam(':pays', $pays);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    public function supprimerAdresse(int $id): int
    {
        $sql = 'SELECT supprimer_adresse(:id) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    private function _hydrate(array $data): Adresse
    {
        return new Adresse(
            id_adresse: (int) $data['id_adresse'],
            id_client: (int) $data['id_client'],
            type_adresse: $data['type_adresse'],
            nom_destinataire: $data['nom_destinataire'],
            rue: $data['rue'],
            numero: $data['numero'],
            boite: $data['boite'] ?? null,
            code_postal: $data['code_postal'],
            ville: $data['ville'],
            pays: $data['pays']
        );
    }
}
