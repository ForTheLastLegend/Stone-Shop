<?php

declare(strict_types=1);

class MessageContactDAO
{
    public function __construct(private PDO $_cnx) {}

    public function getAllMessages(): ?array
    {
        $sql  = 'SELECT * FROM message_contact ORDER BY date_envoi DESC';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ? array_map(fn($d) => $this->_hydrate($d), $data) : null;
    }

    public function getMessagesNonTraites(): ?array
    {
        $sql  = 'SELECT * FROM message_contact WHERE traite = false ORDER BY date_envoi';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ? array_map(fn($d) => $this->_hydrate($d), $data) : null;
    }

    public function ajouterMessage(?int $idClient, string $nom, string $email, string $sujet, string $contenu): int
    {
        $sql  = 'SELECT ajout_message_contact(:id_client, :nom, :email, :sujet, :contenu) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id_client', $idClient, PDO::PARAM_INT);
        $stmt->bindParam(':nom',       $nom);
        $stmt->bindParam(':email',     $email);
        $stmt->bindParam(':sujet',     $sujet);
        $stmt->bindParam(':contenu',   $contenu);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    public function marquerTraite(int $id): int
    {
        $sql  = 'SELECT marquer_contact_traite(:id) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    private function _hydrate(array $data): MessageContact
    {
        return new MessageContact(
            id_message:   (int)  $data['id_message'],
            id_client:    isset($data['id_client']) ? (int) $data['id_client'] : null,
            nom_contact:  $data['nom_contact'],
            email_contact: $data['email_contact'],
            sujet:        $data['sujet'],
            contenu:      $data['contenu'],
            date_envoi:   $data['date_envoi'],
            traite:       (bool) $data['traite']
        );
    }
}
