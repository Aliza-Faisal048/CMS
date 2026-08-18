<?php include "connection.php";
 session_start();

if(!isset($_SESSION["testing"])){
    header("location:login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS Dashboard</title>
    <style>
        .navbar{
            width: 100%;
            height:60px;
            background-color: pink;
            padding:5px;
        }
        button{
            color: white;
            border: none;
            padding: 8px 15px;
            background-color: pink;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <button><a href="logout.php">Log-out</a></button>
    </div>
</body>
</html>