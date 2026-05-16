<?php

declare(strict_types=1);

class ConversationDAO
{
    public function __construct(private PDO $_cnx) {}

    /** Conversations d'un client (espace compte). */
    public function getConversationsByClient(int $idClient): ?array
    {
        $sql  = 'SELECT * FROM vue_conversations WHERE id_client = :id ORDER BY date_creation DESC';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $idClient, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    /** Alias utilisé par content/compte/chat.php */
    public function getConversationsParClient(int $idClient): ?array
    {
        return $this->getConversationsByClient($idClient);
    }

    /** Conversations ouvertes ou en cours (interface support/admin). */
    public function getConversationsOuvertes(): ?array
    {
        $sql  = "SELECT * FROM vue_conversations WHERE statut IN ('ouverte','en_cours') ORDER BY messages_non_lus DESC, date_creation";
        $stmt = $this->_cnx->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function getConversationParId(int $id): ?array
    {
        $sql  = 'SELECT * FROM vue_conversations WHERE id_conversation = :id';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function ajouterConversation(int $idClient, string $sujet): int
    {
        $sql  = 'SELECT ajout_conversation(:id_client, :sujet) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id_client', $idClient, PDO::PARAM_INT);
        $stmt->bindParam(':sujet',     $sujet);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    public function assignerSupport(int $idConv, int $idSupport): int
    {
        $sql  = 'SELECT assigner_support(:id_conv, :id_support) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id_conv',    $idConv,    PDO::PARAM_INT);
        $stmt->bindParam(':id_support', $idSupport, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    public function fermerConversation(int $id): int
    {
        $sql  = 'SELECT fermer_conversation(:id) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }
}
