<?php
require_once __DIR__ . '/../config/config.inc.php';
require_once __DIR__ . '/../models/Device.php';
require_once __DIR__ . '/../models/Leave.php';

class ApprovalController
{
    public function index()
    {
        session_start();

        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'hr'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        include __DIR__ . '/../views/approval/index.php';
    }

    function ajax_deviceBorrowRequest()
    {
        global $mysqli;

        // Filters
        $from = $_GET['from_date'] ?? null;
        $to   = $_GET['to_date'] ?? null;
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $whereDate = "";
        $params = [];
        $types = "";

        if ($from) {
            $whereDate .= " AND DATE(borrow_date) >= ? ";
            $params[] = $from;
            $types .= "s";
        }
        if ($to) {
            $whereDate .= " AND DATE(borrow_date) <= ? ";
            $params[] = $to;
            $types .= "s";
        }

        $count_sql = "SELECT COUNT(*) AS `total`
                 FROM DEVICE_BORROW b
                 WHERE 1=1 $whereDate";

        $count_res = $mysqli->prepare($count_sql);
        if ($types) $count_res->bind_param($types, ...$params);
        $count_res->execute();

        $total = $count_res->get_result()->fetch_assoc()['total'];
        $total_pages = ceil($total / $limit);

        $sql = "SELECT b.borrow_id, e.fullname, b.borrow_date, b.status
                 FROM DEVICE_BORROW b
                 JOIN EMPLOYEES e ON e.employee_id = b.employee_id
                 WHERE 1=1 $whereDate
                 ORDER BY
                    CASE WHEN b.status = 'pending' THEN 0 ELSE 1 END,
                    b.borrow_date DESC
                 LIMIT ?, ?";

        $types .= "ii";
        $params[] = $offset;
        $params[] = $limit;

        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param($types, ...$params);

        $stmt->execute();
        $borrowRequests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        require_once(__DIR__ . '/../helpers/pagination_helper.php');

        ob_start();
        include __DIR__ . '/../views/approval/_borrowRequests.php';
        echo ob_get_clean();
        exit;
    }

    function ajax_leaveRequest()
    {
        global $mysqli;

        // Filters
        $from = $_GET['from_date'] ?? null;
        $to   = $_GET['to_date'] ?? null;
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $whereDate = "";
        $params = [];
        $types = "";

        if ($from) {
            $whereDate .= " AND DATE(l.start_date) >= ? ";
            $params[] = $from;
            $types .= "s";
        }
        if ($to) {
            $whereDate .= " AND DATE(l.start_date) <= ? ";
            $params[] = $to;
            $types .= "s";
        }

        $count_sql = "SELECT COUNT(*) AS `total`
                 FROM LEAVE_REQUESTS l
                 WHERE 1=1 $whereDate";

        $count_res = $mysqli->prepare($count_sql);
        if ($types) $count_res->bind_param($types, ...$params);
        $count_res->execute();

        $total = $count_res->get_result()->fetch_assoc()['total'];
        $total_pages = ceil($total / $limit);

        $sql = "SELECT l.leave_id, e.fullname, l.start_date, l.end_date, l.status
                     FROM LEAVE_REQUESTS l
                     JOIN EMPLOYEES e ON e.employee_id = l.employee_id
                     WHERE 1=1 $whereDate
                     ORDER BY
                        CASE WHEN l.status = 'pending' THEN 0 ELSE 1 END,
                        l.start_date DESC
                     LIMIT ?, ?";

        $types .= "ii";
        $params[] = $offset;
        $params[] = $limit;

        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param($types, ...$params);

        $stmt->execute();
        $leaveRequests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        require_once(__DIR__ . '/../helpers/pagination_helper.php');

        ob_start();
        include __DIR__ . '/../views/approval/_leaveRequests.php';
        echo ob_get_clean();
        exit;
    }

    // Borrow's detail page
    public function detailBorrow()
    {
        session_start();

        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'hr'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        $id = intval($_GET['id'] ?? 0);

        $borrow = Device::getBorrowDetail($id);
        if (!$borrow) {
            echo "Không tìm thấy phiếu mượn.";
            return;
        }

        // Lấy chi tiết thiết bị trong phiếu
        $devices = Device::getBorrowDevices($id);

        include __DIR__ . '/../views/approval/detail_borrow.php';
    }

    public function updateBorrowStatus()
    {
        session_start();

        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'hr'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        $id = intval($_POST['borrow_id']);
        $status = $_POST['status']; // approved, rejected, returned

        Device::updateBorrowStatus($status, $_SESSION['user']['employee_id'], $id);

        $_SESSION['success'] = "Đã cập nhật trạng thái phiếu mượn id={$id}";
        header("Location: index.php?controller=approval&action=index");
        exit;
    }

    // Leave's detail page
    public function detailLeave()
    {
        session_start();

        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'hr'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        $id = intval($_GET['id'] ?? 0);

        // Fetch leave request
        $leave = Leave::getRequestDetail($id);
        if (!$leave) {
            echo "Không tìm thấy đơn nghỉ phép.";
            return;
        }

        // Fetch employee’s leave history
        $history = Leave::getEmployeeHistory($leave['employee_id']);

        include __DIR__ . '/../views/approval/detail_leave.php';
    }

    public function updateLeaveStatus()
    {
        session_start();

        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'hr'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        $id = intval($_POST['leave_id']);
        $status = $_POST['status']; // "approved" or "rejected"

        Leave::updateLeaveStatus($status, $_SESSION['user']['employee_id'], $id);

        $_SESSION['success'] = "Đã cập nhật trạng thái đơn nghỉ phép id={$id}";
        header("Location: index.php?controller=approval&action=index");
        exit;
    }
}
