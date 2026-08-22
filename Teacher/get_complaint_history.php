<?php

session_start();

include "../connection.php";

header("Content-Type: application/json; charset=UTF-8");


/* =========================================
   LOGIN CHECK
   ========================================= */

if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "You are not logged in."
    ]);

    exit;
}


$email = $_SESSION["email"];

$email = mysqli_real_escape_string(
    $conn,
    $email
);


/* =========================================
   GET COMPLAINT ID
   ========================================= */

if (!isset($_GET["id"])) {

    echo json_encode([
        "success" => false,
        "message" => "Complaint ID is missing."
    ]);

    exit;
}


$complaint_id = (int) $_GET["id"];


if ($complaint_id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid complaint ID."
    ]);

    exit;
}


/* =========================================
   VERIFY COMPLAINT BELONGS TO USER
   ========================================= */

$check_query = "
    SELECT id
    FROM complaints
    WHERE id = '$complaint_id'
    AND email = '$email'
    LIMIT 1
";


$check_run = mysqli_query(
    $conn,
    $check_query
);


if (!$check_run) {

    echo json_encode([
        "success" => false,
        "message" => "Database error while checking complaint."
    ]);

    exit;
}


if (mysqli_num_rows($check_run) === 0) {

    echo json_encode([
        "success" => false,
        "message" => "Complaint not found."
    ]);

    exit;
}


/* =========================================
   GET HISTORY
   ========================================= */

$history_query = "
    SELECT
        id,
        status,
        changed_by,
        remarks,
        changed_at
    FROM complaint_status_history
    WHERE complaint_id = '$complaint_id'
    ORDER BY changed_at ASC, id ASC
";


$history_run = mysqli_query(
    $conn,
    $history_query
);


if (!$history_run) {

    echo json_encode([
        "success" => false,
        "message" => "Unable to retrieve complaint history."
    ]);

    exit;
}


$history = [];


while ($row = mysqli_fetch_assoc($history_run)) {

    $history[] = [

        "status" =>
            $row["status"],

        "changed_by" =>
            $row["changed_by"] ?? "",

        "remarks" =>
            $row["remarks"] ?? "",

        "changed_at" =>
            $row["changed_at"]

    ];

}


/* =========================================
   SEND RESPONSE
   ========================================= */

echo json_encode([

    "success" => true,

    "history" => $history

]);

exit;

?>