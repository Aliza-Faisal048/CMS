<?php

$ams_conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "ams_db"
);

if (!$ams_conn) {
    die("AMS database connection failed: " . mysqli_connect_error());
}

?>