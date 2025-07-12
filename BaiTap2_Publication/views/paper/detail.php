<?php include("views/partials/header.php"); ?>

<div class="container my-4">
    <h3><?= htmlspecialchars($paper['title']) ?></h3>
    <div>
        <span class="h6">
            <span class="text-secondary">Paper#</span><span><?= $paper['paper_id'] ?></span>
        </span>
        <?php if ($is_latest): ?>
            <span class="badge text-bg-success ms-2">Latest</span>
        <?php endif ?>
    </div>

    <dl class="row mt-4">
        <dt class="col-sm-3 text-end">Topic:</dt>
        <dd class="col-sm-9"><?= htmlspecialchars($paper['topic_name']) ?></dd>
        <dt class="col-sm-3 text-end">Conference:</dt>
        <dd class="col-sm-9">
            <p class="mb-1">
                <span><?= htmlspecialchars($paper['conf_abbrev']) . ' – ' ?></span>
                <span class="fst-italic"><?= htmlspecialchars($paper['conf_name']) ?></span>
            </p>
            <p class="mb-0 text-secondary">
                <span><?= htmlspecialchars($paper['conf_type']) . ' – ' ?></span>
                <span>Rank <?= htmlspecialchars($paper['conf_rank']) ?></span>
            </p>
        </dd>
        <dt class="col-sm-3 text-end">Period:</dt>
        <dd class="col-sm-9"><?= formatDateRange($paper['date_added'], $paper['end_date']) ?></dd>
    </dl>

    <p class="lead text-break overflow-auto">
        <strong>Abstract:</strong><br><?= nl2br(htmlspecialchars($paper['abstract'])) ?>
    </p>

    <h5 class="mt-4">Authors</h5>
    <ul class="list-group">
        <?php foreach ($authors as $a): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <dl class="w-100 row mb-0">
                    <dt class="col-sm-2 text-end">
                        <span class="badge
                        <?= $a['role'] !== 'member' ? 'text-bg-primary' : 'text-bg-secondary' ?>
                        ">
                            <?= htmlspecialchars($a['role']) ?>
                        </span>
                    </dt>

                    <dd class="col-sm-10 mb-0 d-flex align-items-center">
                        <a class="d-flex align-items-center gap-2 link-offset-2 link-offset-3-hover
                            link-underline link-underline-opacity-0 link-underline-opacity-75-hover w-50 text-break"
                            href="index.php?controller=author&action=profile&id=<?= $a['author_id'] ?>">
                            <img src="<?= $a['author_image'] ?>?v=<?= filemtime($a['author_image']) ?>"
                                class="rounded-circle object-fit-contain" alt="Profile Image"
                                style="width: 32px; height: 32px;">

                            <span><?= htmlspecialchars($a['full_name']) ?></span>
                        </a>

                        <a href="<?= htmlspecialchars($a['author_site']) ?>" target="_blank"
                            class="link-underline link-underline-opacity-0 link-secondary fst-italic flex-grow-1">
                            – <?= htmlspecialchars($a['author_site']) ?></a>
                    </dd>
                </dl>

                <?php if (!empty($is_admin)): ?>
                    <a href="index.php?controller=paper&action=removeAuthor&paper_id=<?= $paper['paper_id'] ?>&author_id=<?= $a['author_id'] ?>"
                        onclick="return confirm('Remove this author?')" class="btn btn-sm btn-danger">Remove</a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if (!empty($_SESSION['user']) && !$already_joined): ?>
        <a href="index.php?controller=paper&action=joinPaper&id=<?= $paper['paper_id'] ?>" class="btn btn-success mt-3">Join as Member</a>
    <?php endif; ?>
</div>

<?php include("views/partials/footer.php"); ?>