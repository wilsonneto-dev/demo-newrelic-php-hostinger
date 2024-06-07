<?php

namespace App\Gateway;

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
}
