<?php
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../models/Leave.php';


class LeaveController
{
    public function createForm()
    {
        session_start();

        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        include __DIR__ . '/../views/leave/create.php';
    }

    public function store()
    {
        session_start();

        $employee = Employee::findByUserId($_SESSION['user']['user_id']);
        $employeeId = $employee['employee_id'];

        $leave_type = $_POST['leave_type'] ?? '';
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $reason_type = $_POST['reason_type'] ?? '';
        $description = $_POST['description'] ?? '';
        $details = $_POST['details_json'] ?? '{}'; // JSON string

        $details_arr = json_decode($details, true);
        $total = 0;
        if ($details_arr) {
            foreach ($details_arr as $day => $parts) {
                $count = 0;
                if (!empty($parts['Sáng'])) $count += 0.5;
                if (!empty($parts['Chiều'])) $count += 0.5;
                $total += $count;
            }
        }

        // Check annual leave limit (12 days/year)
        $year = date('Y', strtotime($start_date));
        $used_days = Leave::getEmployeeTotalUsedInYear($employeeId, $year);

        if (($used_days + $total) > 12 && $leave_type == 'nghỉ phép năm') {
            $_SESSION['error'] = "Bạn đã vượt quá số ngày phép năm (12 ngày).";
            header("Location: index.php?controller=leave&action=createForm");
            exit;
        }

        // Insert new leave request
        Leave::insert(
            $employeeId,
            $leave_type,
            $start_date,
            $end_date,
            $details,
            $total,
            $reason_type,
            $description
        );

        $_SESSION['success'] = "Đã tạo đơn nghỉ phép thành công!";
        header("Location: index.php?controller=leave&action=createForm");
        exit;
    }
}
