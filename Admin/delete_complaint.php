<?php

session_start();

include "../connection.php";

$id = $_GET["id"];

$query = "DELETE FROM complaints WHERE id='$id'";

$run = mysqli_query($conn, $query);

header("Location: all_complaints.php");

exit();

?>