<h5 class="mt-4">Đơn nghỉ phép</h5>
<?php if (!empty($leaveRequests)): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID đơn</th>
                <th>Nhân viên</th>
                <th>Từ ngày</th>
                <th>Đến ngày</th>
                <th>Trạng thái</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($leaveRequests as $l): ?>
                <tr>
                    <td><?= $l['leave_id'] ?></td>
                    <td><?= htmlspecialchars($l['fullname']) ?></td>
                    <td><?= $l['start_date'] ?></td>
                    <td><?= $l['end_date'] ?></td>
                    <td>
                        <span class="badge <?= match ($l['status']) {
                                                'approved' => 'bg-success',
                                                'rejected' => 'bg-danger',
                                                'returned' => 'bg-secondary',
                                                'pending'  => 'bg-warning text-dark',
                                                default    => 'bg-light text-muted'
                                            } ?>">
                            <?= ucfirst($l['status']) ?>
                        </span>
                    </td>
                    <td><a href="index.php?controller=approval&action=detailLeave&id=<?= $l['leave_id'] ?>" class="btn btn-sm btn-info">Xem</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Không có đơn nghỉ phép nào.</p>
<?php endif; ?>

<?php
render_pagination_bar($page, $total_pages, 'loadLeaveRequests');
?>