<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["testing"])) {
    header("Location: ../login.php");
    exit();
}

if (
    $_SESSION["role"] !== "student" &&
    $_SESSION["role"] !== "teacher"
) {
    echo "Access Denied";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Complaint Management System</title>

    <link
        href="../css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="../bootstrap-icons/bootstrap-icons.min.css">

    <link
        rel="stylesheet"
        href="../Student/style.css">

</head>

<body>

<nav class="top-navbar">

    <div class="navbar-logo">

        <img
            src="../images/logo.png"
            alt="CMS Logo">

        <span>
            Complaint Management System
        </span>

    </div>

    <a
        href="../logout.php"
        class="logout-btn">

        <i class="bi bi-box-arrow-right"></i>

        Logout

    </a>

</nav>