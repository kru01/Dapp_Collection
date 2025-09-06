<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="container mt-4">
    <h3>Chi tiết đơn nghỉ phép #<?php echo $id; ?></h3>

    <div class="card mb-3">
        <div class="card-body">
            <h5>Thông tin nhân viên</h5>
            <p><strong>Họ tên:</strong> <?= htmlspecialchars($leave['fullname']) ?></p>
            <p><strong>Phòng ban:</strong> <?= htmlspecialchars($leave['department']) ?></p>
            <p><strong>Chức vụ:</strong> <?= htmlspecialchars($leave['position']) ?></p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5>Chi tiết yêu cầu nghỉ</h5>
            <ul>
                <li><strong>Loại nghỉ:</strong> <?= htmlspecialchars($leave['leave_type']) ?></li>
                <li><strong>Từ ngày:</strong> <?= $leave['start_date'] ?></li>
                <li><strong>Đến ngày:</strong> <?= $leave['end_date'] ?></li>
                <li><strong>Nghỉ buổi:</strong>
                    <?php
                    $half = json_decode($leave['half_day'], true);
                    echo $half ? implode(", ", $half) : "Cả ngày";
                    ?>
                </li>
                <li><strong>Lý do:</strong> <?= htmlspecialchars($leave['reason']) ?></li>
                <li><strong>Mô tả:</strong> <?= nl2br(htmlspecialchars($leave['description'])) ?></li>
                <li><strong>Trạng thái hiện tại:</strong> <?= htmlspecialchars($leave['status']) ?></li>
            </ul>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5>Lịch sử nghỉ phép của nhân viên</h5>
            <?php if ($history): ?>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Từ ngày</th>
                            <th>Đến ngày</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $h): ?>
                            <tr>
                                <td><?= $h['start_date'] ?></td>
                                <td><?= $h['end_date'] ?></td>
                                <td><?= $h['status'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Chưa có lịch sử nghỉ phép.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($leave['status'] == 'pending'): ?>
        <form method="POST" action="index.php?controller=approval&action=updateLeaveStatus">
            <input type="hidden" name="leave_id" value="<?= $leave['leave_id'] ?>">
            <button name="status" value="approved" class="btn btn-success">Phê duyệt</button>
            <button name="status" value="rejected" class="btn btn-danger">Từ chối</button>
        </form>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>