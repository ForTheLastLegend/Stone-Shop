<?php

declare(strict_types=1);

class MessageChatDAO
{
    public function __construct(private PDO $_cnx) {}

    public function getMessagesByConversation(int $idConv): ?array
    {
        $sql  = 'SELECT * FROM message_chat WHERE id_conversation = :id ORDER BY date_envoi';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id', $idConv, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ? array_map(fn($d) => $this->_hydrate($d), $data) : null;
    }

    public function getMessagesDepuis(int $idConv, int $dernierId): ?array
    {
        $sql  = 'SELECT * FROM message_chat
                  WHERE id_conversation = :id AND id_message > :dernier
                  ORDER BY id_message';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id',      $idConv,    PDO::PARAM_INT);
        $stmt->bindParam(':dernier', $dernierId, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data ? array_map(fn($d) => $this->_hydrate($d), $data) : null;
    }

    /** expediteur_type : 'client' | 'support' */
    public function envoyerMessage(int $idConv, string $expType, string $contenu): int
    {
        $sql  = 'SELECT ajout_message_chat(:id_conv, :exp_type, :contenu) AS retour';
        $stmt = $this->_cnx->prepare($sql);
        $stmt->bindParam(':id_conv',   $idConv,   PDO::PARAM_INT);
        $stmt->bindParam(':exp_type',  $expType);
        $stmt->bindParam(':contenu',   $contenu);
        $stmt->execute();
        return (int) $stmt->fetchColumn(0);
    }

    private function _hydrate(array $data): MessageChat
    {
        return new MessageChat(
            id_message:      (int)  $data['id_message'],
            id_conversation: (int)  $data['id_conversation'],
            expediteur_type: $data['expediteur_type'],
            contenu:         $data['contenu'],
            date_envoi:      $data['date_envoi'],
            lu:              (bool) $data['lu']
        );
    }
}
