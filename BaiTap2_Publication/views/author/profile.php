<?php include('views/partials/header.php'); ?>

<div class="container my-4">
    <h3>Personal Profile</h3>
    <div class="row mt-3">
        <div class="col-md-4">
            <img src="<?= $author['image_path'] ?>" class="img-fluid border" alt="Profile Image">
        </div>
        <div class="col-md-8">
            <p><strong>Name:</strong> <?= htmlspecialchars($author['full_name']) ?></p>
            <p><strong>Website:</strong> <a href="<?= htmlspecialchars($author['website']) ?>" target="_blank"><?= htmlspecialchars($author['website']) ?></a></p>
            <p><strong>Bio:</strong> <?= htmlspecialchars($profile['bio'] ?? '') ?></p>
            <p><strong>Interests:</strong> <?= htmlspecialchars(implode(', ', $profile['interests'] ?? [])) ?></p>

            <?php if (!empty($is_owner) && $is_owner): ?>
                <a class="btn btn-primary" href="index.php?controller=author&action=edit">Edit Profile</a>
            <?php endif; ?>
        </div>
    </div>

    <h4 class="mt-5">Authored Papers</h4>
    <ul class="list-group">
        <?php foreach ($papers as $p): ?>
            <li class="list-group-item">
                <strong><?= htmlspecialchars($p['title']) ?></strong><br>
                <small>Authors: <?= htmlspecialchars($p['author_string_list']) ?></small><br>
                <small>Conference: <?= htmlspecialchars($p['conference_name']) ?></small><br>
                <small>Participation Period:
                    <?= htmlspecialchars(
                        formatDateRange($p['date_added'], $p['end_date'])
                    ) ?></small>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php include('views/partials/footer.php'); ?>