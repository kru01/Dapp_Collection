<?php include("views/partials/header.php"); ?>

<div class="container my-4" style="max-width: 600px;">
    <h3>Edit Profile</h3>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Full Name</label>
            <input name="full_name" class="form-control" value="<?= htmlspecialchars($author['full_name']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Website</label>
            <input name="website" class="form-control" value="<?= htmlspecialchars($author['website']) ?>">
        </div>
        <div class="mb-3">
            <label>Profile JSON</label>
            <textarea name="profile_json_text" class="form-control" rows="5" required><?= htmlspecialchars($author['profile_json_text']) ?></textarea>
            <small class="text-danger">Must include `bio` and `interests` keys</small>
        </div>
        <div class="mb-3">
            <label>Change Profile Image</label>
            <input type="file" name="image" class="form-control">
        </div>
        <button class="btn btn-success">Save Changes</button>
    </form>
</div>

<?php include("views/partials/footer.php"); ?>