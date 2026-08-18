<?php

include "../connection.php";

if (isset($_GET['category'])) {

    $category = $_GET['category'];

    $stmt = $conn->prepare("
        SELECT id, problem
        FROM complaint_problems
        WHERE category = ?
        ORDER BY problem ASC
    ");

    $stmt->bind_param("s", $category);

    $stmt->execute();

    $result = $stmt->get_result();

    $problems = [];

    while ($row = $result->fetch_assoc()) {

        $problems[] = $row;

    }

    header('Content-Type: application/json');

    echo json_encode($problems);

}