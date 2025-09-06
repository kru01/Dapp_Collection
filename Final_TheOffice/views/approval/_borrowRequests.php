<h5>Phiếu mượn thiết bị</h5>
<?php if ($borrowRequests): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID phiếu</th>
                <th>Nhân viên</th>
                <th>Ngày mượn</th>
                <th>Trạng thái</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($borrowRequests as $b): ?>
                <tr>
                    <td><?= $b['borrow_id'] ?></td>
                    <td><?= htmlspecialchars($b['fullname']) ?></td>
                    <td><?= $b['borrow_date'] ?></td>
                    <td>
                        <span class="badge <?= match ($b['status']) {
                                                'approved' => 'bg-success',
                                                'rejected' => 'bg-danger',
                                                'returned' => 'bg-secondary',
                                                'pending'  => 'bg-warning text-dark',
                                                default    => 'bg-light text-muted'
                                            } ?>">
                            <?= ucfirst($b['status']) ?>
                        </span>
                    </td>
                    <td><a href="index.php?controller=approval&action=detailBorrow&id=<?= $b['borrow_id'] ?>" class="btn btn-sm btn-info">Xem</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Không có yêu cầu mượn thiết bị nào.</p>
<?php endif; ?>

<?php
render_pagination_bar($page, $total_pages, 'loadDeviceBorrowRequests');
?>