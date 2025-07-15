<?php
render_pagination_bar($page, $total_pages, 'handlePaper.loadAuthorPapers');
?>

<ul id='paperList' class="list-group">
    <?php foreach ($papers as $p): ?>
        <li class="list-group-item">
            <strong><?= makeLinkPaper($p) ?>
                <small class="ms-2">
                    <span class="text-secondary">
                        #</span><?= htmlspecialchars($p['paper_id']) ?>
                </small>
            </strong><br>

            <small>Authors: <?= implode(', ', linkAuthors($p, $mysqli)) ?></small><br>
            <small>Conference: <?= htmlspecialchars($p['conference_name'])
                                ?> | Topic: <?= htmlspecialchars($p['topic_name']) ?></small><br>
            <small>Participation Period: <?= formatDateRange($p['date_added'], $p['end_date']) ?></small>
        </li>
    <?php endforeach; ?>
</ul>