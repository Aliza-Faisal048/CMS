<?php
 session_start();
 session_destroy();

header("Location: login.php");
exit();
// if(isset($_SESSION["testing"])){
//     unset($_SESSION["testing"]);
//     header("location:login.php");
//     exit();
// }

?>