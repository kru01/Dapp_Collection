<?php
class AuthorController
{
    function profile()
    {
        require_once("config/config.inc.php");

        $user_id = isset($_GET['id']) ? $_GET['id'] : null;

        if ($user_id === null) {
            if (empty($_SESSION['user'])) {
                header("Location: index.php?controller=auth&action=login");
                exit;
            }

            $user_id = $_SESSION['user']['user_id'];
        }

        $stmt = $mysqli->prepare("SELECT * FROM AUTHORS WHERE user_id = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();

        $author = $stmt->get_result()->fetch_assoc();
        if (!$author) {
            header("Location: index.php?controller=home&action=index");
            exit;
        }

        $profile = json_decode($author['profile_json_text'], true);

        $sql = "SELECT PA.paper_id, PA.title, PA.author_string_list,
                CO.conference_id, CO.name AS conference_name, PAR.date_added, CO.end_date
            FROM PAPERS PA
                JOIN CONFERENCES CO ON PA.conference_id = CO.conference_id
                JOIN PARTICIPATION PAR ON PA.paper_id = PAR.paper_id
            WHERE PAR.author_id = $user_id
            ORDER BY PAR.date_added DESC";

        $papers = [];
        $res = $mysqli->query($sql);

        while ($row = $res->fetch_assoc()) {
            $papers[] = $row;
        }

        // Check if the current view is the profile's owner
        $is_owner = (!empty($_SESSION['user']) && $_SESSION['user']['user_id'] == $user_id);

        require_once("helpers/date_helper.php");
        require("views/author/profile.php");
    }

    function edit()
    {
        require_once("config/config.inc.php");

        if (empty($_SESSION['user'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $user_id = $_SESSION['user']['user_id'];
        $message = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $full_name = $_POST['full_name'];
            $website = $_POST['website'];
            $profile_json_text = $_POST['profile_json_text'];
            $image_path = '';

            if (!empty($_FILES['image']['name'])) {
                $image_path = "uploads/{$user_id}_profile_"
                    . basename($_FILES['image']['name']);
                move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
            }

            $stmt = $mysqli->prepare("UPDATE AUTHORS
                SET full_name=?, website=?, profile_json_text=?, image_path=?
                WHERE user_id=?");
            $stmt->bind_param("ssssi", $full_name, $website, $profile_json_text, $image_path, $user_id);
            $stmt->execute();

            $message = "Profile updated.";
        }

        $author = $mysqli->query("SELECT * FROM AUTHORS WHERE user_id = $user_id")->fetch_assoc();
        require("views/author/edit.php");
    }
}
