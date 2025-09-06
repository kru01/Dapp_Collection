<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="container mt-4">
    <h3>Yêu cầu chờ duyệt</h3>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'];
                                        unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'];
                                            unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <!-- Filter form -->
    <form class="row g-3 mb-4">
        <input type="hidden" name="controller" value="approval">
        <input type="hidden" name="action" value="index">

        <div class="col-auto">
            <label>Từ ngày</label>
            <input type="date" name="from_date" value="<?= htmlspecialchars($_GET['from_date'] ?? '') ?>" class="form-control">
        </div>
        <div class="col-auto">
            <label>Đến ngày</label>
            <input type="date" name="to_date" value="<?= htmlspecialchars($_GET['to_date'] ?? '') ?>" class="form-control">
        </div>
        <div class="col-auto align-self-end">
            <button class="btn btn-primary">Lọc</button>
        </div>
    </form>

    <!-- Device borrow requests -->
    <div id="device-borrow-requests"></div>

    <!-- Leave requests -->
    <div id="leave-requests"></div>
</div>

<script>
    function loadDeviceBorrowRequests(page) {
        const from = document.querySelector('input[name="from_date"]').value;
        const to = document.querySelector('input[name="to_date"]').value;

        const params = new URLSearchParams({
            controller: 'approval',
            action: 'ajax_deviceBorrowRequest',
            from_date: from,
            to_date: to,
            page: page
        });

        fetch('index.php?' + params.toString())
            .then(response => response.text())
            .then(html => {
                document.getElementById('device-borrow-requests').innerHTML = html;
            });
    }

    function loadLeaveRequests(page) {
        const from = document.querySelector('input[name="from_date"]').value;
        const to = document.querySelector('input[name="to_date"]').value;

        const params = new URLSearchParams({
            controller: 'approval',
            action: 'ajax_leaveRequest',
            from_date: from,
            to_date: to,
            page: page
        });

        fetch('index.php?' + params.toString())
            .then(response => response.text())
            .then(html => {
                document.getElementById('leave-requests').innerHTML = html;
            });
    }

    // Load on page load
    window.addEventListener('DOMContentLoaded', function(e) {
        loadDeviceBorrowRequests(1);
        loadLeaveRequests(1);
    });

    // Optionally, reload on filter submit
    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();
        loadDeviceBorrowRequests(1);
        loadLeaveRequests(1);
    });
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>