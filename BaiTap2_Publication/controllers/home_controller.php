<?php
class HomeController
{
    function index()
    {
        require_once("config/config.inc.php");

        $topics = [];

        $sql = "SELECT * FROM TOPICS ORDER BY topic_name";
        $result = $mysqli->query($sql);

        while ($row = $result->fetch_assoc()) {
            $topics[] = $row;
        }
        $result->free();

        $papers_by_topic = [];

        foreach ($topics as $topic) {
            $tid = $topic['topic_id'];

            $sql = "SELECT PA.paper_id, PA.title, PA.author_string_list,
                    CO.conference_id, CO.name AS conference_name, CO.start_date, CO.end_date
                FROM PAPERS PA JOIN CONFERENCES CO
                    ON PA.conference_id = CO.conference_id
                WHERE PA.topic_id = $tid
                ORDER BY CO.start_date DESC
                LIMIT 5";

            $res = $mysqli->query($sql);
            while ($paper = $res->fetch_assoc()) {
                $papers_by_topic[$tid][] = $paper;
            }
            $res->free();
        }

        require_once("helpers/date_helper.php");
        require("views/home/index.php");
    }
}
