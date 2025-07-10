<?php
class AuthController
{
    function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once("config/config.inc.php");
            $username = $_POST['username'];
            $password = $_POST['password'];

            $stmt = $mysqli->prepare("SELECT * FROM USERS WHERE username=? AND `password`=?");
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();

            $result = $stmt->get_result();
            if ($user = $result->fetch_assoc()) {
                $_SESSION['user'] = $user;
                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        }

        include("views/auth/login.php");
    }

    function logout()
    {
        session_destroy();
        header("Location: index.php");
        exit;
    }
}
