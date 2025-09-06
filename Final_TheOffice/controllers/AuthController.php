<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Employee.php';

class AuthController
{
    public function login()
    {
        require __DIR__ . '/../views/auth/login.php';
    }

    public function doLogin()
    {
        session_start();
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = User::findByEmailAndPassword($email, $password);

        if ($user) {
            $_SESSION['user'] = $user;

            $employee = Employee::findByUserId($user['user_id']);

            $_SESSION['user']['employee_id'] = $employee['employee_id'];
            $_SESSION['user']['role'] = $employee['role'];

            header("Location: index.php?controller=employee&action=home");
            exit();
        } else {
            $error = "Invalid email or password!";
            require __DIR__ . '/../views/auth/login.php';
        }
    }

    public function logout()
    {
        session_start();
        session_destroy();
        header("Location: index.php?controller=auth&action=login");
        exit();
    }
}
