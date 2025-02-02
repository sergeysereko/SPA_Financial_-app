<?php
namespace App\Api;

use App\Database;

class Operations{
    private $db;

    public function __construct(Database $db){
        $this->db = $db->getPDO();
    }

    public function add($userId, $amount, $type, $comment) {
        $stmt = $this->db->prepare("INSERT INTO operations (user_id, amount, type, comment) VALUES (?,?,?,?)");
        return $stmt->execute([$userId, $amount, $type, $comment]);
    }
}