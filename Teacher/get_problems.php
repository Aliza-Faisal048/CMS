<?php

session_start();

include "../connection.php";

header("Content-Type: application/json");


/* Check login */

if (!isset($_SESSION["role"])) {

    echo json_encode([
        "success" => false,
        "message" => "User role not found in session."
    ]);

    exit();
}


$role = $_SESSION["role"];

$category = $_GET["category"] ?? "";


if ($category === "") {

    echo json_encode([
        "success" => false,
        "message" => "No category received."
    ]);

    exit();
}


/* Escape values */

$role = mysqli_real_escape_string(
    $conn,
    $role
);

$category = mysqli_real_escape_string(
    $conn,
    $category
);


/* Get problems */

$query = "
    SELECT id, problem_detail
    FROM problem_table
    WHERE p_category = '$category'
    AND role = '$role'
    ORDER BY id ASC
";


$run = mysqli_query($conn, $query);


if (!$run) {

    echo json_encode([
        "success" => false,
        "message" => mysqli_error($conn)
    ]);

    exit();
}


$problems = [];


while ($row = mysqli_fetch_assoc($run)) {

    $problems[] = [

        "id" => $row["id"],

        "problem_detail" =>
            $row["problem_detail"]

    ];

}


echo json_encode([
    "success" => true,
    "problems" => $problems
]);

?>