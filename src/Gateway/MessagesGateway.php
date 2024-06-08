<?php

namespace App\Gateway;

use PDO;

class MessagesGateway
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllMessages()
    {
        $stmt = $this->pdo->query("SELECT * FROM messages ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function addMessage($username, $message)
    {
        $stmt = $this->pdo->prepare("INSERT INTO messages (username, message) VALUES (:username, :message)");
        return $stmt->execute(['username' => $username, 'message' => $message]);
    }

    public function getMessagesWithPagination($limit, $offset)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM messages ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTotalMessageCount()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM messages");
        return $stmt->fetch()['count'];
    }

    public function getMessageById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM messages WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function deleteMessage($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM messages WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
