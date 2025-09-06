<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="container mt-4">
    <h3>Cập nhật hồ sơ cá nhân</h3>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'];
                                        unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'];
                                            unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php?controller=employee&action=update_profile" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Họ tên</label>
            <input name="fullname" class="form-control" value="<?= htmlspecialchars($user['fullname']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Số điện thoại</label>
            <input name="phone" class="form-control"
                value="<?= htmlspecialchars($user['phone']) ?>"
                required
                pattern="^(03|05|07|08|09)\d{8}$"
                title="Số điện thoại phải bắt đầu bằng 03, 05, 07, 08 hoặc 09 và có 10 chữ số.">
        </div>

        <div class="mb-3">
            <label class="form-label">Ảnh đại diện</label><br>
            <?php if (!empty($user['avatar'])): ?>
                <img src="<?= $user['avatar'] ?>" alt="avatar" class="img-thumbnail mb-2" style="max-width:150px;">
            <?php endif; ?>
            <input type="file" name="avatar" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Lịch sử làm việc (JSON)</label>
            <textarea name="work_history" class="form-control" rows="5"><?= htmlspecialchars($user['work_history'] ?? '') ?></textarea>
            <small class="text-muted">Ví dụ: [{"company":"ABC","role":"Dev","years":2}]</small>
        </div>

        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
    </form>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>