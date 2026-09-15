<?php

$host = getenv("MYSQLHOST") ?: "localhost";
$port = getenv("MYSQLPORT") ?: 3306;
$user = getenv("MYSQLUSER") ?: "root";
$password = getenv("MYSQLPASSWORD") ?: "";
$database = getenv("MYSQL_DATABASE") ?: getenv("MYSQLDATABASE") ?: "cms_db";

$conn = mysqli_connect(
    $host,
    $user,
    $password,
    $database,
    $port
);

if (!$conn) {
    die("Database connection failed. Check MYSQLHOST, MYSQLPORT, MYSQLUSER, MYSQLPASSWORD, and MYSQLDATABASE.");
}

mysqli_set_charset($conn, "utf8mb4");

?>