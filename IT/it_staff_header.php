<?php

if (
    session_status() ===
    PHP_SESSION_NONE
) {

    session_start();

}


if (
    !isset($_SESSION["testing"]) ||
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "it_staff"
) {

    header("Location: ../login.php");
    exit();

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        IT Staff Panel - Complaint Management System
    </title>


    <link
        rel="stylesheet"
        href="../css/bootstrap.min.css"
    >

    <link
        rel="stylesheet"
        href="../bootstrap-icons/bootstrap-icons.css"
    >

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>


<body>


<nav class="top-navbar">


    <div class="navbar-logo">

        <img
            src="../images/logo.png"
            alt="CMS Logo"
        >

        <a href="dashboard.php" class="text-decoration-none">
                Complaint Management System
            </a>

    </div>


    <a
        href="../logout.php"
        class="logout-btn"
    >

        <i
            class="bi bi-box-arrow-right"
        ></i>

        Logout

    </a>


</nav>