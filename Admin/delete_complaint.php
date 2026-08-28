<?php

session_start();

include "../connection.php";

$id = (int) $_GET["id"];


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