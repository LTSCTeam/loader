<?php
$host = 'localhost';
$database = 'test';
$user = 'root';
$password = '';

$connection = new mysqli($host, $user, $password, $database);

if ($connection->connect_error) {
    die("Database connection failed: " . $connection->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["hwid"])) {
    $hwid = $connection->real_escape_string($_POST["hwid"]);

    $result = $connection->query("SELECT * FROM `hwid_table` WHERE `hwid` = '{$hwid}'");

    if ($result->num_rows > 0) {
        echo "Allowed";
    } else {
        echo "Invalid";
    }
} else {
    echo "Invalid request";
}

$connection->close();
?>