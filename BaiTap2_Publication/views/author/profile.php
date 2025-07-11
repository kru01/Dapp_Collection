<?php include('views/partials/header.php'); ?>

<div class="container my-4">
    <h3>Personal Profile</h3>
    <div class="row mt-3">
        <div class="col-md-4">
            <!--
            Append the file's last modified time to force browser reloads
                when the file is replaced, i.e., `v` changes.
            E.g., /uploads/123_profile.jpg?v=1720710289
            -->
            <img src="<?= $author['image_path'] ?>?v=<?= filemtime($author['image_path']) ?>" class="img-fluid border" alt="Profile Image">
        </div>
        <div class="col-md-8">
            <p><strong>Name:</strong> <?= htmlspecialchars($author['full_name']) ?></p>

            <p><strong>Website:</strong> <a href="<?= htmlspecialchars($author['website']) ?>" target="_blank">
                    <?= htmlspecialchars($author['website']) ?></a></p>

            <dl class="row">
                <dt class="col-sm-2">
                    <p>Interests:</p>
                </dt>
                <dd class="col-sm-10 text-break"><?= htmlspecialchars(implode(', ', $profile['interests'] ?? [])) ?></dd>

                <dt class="col-sm-2">Bio:</dt>
                <dd class="col-sm-10 p-2 border rounded bg-info bg-opacity-10 overflow-auto text-break shadow-sm"
                    style="max-height: 300px;">
                    <?= nl2br(htmlspecialchars($profile['bio'] ?? '')) ?>
                </dd>
            </dl>

            <?php if (!empty($is_owner) && $is_owner): ?>
                <a class="btn btn-primary" href="index.php?controller=author&action=edit">Edit Profile</a>
            <?php endif; ?>
        </div>
    </div>

    <h4 class="mt-5">Authored Papers</h4>
    <ul class="list-group">
        <?php foreach ($papers as $p): ?>
            <li class="list-group-item">
                <strong><?= makeLinkPaper($p) ?></strong><br>
                <small>Authors: <?= implode(', ', linkAuthors($p, $mysqli)) ?></small><br>
                <small>Conference: <?= htmlspecialchars($p['conference_name']) ?></small><br>
                <small>Participation Period: <?= formatDateRange($p['date_added'], $p['end_date']) ?></small>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php include('views/partials/footer.php'); ?>