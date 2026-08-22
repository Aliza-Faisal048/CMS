<?php

include_once "connection.php";
session_start();


/* If user is already logged in */

if (isset($_SESSION["testing"])) {

    if ($_SESSION["role"] === "student") {

        header("Location: Student/dashboard.php");

    }
    elseif ($_SESSION["role"] === "teacher") {

        header("Location: Teacher/dashboard.php");

    }
    elseif ($_SESSION["role"] === "hr_admin") {

        header("Location: Admin/dashboard.php");

    }
    elseif ($_SESSION["role"] === "it_staff") {

        header("Location: IT/dashboard.php");

    }
    else {

        header("Location: dashboard.php");

    }

    exit();
}


/* Login */

if (isset($_POST["login-btn"])) {

    $email = $_POST["email"];
    $password = $_POST["password"];
    $role = $_POST["role"];


    $query = "SELECT * FROM user_table
              WHERE email='$email'
              AND password='$password'
              AND role='$role'";


    $run = mysqli_query($conn, $query);


    if (mysqli_num_rows($run) > 0) {

        $user = mysqli_fetch_assoc($run);


        /* Store user information */

        $_SESSION["testing"] = "testing";
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["email"] = $user["email"];
        $_SESSION["role"] = $user["role"];


        /* Redirect according to role */

        if ($user["role"] === "student") {

            header("Location: Student/dashboard.php");

        }
        elseif ($user["role"] === "teacher") {

            header("Location: Teacher/dashboard.php");

        }
        elseif ($user["role"] === "hr_admin") {

            header("Location: Admin/dashboard.php");

        }
        elseif ($user["role"] === "it_staff") {

            header("Location: IT/dashboard.php");

        }
        else {

            header("Location: dashboard.php");

        }


        exit();

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Complaint Management System</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

</head>

<body>

    <div class="login-card">

        <img
            src="images/logo.png"
            alt="CMS-Logo"
            style="height: 50px; margin-bottom: 5px;"
        >

        <h2 class="login-title">
            Complaint Management System
        </h2>

        <p class="login-subtitle">
            Sign in to your account
        </p>

        <hr>


        <form method="POST">

            <!-- Email -->

            <div class="mb-3">

                <label class="form-label">
                    Email Address
                </label>

                <input
                    type="email"
                    name="email"
                    class="form-control"
                    placeholder="you@university.edu"
                    required
                >

            </div>


            <!-- Password -->

            <div class="mb-3">

                <label class="form-label">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder="Enter your password"
                    required
                >

            </div>


            <!-- Role -->

            <div class="mb-4">

                <label class="form-label">
                    Role
                </label>

                <select
                    name="role"
                    class="form-select"
                    required
                >

                    <option value="">
                        Choose your role
                    </option>

                    <option value="student">
                        Student
                    </option>

                    <option value="teacher">
                        Teacher
                    </option>

                    <option value="hr_admin">
                        HR Admin
                    </option>

                    <option value="it_staff">
                        IT Staff
                    </option>

                </select>

            </div>


            <!-- Login Button -->

            <button
                type="submit"
                name="login-btn"
                class="login-button"
            >

                Sign In

            </button>

        </form>


        <p class="footer-text">

            University Complaint Management System
            &copy; <?php echo date("Y"); ?>

        </p>

    </div>


    <script src="js/bootstrap.bundle.min.js"></script>

</body>

</html>