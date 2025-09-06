<?php
define('BASE_URL', '/Final_TheOffice/');

/* MYSQL */
$host = 'localhost';
$dbname = 'OfficeManagementDB';
$username = 'root';
$password = 'root';

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_error) {
    echo "Connection failed: " . $mysqli->connect_error;
    die("Connection failed: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8");
