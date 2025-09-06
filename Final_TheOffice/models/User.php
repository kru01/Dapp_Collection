<?php
require_once __DIR__ . '/../config/config.inc.php';

class User
{
    public static function findByEmailAndPassword($email, $password)
    {
        global $mysqli;

        $stmt = $mysqli->prepare("SELECT * FROM USERS WHERE email=? AND password=? AND status='active'");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
