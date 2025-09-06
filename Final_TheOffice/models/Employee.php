<?php
require_once __DIR__ . '/../config/config.inc.php';

class Employee
{
    public static function findByUserId($user_id)
    {
        global $mysqli;

        $stmt = $mysqli->prepare("SELECT * FROM EMPLOYEES WHERE user_id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public static function getOverviewStats($employeeId)
    {
        global $mysqli;

        // 1. Số ngày phép đã nghỉ trong năm hiện tại
        $year = date('Y');
        $sql_leave_days = "
    SELECT SUM(total) AS total_days
    FROM LEAVE_REQUESTS
    WHERE employee_id = $employeeId
      AND status = 'approved'
      AND YEAR(start_date) = $year
";
        $res = $mysqli->query($sql_leave_days);
        $leave_days = $res->fetch_assoc()['total_days'] ?? 0;

        // 2. Số thiết bị đang mượn (phiếu chưa trả)
        $sql_devices_borrowed = "
    SELECT COUNT(*) AS cnt
    FROM DEVICE_BORROW
    WHERE employee_id = $employeeId
      AND status = 'approved'
";
        $res = $mysqli->query($sql_devices_borrowed);
        $devices_borrowed = $res->fetch_assoc()['cnt'] ?? 0;

        // 3. Số yêu cầu đang chờ duyệt (nghỉ phép + mượn thiết bị)
        $sql_pending = "
    SELECT (
        (SELECT COUNT(*) FROM LEAVE_REQUESTS
         WHERE employee_id = $employeeId AND status = 'pending')
        +
        (SELECT COUNT(*) FROM DEVICE_BORROW
         WHERE employee_id = $employeeId AND status = 'pending')
    ) AS total_pending
";
        $res = $mysqli->query($sql_pending);
        $pending_requests = $res->fetch_assoc()['total_pending'] ?? 0;

        // 4. Danh sách đơn nghỉ phép (pending/approved) - mới nhất trước
        $sql_leaves = "
    SELECT * FROM LEAVE_REQUESTS
    WHERE employee_id = $employeeId
      AND status IN ('pending','approved')
    ORDER BY start_date DESC
    LIMIT 5
";
        $leaves = $mysqli->query($sql_leaves)->fetch_all(MYSQLI_ASSOC);

        // 5. Danh sách thiết bị đang mượn
        $sql_borrows = "
    SELECT b.borrow_id, b.borrow_date, GROUP_CONCAT(d.device_name SEPARATOR ', ') AS devices
    FROM DEVICE_BORROW b
    JOIN DEVICE_BORROW_DETAILS bd ON b.borrow_id = bd.borrow_id
    JOIN DEVICES d ON bd.device_id = d.device_id
    WHERE b.employee_id = $employeeId AND b.status = 'approved'
    GROUP BY b.borrow_id, b.borrow_date
    ORDER BY b.borrow_date DESC
    LIMIT 5
";
        $borrows = $mysqli->query($sql_borrows)->fetch_all(MYSQLI_ASSOC);


        return [
            'leave_days' => $leave_days,
            'devices_borrowed' => $devices_borrowed,
            'pending_requests' => $pending_requests,
            'leaves' => $leaves,
            'borrows' => $borrows
        ];
    }
}
