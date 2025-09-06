<?php
require_once __DIR__ . '/../config/config.inc.php';

class Leave
{
    public static function getEmployeeTotalUsedInYear($employee_id, $year)
    {
        global $mysqli;
        $sql = "
            SELECT SUM(total) AS used_days
            FROM LEAVE_REQUESTS
            WHERE employee_id = $employee_id
              AND status = 'approved'
              AND YEAR(start_date) = $year
        ";

        $res = $mysqli->query($sql);
        return $res->fetch_assoc()['used_days'] ?? 0;
    }

    public static function insert(
        $employeeId,
        $leave_type,
        $start_date,
        $end_date,
        $details,
        $total,
        $reason_type,
        $description
    ) {
        global $mysqli;
        $stmt = $mysqli->prepare("
            INSERT INTO LEAVE_REQUESTS
            (employee_id, leave_type, start_date, end_date, details, total, reason_type, description, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')
        ");
        $stmt->bind_param(
            "issssiss",
            $employeeId,
            $leave_type,
            $start_date,
            $end_date,
            $details,
            $total,
            $reason_type,
            $description
        );
        $stmt->execute();
    }

    public static function getRequestDetail($id)
    {
        global $mysqli;
        $stmt = $mysqli->prepare("
            SELECT l.*, e.fullname, e.department, e.position
            FROM LEAVE_REQUESTS l
            JOIN EMPLOYEES e ON e.employee_id = l.employee_id
            WHERE l.leave_id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function getEmployeeHistory($employee_id)
    {
        global $mysqli;
        $stmt = $mysqli->prepare("
            SELECT leave_id, start_date, end_date, status
            FROM LEAVE_REQUESTS
            WHERE employee_id = ?
            ORDER BY start_date DESC
        ");

        $stmt->bind_param("i", $employee_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function updateLeaveStatus($status, $approver_id, $leave_id)
    {
        global $mysqli;
        $stmt = $mysqli->prepare(
            "UPDATE LEAVE_REQUESTS SET status=?, approver_id=?
            WHERE leave_id=?
            "
        );

        $stmt->bind_param("sii", $status, $approver_id, $leave_id);
        $stmt->execute();
    }
}
