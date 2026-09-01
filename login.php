<?php

session_start();


/* =========================================
   UMS API SETTINGS
========================================= */

$ums_login_url =
    "https://ums-production-34b4.up.railway.app/api/login.php";

$ums_api_token =
    getenv("UMS_API_KEY");


/* =========================================
   IF USER IS ALREADY LOGGED IN
========================================= */

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


/* =========================================
   LOGIN
========================================= */

$login_error = "";


if (isset($_POST["login-btn"])) {

    $email =
        trim($_POST["email"]);

    $password =
        $_POST["password"];

    $selected_role =
        $_POST["role"];


    /* =====================================
       SEND LOGIN REQUEST TO UMS
    ===================================== */

    $login_data = json_encode([

        "email" =>
            $email,

        "password" =>
            $password

    ]);


    $ch =
        curl_init($ums_login_url);


    curl_setopt(
        $ch,
        CURLOPT_POST,
        true
    );


    curl_setopt(
        $ch,
        CURLOPT_POSTFIELDS,
        $login_data
    );


    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        [

            "Content-Type: application/json",

            "Authorization: Bearer " .
            $ums_api_token

        ]
    );


    curl_setopt(
        $ch,
        CURLOPT_RETURNTRANSFER,
        true
    );


    $response =
        curl_exec($ch);


    $curl_error =
        curl_error($ch);


    curl_close($ch);


    /* =====================================
       CHECK API CONNECTION
    ===================================== */

    if ($response === false || !empty($curl_error)) {

        $login_error =
            "Unable to connect to UMS. Please try again.";

    }

    else {

        /* Decode UMS response */

        $data =
            json_decode(
                $response,
                true
            );


        /* =================================
           CHECK LOGIN SUCCESS
        ================================= */

        if (
            isset($data["success"]) &&
            $data["success"] === true
        ) {


            /* =============================
               GET USER INFORMATION
            ============================== */

            if (
                isset($data["data"]["user"])
            ) {

                $user =
                    $data["data"]["user"];

            }
            else {

                $user = [];

            }


            /* =============================
               GET ROLE
            ============================== */

            $user_role =
                $user["role"] ?? "";

            /* =============================
               CHECK SELECTED ROLE
            ============================== */

            if (
                $selected_role !==
                $user_role
            ) {

                $login_error =
                    "The selected role does not match your UMS account.";

            }

            else {


                /* =========================
                   CREATE CMS SESSION
                ========================== */

                $_SESSION["testing"] =
                    "testing";


                $_SESSION["user_id"] =
                    $user["id"] ?? "";


                $_SESSION["email"] =
                    $user["email"] ?? $email;


                $_SESSION["role"] =
                    $user["role"] ?? $selected_role;


                $_SESSION["name"] =
                    $user["name"] ?? "";


                $_SESSION["department"] =
                    $user["department"] ?? "";


                $_SESSION["phone"] =
                    $user["phone"] ?? "";


                $_SESSION["dob"] =
                    $user["dob"] ?? "";


                $_SESSION["profile_picture"] =
                    $user["profile_picture"] ?? "";


                $_SESSION["current_semester"] =
                    $user["current_semester"] ?? "";


                $_SESSION["main_subject"] =
                    $user["main_subject"] ?? "";


                /* =========================
                   REDIRECT ACCORDING TO ROLE
                ========================== */

                if (
                    $user_role === "student"
                ) {

                    header(
                        "Location: Student/dashboard.php"
                    );

                }

                elseif (
                    $user_role === "teacher"
                ) {

                    header(
                        "Location: Teacher/dashboard.php"
                    );

                }

                elseif (
                    $user_role === "hr_admin"
                ) {

                    header(
                        "Location: Admin/dashboard.php"
                    );

                }

                elseif (
                    $user_role === "it_staff"
                ) {

                    header(
                        "Location: IT/dashboard.php"
                    );

                }

                else {

                    $login_error =
                        "Your account has an unsupported role.";

                }


                if (empty($login_error)) {

                    exit();

                }

            }

        }

        else {

            /* =============================
               UMS LOGIN FAILED
            ============================== */

            if (
                isset($data["message"]) &&
                !empty($data["message"])
            ) {

                $login_error =
                    $data["message"];

            }

            else {

                $login_error =
                    "Invalid email or password.";

            }

        }

    }

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
    Login - Complaint Management System
</title>


<!-- Bootstrap -->

<link
    href="css/bootstrap.min.css"
    rel="stylesheet"
>

<link
    rel="stylesheet"
    href="style.css"
>


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


    <?php if (!empty($login_error)) { ?>

        <div
            class="alert alert-danger"
            role="alert"
        >

            <?php

            echo htmlspecialchars(
                $login_error
            );

            ?>

        </div>

    <?php } ?>


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

    &copy;

    <?php echo date("Y"); ?>

    </p>


    <p class="signup-text">

        Don't have an account?

        <a
            href="https://ums-production-34b4.up.railway.app/signup.php"
            target="_blank"
        >
            Sign up
        </a>

    </p>


</div>


<script
    src="js/bootstrap.bundle.min.js"
></script>


</body>

</html>
