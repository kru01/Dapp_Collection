<?php include("views/partials/header.php"); ?>

<div class="container my-4" style="max-width: 400px;">
    <h3 class="mb-3">Login</h3>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="index.php?controller=auth&action=login">
        <div class="mb-3">
            <label>Username</label>
            <input name="username" value="johnsmith" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" value="password123" name="password" class="form-control" required>
        </div>
        <button class="btn btn-primary w-100">Login</button>
    </form>
</div>

<?php include("views/partials/footer.php"); ?>