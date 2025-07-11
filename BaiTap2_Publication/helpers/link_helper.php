<?php
function makeLinkAuthor($author)
{
    $name = htmlspecialchars($author['full_name']);
    $url = "index.php?controller=author&action=profile&id=" . $author['author_id'];
    return "<a href=\"$url\"
            class=\"link-secondary link-offset-2 link-underline-opacity-25
                link-underline-opacity-100-hover\"
            >$name</a>";
}

function linkAuthors($paper, $mysqli)
{
    $paper_id = $paper['paper_id'];

    $sql = "SELECT PAR.author_id, AU.full_name
            FROM PARTICIPATION PAR
                JOIN AUTHORS AU ON PAR.author_id = AU.user_id
            WHERE PAR.paper_id = {$paper_id}
            ORDER BY PAR.role = 'first_author' DESC, PAR.date_added ASC";

    $res = $mysqli->query($sql);

    $linked_authors = [];

    while ($author = $res->fetch_assoc()) {
        $linked_authors[] = makeLinkAuthor($author);
    }

    return $linked_authors;
}

function makeLinkPaper($paper)
{
    $title = htmlspecialchars($paper['title']);
    $url = "index.php?controller=paper&action=detail&id=" . $paper['paper_id'];
    return "<a href=\"$url\"
        class=\"link-underline link-underline-opacity-0\"
        >$title</a>";
}
