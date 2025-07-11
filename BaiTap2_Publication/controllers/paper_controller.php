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
        $stmt->bind_param("s", $paper_id);
        $stmt->execute();

        $paper = $stmt->get_result()->fetch_assoc();
        if (!$paper) {
            header('Location: index.php?controller=home&action=index');
            exit;
        }

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
}
