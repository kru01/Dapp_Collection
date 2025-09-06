<?php
require_once __DIR__ . '/../models/Employee.php';

class EmployeeController
{
    public function home()
    {
        session_start();
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        $user = $_SESSION['user'];
        $employee = Employee::findByUserId($user['user_id']);

        $stats = Employee::getOverviewStats($employee['employee_id']);
        extract($stats);

        require __DIR__ . '/../views/employee/home.php';
    }

    public function edit_profile()
    {
        session_start();
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        $user = Employee::findByUserId($_SESSION['user']['user_id']);

        include __DIR__ . '/../views/employee/edit_profile.php';
    }

    public function update_profile()
    {
        session_start();
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        global $mysqli;

        $fullname = $_POST['fullname'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $work_history = $_POST['work_history'] ?? '';

        $user_id = $_SESSION['user']['user_id'];

        $fields = ['fullname=?', 'phone=?', 'work_history=?'];
        $params = [$fullname, $phone, $work_history];
        $types = 'sss';

        if (!empty($_FILES['avatar']['name'])) {
            $filename = basename($_FILES['avatar']['name']);
            $fileName = "{$user_id}_profile." . pathinfo($filename, PATHINFO_EXTENSION);

            $upload_path = __DIR__ . "/../uploads/images/" . $fileName;
            $image_path = "/uploads/images/" . $fileName;

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_path)) {
                $fields[] = 'image_path = ?';
                $params[] = $image_path;
                $types .= 's';
            }
        }

        $fields_sql = implode(', ', $fields);
        $params[] = $user_id;
        $types .= 'i';

        $stmt = $mysqli->prepare("UPDATE EMPLOYEES SET {$fields_sql} WHERE employee_id=?");
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        $_SESSION['success'] = "Cập nhật hồ sơ thành công.";
        header("Location: index.php?controller=employee&action=edit_profile");
        exit;
    }
}
