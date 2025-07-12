<?php
class PaperController
{
    function detail()
    {
        require_once("config/config.inc.php");

        $paper_id = isset($_GET['id']) ? $_GET['id'] : null;

        $condition = $paper_id !== null
            ? " WHERE PA.paper_id = ?"
            : " ORDER BY PA.paper_id DESC LIMIT 1";

        $stmt = $mysqli->prepare(
            "SELECT PA.*, CO.name AS conf_name, CO.abbreviation AS conf_abbrev,
                CO.rank AS conf_rank, CO.start_date, CO.end_date, CO.type AS conf_type,
                TP.topic_name
            FROM PAPERS PA
                JOIN CONFERENCES CO ON PA.conference_id = CO.conference_id
                JOIN TOPICS TP ON PA.topic_id = TP.topic_id"
                . $condition
        );

        if ($paper_id !== null) {
            $stmt->bind_param("s", $paper_id);
        }

        $stmt->execute();

        $paper = $stmt->get_result()->fetch_assoc();
        if (!$paper) {
            header('Location: index.php?controller=home&action=index');
            exit;
        }

        $is_latest = $paper_id === null;
        // Reset $paper_id if case we did get the latest paper
        $paper_id = $paper['paper_id'];

        $sql = "SELECT PAR.*, AU.full_name, AU.website AS author_site, AU.image_path AS author_image
            FROM PARTICIPATION PAR
                JOIN AUTHORS AU ON PAR.author_id = AU.user_id
            WHERE PAR.paper_id = {$paper_id}
            ORDER BY PAR.role = 'first_author' DESC, PAR.date_added ASC";

        $authors = [];
        $res = $mysqli->query($sql);

        while ($row = $res->fetch_assoc()) {
            $authors[] = $row;
        }

        $already_joined = false;
        $is_admin = false;

        if (!empty($_SESSION['user'])) {
            $uid = $_SESSION['user']['user_id'];

            $check = $mysqli->query(
                "SELECT * FROM PARTICIPATION
                WHERE paper_id = {$paper_id} AND author_id = {$uid}"
            );

            $already_joined = ($check->num_rows > 0);
            $is_admin = $_SESSION['user']['user_type'] === 'admin';
        }

        require_once("helpers/link_helper.php");
        require_once("helpers/date_helper.php");

        require("views/paper/detail.php");
    }

