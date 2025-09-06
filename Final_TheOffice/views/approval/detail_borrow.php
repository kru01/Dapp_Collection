<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="container mt-4">
    <h3>Chi tiết phiếu mượn thiết bị #<?php echo $id; ?></h3>

    <div class="card mb-3">
        <div class="card-body">
            <h5>Thông tin nhân viên</h5>
            <p><strong>Họ tên:</strong> <?= htmlspecialchars($borrow['fullname']) ?></p>
            <p><strong>Phòng ban:</strong> <?= htmlspecialchars($borrow['department']) ?></p>
            <p><strong>Chức vụ:</strong> <?= htmlspecialchars($borrow['position']) ?></p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5>Thông tin mượn</h5>
            <ul>
                <li><strong>Ngày mượn:</strong> <?= $borrow['borrow_date'] ?></li>
                <li><strong>Ngày trả dự kiến:</strong> <?= $borrow['expected_return_date'] ?></li>
                <li><strong>Ngày trả thực tế:</strong> <?= $borrow['return_date'] ?: 'Chưa trả' ?></li>
                <li><strong>Trạng thái:</strong> <?= htmlspecialchars($borrow['status']) ?></li>
            </ul>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5>Danh sách thiết bị</h5>
            <?php if ($devices): ?>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Tên thiết bị</th>
                            <th>Mô tả</th>
                            <th>Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($devices as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars($d['device_name']) ?></td>
                                <td><?= htmlspecialchars($d['description']) ?></td>
                                <td><?= htmlspecialchars($d['note']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Không có thiết bị nào trong phiếu.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($borrow['status'] == 'pending' || $borrow['status'] == 'approved'): ?>
        <form method="POST" action="index.php?controller=approval&action=updateBorrowStatus">
            <input type="hidden" name="borrow_id" value="<?= $borrow['borrow_id'] ?>">
            <?php if ($borrow['status'] == 'pending'): ?>
                <button name="status" value="approved" class="btn btn-success">Phê duyệt</button>
                <button name="status" value="rejected" class="btn btn-danger">Từ chối</button>
            <?php elseif ($borrow['status'] == 'approved'): ?>
                <button name="status" value="returned" class="btn btn-primary">Đánh dấu đã trả</button>
            <?php endif; ?>
        </form>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>