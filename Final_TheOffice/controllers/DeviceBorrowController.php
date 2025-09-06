<?php
require_once __DIR__ . '/../config/config.inc.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../models/Device.php';

class DeviceBorrowController
{
    public function createForm()
    {
        session_start();

        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        include __DIR__ . '/../views/device_borrow/create.php';
    }

    // AJAX search
    public function search()
    {
        $term = $_GET['term'] ?? '';
        $res = Device::searchByName($term);

        header('Content-Type: application/json');
        echo json_encode($res->fetch_all(MYSQLI_ASSOC));
        exit;
    }

    // Store borrow request
    public function store()
    {
        session_start();

        $employee = Employee::findByUserId($_SESSION['user']['user_id']);
        $employeeId = $employee['employee_id'];

        $devices = $_POST['devices'] ?? [];  // array of device_id
        $notes = $_POST['notes'] ?? [];      // array of notes
        $expected_return_date = $_POST['expected_return_date'];

        if (count($devices) == 0 || count($devices) > 2) {
            $_SESSION['error'] = "Bạn phải chọn từ 1 đến 2 thiết bị.";
            header("Location: index.php?controller=deviceBorrow&action=createForm");
            exit;
        }

        // Insert borrow record
        $borrow_id = Device::insertBorrowRecord($employeeId, $expected_return_date);

        // Insert details
        foreach ($devices as $idx => $device_id) {
            $note = $notes[$idx] ?? '';
            Device::insertBorrowDetail($borrow_id, $device_id, $note);
        }

        $_SESSION['success'] = "Đã tạo yêu cầu mượn thiết bị!";
        header("Location: index.php?controller=deviceBorrow&action=createForm");
        exit;
    }
}