    function joinPaper()
    {
        require_once('config/config.inc.php');

        if (empty($_SESSION['user'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        $paper_id = $_GET['id'];
        $user_id = $_SESSION['user']['user_id'];

        $check = $mysqli->prepare("SELECT * FROM PARTICIPATION
            WHERE author_id=? AND paper_id=?");

        $check->bind_param('ss', $user_id, $paper_id);
        $check->execute();

        if ($check->get_result()->num_rows == 0) {
            $stmt = $mysqli->prepare(
                "INSERT INTO PARTICIPATION (author_id, paper_id, `role`, date_added, `status`)
                VALUES (?, ?, 'member', NOW(), 'show')"
            );

            $stmt->bind_param('ss', $user_id, $paper_id);
            $stmt->execute();
        }

        header("Location: index.php?controller=paper&action=detail&id={$paper_id}");
        exit();
    }

    function removeAuthor()
    {
        require_once('config/config.inc.php');

        if (empty($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'admin') {
            echo "Not allowed.";
            return;
        }

        $paper_id = $_GET['paper_id'];
        $author_id = $_GET['author_id'];

        $stmt = $mysqli->prepare("DELETE FROM PARTICIPATION
            WHERE paper_id=? AND author_id=?");

        $stmt->bind_param('ss', $paper_id, $author_id);
        $stmt->execute();

        header("Location: index.php?controller=paper&action=detail&id={$paper_id}");
        exit();
    }

    function search()
    {
        require_once("config/config.inc.php");

        $topics = [];
        $res = $mysqli->query("SELECT * FROM TOPICS");

        while ($row = $res->fetch_assoc()) {
            $topics[] = $row;
        }

        require("views/paper/search.php");
    }

    function ajax_search()
    {
        require_once("config/config.inc.php");

        $keyword = $_GET['keyword'] ?? '';
        $author = $_GET['author'] ?? '';

        $conf = $_GET['conference'] ?? '';
        $topic = $_GET['topic'] ?? '';

        $start_date = $_GET['start_date'] ?? '';
        $end_date = $_GET['end_date'] ?? '';

        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 5;
        $offset = ($page - 1) * $limit;

        $conditions = [];
        $params = [];
        $types = '';

        if ($keyword) {
            $conditions[] = "PA.title LIKE ?";
            $params[] = '%' . $keyword . '%';
            $types .= 's';
        }

        if ($author) {
            $conditions[] = "PA.author_string_list LIKE ?";
            $params[] = '%' . $author . '%';
            $types .= 's';
        }

        if ($conf) {
            $conditions[] = '(CO.name LIKE ? OR CO.abbreviation LIKE ?)';
            $params[] = '%' . $conf . '%';
            $params[] = '%' . $conf . '%';
            $types .= 'ss';
        }

        if ($topic) {
            $conditions[] = 'PA.topic_id = ?';
            $params[] = intval($topic);
            $types .= 'i';
        }

        if ($start_date && !$end_date) {
            $conditions[] = 'CO.start_date >= ?';
            $params[] = $start_date;
            $types .= 's';
        } else if (!$start_date && $end_date) {
            $conditions[] = 'CO.end_date <= ?';
            $params[] = $end_date;
            $types .= 's';
        } else if ($start_date && $end_date) {
            $conditions[] = '(CO.start_date >= ? AND CO.end_date <= ?)';
            $params[] = $start_date;
            $params[] = $end_date;
            $types .= 'ss';
        }

        $where = implode(' AND ', $conditions);
        if (!$where) $where = '1=1';

        // Count total
        $countRes = $mysqli->prepare("SELECT COUNT(*) AS total
            FROM PAPERS PA JOIN CONFERENCES CO ON PA.conference_id = CO.conference_id
            WHERE {$where}");

        if ($types) $countRes->bind_param($types, ...$params);
        $countRes->execute();

        $total = $countRes->get_result()->fetch_assoc()['total'];
        $pages = ceil($total / $limit);

        // Get papers
        $stmt = $mysqli->prepare(
            "SELECT PA.paper_id, PA.title, CO.conference_id,
                CO.name AS conference_name, CO.abbreviation AS conf_abbrev,
                CO.start_date, CO.end_date,
                TP.topic_name
            FROM PAPERS PA
                JOIN CONFERENCES CO ON PA.conference_id = CO.conference_id
                JOIN TOPICS TP ON PA.topic_id = TP.topic_id
            WHERE {$where}
            ORDER BY PA.paper_id DESC
            LIMIT ? OFFSET ?"
        );

        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';

        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        $papers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Output HTML
        require_once('helpers/link_helper.php');
        require_once('helpers/date_helper.php');

        echo "<nav class='d-flex justify-content-center'><ul class='pagination'>";
        for ($i = 1; $i <= $pages; $i++) {
            echo "<li class='page-item'>
                <a class='page-link' href='#' onclick='handleForm.loadPage($i); return false;'>$i</a>
                </li>";
        }
        echo "</ul></nav>";

        foreach ($papers as $p) {
            echo "<div class='border rounded p-2 mb-2'>";
            echo "<h5>" . makeLinkPaper($p)
                . "<span class='h6'><span class='text-secondary ms-3'>#</span>{$p['paper_id']}</span>
                </h5>";

            echo "<p><strong>Authors:</strong> " . implode(', ', linkAuthors($p, $mysqli)) . "</p>";

            echo "<p><strong>Conference:</strong> " . htmlspecialchars($p['conf_abbrev']) . ' – '
                . "<span class='fst-italic'>" . htmlspecialchars($p['conference_name']) . "</span>"
                . " | <strong>Topic:</strong> " . htmlspecialchars($p['topic_name']) . "</p>";

            echo "<p><strong>Period:</strong> " . formatDateRange($p['start_date'], $p['end_date']) . "</p>";
            echo "</div>";
        }

        echo "<nav class='d-flex justify-content-center'><ul class='pagination'>";
        for ($i = 1; $i <= $pages; $i++) {
            echo "<li class='page-item'>
                <a class='page-link' href='#' onclick='handleForm.loadPage($i); return false;'>$i</a>
                </li>";
        }
        echo "</ul></nav>";
    }
}
