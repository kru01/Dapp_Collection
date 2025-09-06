<?php require __DIR__ . '/../layout/header.php'; ?>

<h2>Xin chào, <?= htmlspecialchars($employee['fullname']) ?></h2>

<div class="row mt-4">
    <div class="col-md-4">
        <div class="card shadow-sm bg-dark text-white">
            <div class="card-body text-center">
                <?php if (!empty($employee['image_path'])): ?>
                    <!--
                    Append the file's last modified time to force browser reloads
                        when the file is replaced, i.e., `v` changes.
                    E.g., /uploads/123_profile.jpg?v=1720710289
                    -->
                    <img src="<?= BASE_URL . htmlspecialchars($employee['image_path']) ?>?v=<?= filemtime($employee['image_path']) ?>"
                        class="img-thumbnail mb-2"
                        style="width:120px;height:120px;">
                <?php else: ?>
                    <img src="https://via.placeholder.com/120" class="img-thumbnail mb-2">
                <?php endif; ?>
                <h5><?= htmlspecialchars($employee['fullname']) ?></h5>
                <p><?= htmlspecialchars($employee['position']) ?> - <?= htmlspecialchars($employee['department']) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm mb-3 border-primary">
            <div class="card-body">
                <h5 class="text-primary">Thông tin cá nhân</h5>
                <ul>
                    <li>Mã nhân viên: <?= $employee['employee_id'] ?></li>
                    <li>Số điện thoại: <?= htmlspecialchars($employee['phone']) ?></li>
                </ul>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <h5>Lịch sử làm việc</h5>
                <?php
                $history = json_decode($employee['work_history'], true);
                if ($history) {
                    echo "<ul>";
                    foreach ($history as $h) {
                        echo "<li>{$h['start_date']} - " .
                            ($h['end_date'] ?: 'Hiện tại') .
                            " : {$h['position']}</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>Chưa có dữ liệu</p>";
                }
                ?>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Overview Stats -->
            <div class="col-md-4">
                <div class="card shadow-sm text-center p-3 bg-warning">
                    <h6>Số ngày phép đã nghỉ trong năm</h6>
                    <h3><?= $leave_days ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm text-center p-3 bg-success text-light">
                    <h6>Số thiết bị đang mượn</h6>
                    <h3><?= $devices_borrowed ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm text-center p-3 bg-info">
                    <h6>Số yêu cầu đang chờ duyệt</h6>
                    <h3><?= $pending_requests ?></h3>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Leave Requests -->
            <div class="col-md-6">
                <div class="card shadow-sm border-warning">
                    <div class="card-body">
                        <h5 class="text-warning">Đơn nghỉ phép</h5>
                        <?php if (!empty($leaves)): ?>
                            <ul class="list-group">
                                <?php foreach ($leaves as $lv): ?>
                                    <li class="list-group-item">
                                        <?= htmlspecialchars($lv['leave_type']) ?>:
                                        <?= $lv['start_date'] ?> → <?= $lv['end_date'] ?>
                                        (<?= $lv['status'] ?>)
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>Chưa có đơn nghỉ phép.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Borrowed Devices -->
            <div class="col-md-6">
                <div class="card shadow-sm border-success">
                    <div class="card-body">
                        <h5 class="text-success">Thiết bị đang mượn</h5>
                        <?php if (!empty($borrows)): ?>
                            <ul class="list-group">
                                <?php foreach ($borrows as $br): ?>
                                    <li class="list-group-item">
                                        <?= $br['borrow_date'] ?> :
                                        <?= htmlspecialchars($br['devices']) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>Không có thiết bị nào đang mượn.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>