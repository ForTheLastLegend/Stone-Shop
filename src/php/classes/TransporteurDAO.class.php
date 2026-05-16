<?php

declare(strict_types=1);

class TransporteurDAO
{
    public function __construct(private PDO $_cnx) {}

    public function getTransporteursActifs(): ?array
    {
        $sql  = 'SELECT * FROM transporteur WHERE actif = true ORDER BY frais_livraison';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ? array_map(fn($d) => $this->_hydrate($d), $data) : null;
    }

    public function getAllTransporteurs(): ?array
    {
        $sql  = 'SELECT * FROM transporteur ORDER BY nom_transporteur';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ? array_map(fn($d) => $this->_hydrate($d), $data) : null;
    }

    public function getTransporteurParId(int $id): ?Transporteur
    {
        $sql  = 'SELECT * FROM transporteur WHERE id_transporteur = :id';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $this->_hydrate($data) : null;
    }

    public function ajouterTransporteur(string $nom, string $delai, float $frais): int
    {
        $sql  = 'SELECT ajout_transporteur(:nom, :delai, :frais) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':nom',   $nom);
        $stmt->bindParam(':delai', $delai);
        $stmt->bindParam(':frais', $frais);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    public function updateChamp(int $id, string $champ, string $valeur): int
    {
        $sql  = 'SELECT update_champ_transporteur(:id, :champ, :valeur) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id',     $id,    PDO::PARAM_INT);
        $stmt->bindParam(':champ',  $champ);
        $stmt->bindParam(':valeur', $valeur);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    private function _hydrate(array $data): Transporteur
    {
        return new Transporteur(
            id_transporteur:  (int)   $data['id_transporteur'],
            nom_transporteur: $data['nom_transporteur'],
            delai_estime:     $data['delai_estime'] ?? null,
            frais_livraison:  (float) $data['frais_livraison'],
            actif:            (bool)  $data['actif']
        );
    }
}
