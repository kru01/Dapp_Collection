<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="container mt-4">
    <h3>Đăng ký mượn thiết bị</h3>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'];
                                        unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'];
                                            unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <!-- Search bar -->
    <div class="mb-3">
        <input type="text" id="search" class="form-control" placeholder="Tìm thiết bị...">
        <div id="results" class="list-group mt-1"></div>
    </div>

    <!-- Borrow form -->
    <form method="POST" action="index.php?controller=deviceBorrow&action=store">
        <h5>Danh sách mượn (tối đa 2 thiết bị)</h5>
        <table class="table table-bordered" id="borrowList">
            <thead>
                <tr>
                    <th>Tên thiết bị</th>
                    <th>Ghi chú</th>
                    <th></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <div class="mb-3">
            <label>Ngày Hẹn Trả</label>
            <input type="date" name="expected_return_date" class="form-control" required
                value="<?= date('Y-m-d', strtotime('+1 day')) ?>">
        </div>

        <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
    </form>
</div>

<script>
    // AJAX search
    document.getElementById('search').addEventListener('keyup', function() {
        let term = this.value;
        if (term.length < 2) {
            document.getElementById('results').innerHTML = '';
            return;
        }
        fetch('index.php?controller=deviceBorrow&action=search&term=' + encodeURIComponent(term))
            .then(res => res.json())
            .then(data => {
                let html = '';
                data.forEach(d => {
                    html += `<button type="button" class="list-group-item list-group-item-action"
                          onclick="addDevice(${d.device_id}, '${d.device_name}', ${d.quantity})">+ ${d.device_name}</button>`;
                });
                document.getElementById('results').innerHTML = html;
            });
    });

    // Add device to list
    function addDevice(id, name, quantity) {
        if (quantity <= 0) {
            alert("Thiết bị này đã bị mượn hết.");
            return;
        }

        let tbody = document.querySelector('#borrowList tbody');
        if (tbody.rows.length >= 2) {
            alert("Chỉ được chọn tối đa 2 thiết bị.");
            return;
        }
        // Prevent duplicates
        for (let row of tbody.rows) {
            if (row.dataset.deviceId == id) {
                alert("Thiết bị này đã có trong danh sách.");
                return;
            }
        }

        let row = tbody.insertRow();
        row.dataset.deviceId = id;
        row.innerHTML = `
        <td>${name}<input type="hidden" name="devices[]" value="${id}"></td>
        <td><input type="text" name="notes[]" class="form-control" value="Mượn ${name}"></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">X</button></td>
    `;
    }

    // Check valid expected_returned_date
    document.addEventListener('DOMContentLoaded', function() {
        const returnDateInput = document.querySelector('input[name="expected_return_date"]');

        function checkReturnDate() {
            const selectedDate = new Date(returnDateInput.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Normalize to midnight

            if (selectedDate < today) {
                alert("Ngày hẹn trả không được nhỏ hơn hôm nay. Đã đặt lại là ngày mai.");
                const tomorrow = new Date();
                tomorrow.setDate(today.getDate() + 1);
                const yyyy = tomorrow.getFullYear();
                const mm = String(tomorrow.getMonth() + 1).padStart(2, '0');
                const dd = String(tomorrow.getDate()).padStart(2, '0');
                returnDateInput.value = `${yyyy}-${mm}-${dd}`;
            }
        }

        returnDateInput.addEventListener('change', checkReturnDate);
    });
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>