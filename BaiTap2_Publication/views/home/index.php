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
            <h4 class="text-primary"><?= htmlspecialchars($topic['topic_name']) ?></h4>
            <ul class="list-group">
                <?php
                $papers = $papers_by_topic[$topic['topic_id']] ?? [];
                if (empty($papers)):
                ?>
                    <li class="list-group-item">No papers available.</li>
                <?php else: ?>
                    <?php foreach ($papers as $paper): ?>
                        <li class="list-group-item">
                            <strong><?= htmlspecialchars($paper['title']) ?></strong><br>

                            <?php
                            $author_names = explode(',', $paper['author_string_list']);
                            $linked_authors = [];

                            $sql = "SELECT author_id FROM PARTICIPATION WHERE paper_id = {$paper['paper_id']}";
                            $res = $mysqli->query($sql);

                            foreach ($author_names as $name) {
                                $name = trim($name);
                                $escaped = htmlspecialchars($name);

                                if ($row = $res->fetch_assoc()) {
                                    $url = "index.php?controller=author&action=profile&id=" . $row['author_id'];
                                    $linked_authors[] = "<a href=\"$url\">$escaped</a>";
                                } else {
                                    $linked_authors[] = $escaped;
                                }
                            }
                            ?>
                            <small>Authors: <?= implode(', ', $linked_authors) ?></small><br>

                            <small>Conference: <?= htmlspecialchars($paper['conference_name']) ?></small><br>
                            <small>Period: <?= htmlspecialchars(
                                                formatDateRange($paper['start_date'], $paper['end_date'])
                                            ) ?></small>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    <?php endforeach; ?>
</div>

<?php include("views/partials/footer.php"); ?>