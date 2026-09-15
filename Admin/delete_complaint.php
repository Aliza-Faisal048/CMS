<?php

session_start();

require_once __DIR__ . "/../connection.php";

if (
    !isset($_SESSION["logged_in"]) ||
    $_SESSION["logged_in"] !== true ||
    ($_SESSION["role"] ?? "") !== "hr admin"
) {
    header("Location: ../login.php");
    exit();
}

$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($id <= 0) {
    header("Location: all_complaints.php");
    exit();
}


/* Delete related problems */

$query = "
    DELETE FROM complaint_problems
    WHERE complaint_id = '$id'
";

mysqli_query($conn, $query);


/* Delete status history */

$query = "
    DELETE FROM complaint_status_history
    WHERE complaint_id = '$id'
";

mysqli_query($conn, $query);


/* Delete complaint */

$query = "
    DELETE FROM complaints
    WHERE id = '$id'
";

$run = mysqli_query($conn, $query);


/* Go back to complaints */

header("Location: all_complaints.php");

exit();

?>