<?php include("views/partials/header.php"); ?>

<div class="container my-4">
    <?php if (!empty($_SESSION['user'])): ?>
        <div class="alert alert-success">
            Welcome back, <strong><?= htmlspecialchars($_SESSION['user']['username']) ?></strong>!
        </div>
    <?php endif; ?>

    <h2 class="mb-4">Latest Papers by Topic</h2>

    <?php foreach ($topics as $topic): ?>
        <div class="mb-4">
            <h4 class="text-warning"><?= htmlspecialchars($topic['topic_name']) ?></h4>
            <ul class="list-group">
                <?php
                $papers = $papers_by_topic[$topic['topic_id']] ?? [];
                if (empty($papers)):
                ?>
                    <li class="list-group-item">No papers available.</li>
                <?php else: ?>
                    <?php foreach ($papers as $paper): ?>
                        <li class="list-group-item">
                            <strong><?= makeLinkPaper($paper) ?></strong><br>
                            <small>Authors: <?= implode(', ', linkAuthors($paper, $mysqli)) ?></small><br>
                            <small>Conference: <?= htmlspecialchars($paper['conference_name']) ?></small><br>
                            <small>Period: <?= formatDateRange($paper['start_date'], $paper['end_date']) ?></small>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    <?php endforeach; ?>
</div>

<?php include("views/partials/footer.php"); ?>