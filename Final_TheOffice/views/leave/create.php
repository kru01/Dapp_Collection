<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="container mt-4">
    <h3>Đăng ký nghỉ phép</h3>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'];
                                        unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'];
                                            unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php?controller=leave&action=store" id="leaveForm">
        <div class="mb-3">
            <label>Loại nghỉ</label>
            <select name="leave_type" class="form-select">
                <option value="nghỉ phép năm">Nghỉ phép năm</option>
                <option value="nghỉ không lương">Nghỉ không lương</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Từ ngày</label>
            <input type="date" name="start_date" class="form-control" required
                value="<?= date('Y-m-d') ?>">
        </div>
        <div class="mb-3">
            <label>Đến ngày</label>
            <input type="date" name="end_date" class="form-control" required
                value="<?= date('Y-m-d', strtotime('+1 day')) ?>">
        </div>
        <div class="mb-3">
            <label>Lý do</label>
            <select name="reason_type" class="form-select">
                <option value="bệnh">Bệnh</option>
                <option value="chăm con nhỏ">Chăm con nhỏ</option>
                <option value="về quê">Về quê</option>
                <option value="khác">Khác</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Mô tả</label>
            <textarea name="description" class="form-control"></textarea>
        </div>

        <!-- Details JSON input -->
        <div class="mb-3">
            <label>Chi tiết nghỉ (sáng/chiều từng ngày)</label>
            <textarea name="details_json" class="form-control" rows="20"
                placeholder='{"<?= date('Y-m-d') ?>":{"Sáng":"x","Chiều":""}}'></textarea>
            <small class="text-muted">Nhập JSON, ví dụ: {"<?= date('Y-m-d') ?>":{"Sáng":"x","Chiều":""}, "<?= date('Y-m-d',  strtotime('+1 day')) ?>":{"Sáng":"x","Chiều":""}}</small>
        </div>

        <button type="submit" class="btn btn-primary">Gửi đơn</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const startDateInput = document.querySelector('input[name="start_date"]');
        const endDateInput = document.querySelector('input[name="end_date"]');
        const detailsTextarea = document.querySelector('textarea[name="details_json"]');

        function generateDetailsJson() {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);

            if (isNaN(startDate) || isNaN(endDate) || startDate > endDate) {
                detailsTextarea.value = '';
                return;
            }

            const details = {};
            let currentDate = new Date(startDate);

            while (currentDate <= endDate) {
                const yyyy = currentDate.getFullYear();
                const mm = String(currentDate.getMonth() + 1).padStart(2, '0');
                const dd = String(currentDate.getDate()).padStart(2, '0');
                const formattedDate = `${yyyy}-${mm}-${dd}`;

                details[formattedDate] = {
                    "Sáng": "x",
                    "Chiều": "x"
                };

                currentDate.setDate(currentDate.getDate() + 1);
            }

            detailsTextarea.value = JSON.stringify(details, null, 2);
        }

        startDateInput.addEventListener('change', generateDetailsJson);
        endDateInput.addEventListener('change', generateDetailsJson);

        // Trigger on initial load
        generateDetailsJson();
    });
</script>


<?php require __DIR__ . '/../layout/footer.php'; ?>