<?php

$current_page =
    basename($_SERVER["PHP_SELF"]);

$user_id =
    intval($_SESSION["user_id"]);


$query = "
    SELECT
        name,
        profile_picture
    FROM user_table
    WHERE id = '$user_id'
    LIMIT 1
";


$run =
    mysqli_query(
        $conn,
        $query
    );


$user =
    mysqli_fetch_assoc($run);


$name =
    $user["name"] ?? "IT Staff";


$profile_picture =
    $user["profile_picture"] ?? "";

?>

<div class="sidebar">


    <!-- PROFILE -->

    <div class="sidebar-profile">


        <?php

        if (
            !empty($profile_picture)
        ) {

        ?>

            <img
                src="../uploads/<?php
                    echo htmlspecialchars(
                        $profile_picture
                    );
                ?>"
                alt="Profile Picture"
            >

        <?php

        }

        else {

        ?>

            <div class="profile-placeholder">

                <i
                    class="bi bi-person-fill"
                ></i>

            </div>

        <?php

        }

        ?>


        <h6>

            <?php

            echo htmlspecialchars(
                $name
            );

            ?>

        </h6>


        <small>
            IT Staff
        </small>

    </div>



    <!-- HEADER -->

    <div class="sidebar-header">

        <h4>
            IT Staff Panel
        </h4>

        <p>
            Complaint Management
        </p>

    </div>



    <!-- MENU -->

    <div class="sidebar-menu">


        <!-- DASHBOARD -->

        <a
            href="dashboard.php"
            class="<?php

                echo (
                    $current_page ===
                    "dashboard.php"
                )
                    ? "active"
                    : "";

            ?>"
        >

            <i
                class="bi bi-speedometer2"
            ></i>

            <span>
                Dashboard
            </span>

        </a>



        <!-- MY COMPLAINTS -->

        <a
            href="assigned_complaints.php"
            class="<?php

                echo (
                    $current_page ===
                    "assigned_complaints.php"
                )
                    ? "active"
                    : "";

            ?>"
        >

            <i
                class="bi bi-file-text"
            ></i>

            <span>
                Assigned Complaints
            </span>

        </a>



        <!-- PENDING -->

        <a
            href="pending_complaint.php"
            class="<?php

                echo (
                    $current_page ===
                    "pending_complaint.php"
                )
                    ? "active"
                    : "";

            ?>"
        >

            <i
                class="bi bi-clock"
            ></i>

            <span>
                Pending Complaints
            </span>

        </a>



        <!-- IN PROGRESS -->

        <a
            href="in_progress_complaint.php"
            class="<?php

                echo (
                    $current_page ===
                    "in_progress_complaint.php"
                )
                    ? "active"
                    : "";

            ?>"
        >

            <i
                class="bi bi-arrow-repeat"
            ></i>

            <span>
                In Progress
            </span>

        </a>



        <!-- RESOLVED -->

        <a
            href="resolved_complaints.php"
            class="<?php

                echo (
                    $current_page ===
                    "resolved_complaints.php"
                )
                    ? "active"
                    : "";

            ?>"
        >

            <i
                class="bi bi-check-circle"
            ></i>

            <span>
                Resolved Complaints
            </span>

        </a>



        <!-- UNSERVICEABLE -->

        <a
            href="unserviceable_complaints.php"
            class="<?php

                echo (
                    $current_page ===
                    "unserviceable_complaints.php"
                )
                    ? "active"
                    : "";

            ?>"
        >

            <i
                class="bi bi-exclamation-triangle"
            ></i>

            <span>
                Unserviceable
            </span>

        </a>



        <!-- LOGOUT -->

        <a
            href="../logout.php"
        >

            <i
                class="bi bi-box-arrow-right"
            ></i>

            <span>
                Logout
            </span>

        </a>


    </div>

</div>