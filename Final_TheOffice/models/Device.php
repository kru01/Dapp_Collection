<?php
require_once __DIR__ . '/../config/config.inc.php';

class Device
{
    public static function searchByName($term)
    {
        global $mysqli;

        $stmt = $mysqli->prepare("SELECT device_id, device_name, quantity
            FROM DEVICES WHERE device_name LIKE CONCAT('%', ?, '%') LIMIT 10");
        $stmt->bind_param("s", $term);
        $stmt->execute();
        return $stmt->get_result();
    }

    public static function getBorrowDetail($borrow_id)
    {
        global $mysqli;
        $stmt = $mysqli->prepare("
            SELECT b.*, e.fullname, e.department, e.position
            FROM DEVICE_BORROW b
            JOIN EMPLOYEES e ON e.employee_id = b.employee_id
            WHERE b.borrow_id = ?
        ");
        $stmt->bind_param("i", $borrow_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function getBorrowDevices($borrow_id)
    {
        global $mysqli;
        $stmt = $mysqli->prepare("
            SELECT d.device_name, d.description, bd.note
            FROM DEVICE_BORROW_DETAILS bd
            JOIN DEVICES d ON d.device_id = bd.device_id
            WHERE bd.borrow_id = ?
        ");
        $stmt->bind_param("i", $borrow_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function insertBorrowRecord($employeeId, $expected_return_date)
    {
        global $mysqli;

        $stmt = $mysqli->prepare("
            INSERT INTO DEVICE_BORROW (employee_id, borrow_date, expected_return_date, status)
            VALUES (?, NOW(), ?, 'pending')
        ");
        $stmt->bind_param("is", $employeeId, $expected_return_date);
        $stmt->execute();

        return $stmt->insert_id;
    }

    public static function insertBorrowDetail($borrow_id, $device_id, $note)
    {
        global $mysqli;
        $stmt = $mysqli->prepare("
                INSERT INTO DEVICE_BORROW_DETAILS (borrow_id, device_id, note)
                VALUES (?, ?, ?)
            ");
        $stmt->bind_param("iis", $borrow_id, $device_id, $note);
        $stmt->execute();
    }

    private static function updateQuantity($device_id, $diff)
    {
        global $mysqli;
        $stmt = $mysqli->prepare("UPDATE DEVICES SET quantity = quantity + ? WHERE device_id = ?");
        $stmt->bind_param("ii", $diff, $device_id);
        $stmt->execute();
    }

    public static function updateBorrowStatus($status, $approver_id, $borrow_id)
    {
        global $mysqli;
        $stmt = $mysqli->prepare(
            "UPDATE DEVICE_BORROW SET status=?, approver_id=?
            WHERE borrow_id=?
            "
        );

        $stmt->bind_param("sii", $status, $approver_id, $borrow_id);
        $stmt->execute();

        if ($status === 'rejected' || $status === 'pending') return;

        // If approved or returned, update device quantities
        $diff = $status === 'approved' ? -1 : 1;

        $stmt2 = $mysqli->prepare("SELECT device_id FROM DEVICE_BORROW_DETAILS WHERE borrow_id = ?");
        $stmt2->bind_param("i", $borrow_id);
        $stmt2->execute();
        $result = $stmt2->get_result();

        while ($row = $result->fetch_assoc()) {
            self::updateQuantity($row['device_id'], $diff);
        }
    }
}
