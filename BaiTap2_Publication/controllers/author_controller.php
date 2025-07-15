<?php
class AuthorController
{
    function ajax_author_papers()
    {
        require_once('config/config.inc.php');
        require_once('helpers/pagination_helper.php');
        require_once("helpers/link_helper.php");
        require_once("helpers/date_helper.php");

        $user_id = intval($_GET['id'] ?? 0);
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 5;
        $offset = ($page - 1) * $limit;

        $count_sql = "SELECT COUNT(*) AS `total`
            FROM PARTICIPATION WHERE author_id = $user_id";

        $total = $mysqli->query($count_sql)->fetch_assoc()['total'];
        $total_pages = ceil($total / $limit);

        $sql = "SELECT PA.paper_id, PA.title, CO.conference_id,
                CO.name AS conference_name, PAR.date_added, CO.end_date,
                TP.topic_name
            FROM PAPERS PA
                JOIN CONFERENCES CO ON PA.conference_id = CO.conference_id
                JOIN PARTICIPATION PAR ON PA.paper_id = PAR.paper_id
                JOIN TOPICS TP ON PA.topic_id = TP.topic_id
            WHERE PAR.author_id = $user_id
            ORDER BY PAR.date_added DESC
            LIMIT $limit OFFSET $offset";

        $res = $mysqli->query($sql);
        $papers = $res->fetch_all(MYSQLI_ASSOC);

        ob_start();
        include('views/author/_paper_list.php');  // contains #paperList and pagination
        echo ob_get_clean();
        exit;
    }

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

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo 'JSON Error: ' . json_last_error_msg();
            $profile = [
                'bio' => 'Please update your bio!',
                'interests' => ['You haven\'t added any interest.']
            ];
        }

        // Check if the current view is the profile's owner
        $is_owner = (!empty($_SESSION['user']) && $_SESSION['user']['user_id'] == $user_id);

        require("views/author/profile.php");
    }

    private function rebuild_author_string_list($mysqli, $paper_id)
    {
        $sql = "SELECT AU.full_name
            FROM PARTICIPATION PAR
                JOIN AUTHORS AU ON PAR.author_id = AU.user_id
            WHERE PAR.paper_id = $paper_id AND PAR.status = 'show'
            ORDER BY PAR.role = 'first_author' DESC, PAR.date_added ASC";

        $res = $mysqli->query($sql);

        $names = [];
        while ($row = $res->fetch_assoc()) {
            $names[] = $row['full_name'];
        }

        $list = $mysqli->real_escape_string(implode(', ', $names));
        $mysqli->query("UPDATE PAPERS SET author_string_list = '$list' WHERE paper_id = $paper_id");

        /* Could use a trigger in the DB instead
        CREATE TRIGGER update_author_name
        AFTER UPDATE ON AUTHORS
        FOR EACH ROW
        BEGIN
            IF NEW.full_name != OLD.full_name THEN
                UPDATE PAPERS
                SET author_string_list = (
                    SELECT GROUP_CONCAT(A.full_name ORDER BY P.date_added SEPARATOR ', ')
                    FROM PARTICIPATION P
                        JOIN AUTHORS A ON A.user_id = P.author_id
                    WHERE P.paper_id = PAPERS.paper_id AND P.status = 'show'
                    ORDER BY P.role = 'first_author' DESC, P.date_added ASC
                )
                WHERE paper_id IN (
                    SELECT paper_id FROM PARTICIPATION WHERE author_id = NEW.user_id
                );
            END IF;
        END;
        */
    }

    function ajax_rebuild_author_string_list()
    {
        require_once("config/config.inc.php");

        $paper_id = intval($_GET['paper_id'] ?? 0);
        $this->rebuild_author_string_list($mysqli, $paper_id);
        exit('ok');
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
        $name_changed = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $full_name = $_POST['full_name'];

            // Check if full_name has been changed
            $stmt = $mysqli->prepare(
                "SELECT COUNT(*) AS cnt FROM AUTHORS
                WHERE user_id = ? AND LOWER(full_name) = LOWER(?)"
            );
            $stmt->bind_param('is', $user_id, $full_name);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            $count = $result->fetch_assoc()['cnt'];
            $name_changed = ($count === 0);
            //

            $website = $_POST['website'];

            $profile_json_text = $_POST['profile_json_text'];
            $profile_json_text = str_replace(["\r\n", "\n", "\r"], '\\n', $profile_json_text);

            $image_path = null;

            $fields = ['full_name = ?', 'website = ?', 'profile_json_text = ?'];
            $params = [$full_name, $website, $profile_json_text];
            $types = 'sss';

            if (!empty($_FILES['image']['name'])) {
                $filename = basename($_FILES['image']['name']);

                $image_path = "uploads/{$user_id}_profile."
                    . pathinfo($filename, PATHINFO_EXTENSION);

                move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);

                $fields[] = 'image_path = ?';
                $params[] = $image_path;
                $types .= 's';
            }

            $fields_sql = implode(', ', $fields);
            $params[] = $user_id;
            $types .= 'i';

            $stmt = $mysqli->prepare("UPDATE AUTHORS SET {$fields_sql} WHERE user_id=?");
            $stmt->bind_param($types, ...$params);
            $stmt->execute();

            $message = "Profile updated.";
        }

        $author = $mysqli->query("SELECT * FROM AUTHORS WHERE user_id = $user_id")->fetch_assoc();
        require("views/author/edit.php");

        if ($name_changed) {
            $res = $mysqli->query(
                "SELECT DISTINCT paper_id FROM PARTICIPATION WHERE author_id = $user_id"
            );

            $paper_ids = array_column($res->fetch_all(MYSQLI_ASSOC), 'paper_id');

            foreach ($paper_ids as $pid) {
                $this->rebuild_author_string_list($mysqli, $pid);
            }
        }
    }
}
