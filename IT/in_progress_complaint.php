<?php

session_start();

include "../connection.php";


/* =========================================
   LOGIN / ROLE CHECK
========================================= */

if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "it_staff"
) {

    header("Location: ../login.php");
    exit();

}

/* =========================================
   HEADER + SIDEBAR
========================================= */

include "it_staff_header.php";
include "it_staff_sidebar.php";

/* =========================================
   GET LOGGED-IN IT STAFF
========================================= */

$user_id = intval(
    $_SESSION["user_id"]
);


$staff_query = "
    SELECT
        id,
        name,
        email
    FROM user_table
    WHERE id = '$user_id'
    AND role = 'it_staff'
    LIMIT 1
";


$staff_run = mysqli_query(
    $conn,
    $staff_query
);


if (
    !$staff_run ||
    mysqli_num_rows($staff_run) == 0
) {

    session_destroy();

    header("Location: ../login.php");
    exit();

}


$staff = mysqli_fetch_assoc(
    $staff_run
);


$staff_name = $staff["name"];
$staff_email = $staff["email"];


/* =========================================
   ESCAPE STAFF NAME
========================================= */

$staff_name_safe =
    mysqli_real_escape_string(
        $conn,
        $staff_name
    );


/* =========================================
   GET IN-PROGRESS COMPLAINTS ASSIGNED
   TO CURRENT IT STAFF
========================================= */

$query = "
    SELECT

        c.id,
        c.complaint_code,
        c.email,
        c.role,
        c.c_category,
        c.asset_id,
        c.status,
        c.assigned_to,
        c.lab_number,
        c.asset_type

    FROM complaints c

    WHERE c.assigned_to = '$staff_name_safe'
    AND c.status = 'In Progress'

    ORDER BY c.id DESC
";


$run = mysqli_query(
    $conn,
    $query
);


?>


<div class="main-content">


    <!-- =====================================
         TOP HEADER
    ====================================== -->

    <div class="top-header">

        <div>

            <h4 class="mb-1">
                My In-Progress Complaints
            </h4>

            <small class="text-muted">
                Complaints currently assigned to you
                and in progress
            </small>

        </div>


        <div>

            <i class="bi bi-gear fs-3"></i>

        </div>

    </div>



    <!-- =====================================
         COMPLAINT CARD
    ====================================== -->

    <div class="content-card">


        <div
            class="
                d-flex
                justify-content-between
                align-items-center
                mb-3
            "
        >

            <h5 class="mb-0">

                <i class="bi bi-gear me-2"></i>

                My In-Progress Complaints

            </h5>


            <span class="text-muted">

                <?php

                if ($run) {

                    echo mysqli_num_rows($run);

                }

                else {

                    echo "0";

                }

                ?>

                complaint(s)

            </span>

        </div>



        <!-- =====================================
             TABLE
        ====================================== -->

        <div class="table-responsive">

            <table
                class="
                    table
                    table-bordered
                    table-hover
                    align-middle
                "
            >

                <thead>

                    <tr>

                        <th>
                            Complaint ID
                        </th>

                        <th>
                            Submitted By
                        </th>

                        <th>
                            Category
                        </th>

                        <th>
                            Asset
                        </th>

                        <th>
                            Asset Type
                        </th>

                        <th>
                            Lab
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php

                if (
                    $run &&
                    mysqli_num_rows($run) > 0
                ) {

                    while (
                        $row =
                        mysqli_fetch_assoc($run)
                    ) {

                ?>


                    <tr>


                        <!-- =================================
                             COMPLAINT ID
                        ================================== -->

                        <td>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $row["complaint_code"]
                                    ?: $row["id"]
                                );

                                ?>

                            </strong>

                        </td>



                        <!-- =================================
                             SUBMITTED BY
                        ================================== -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row["email"]
                            );

                            ?>

                            <br>

                            <small class="text-muted">

                                <?php

                                echo htmlspecialchars(
                                    ucfirst(
                                        $row["role"]
                                    )
                                );

                                ?>

                            </small>

                        </td>



                        <!-- =================================
                             CATEGORY
                        ================================== -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row["c_category"]
                            );

                            ?>

                        </td>



                        <!-- =================================
                             ASSET
                        ================================== -->

                        <td>

                            <?php

                            if (
                                !empty(
                                    $row["asset_id"]
                                )
                            ) {

                                echo htmlspecialchars(
                                    $row["asset_id"]
                                );

                            }

                            else {

                            ?>

                                <span class="text-muted">

                                    No asset

                                </span>

                            <?php

                            }

                            ?>

                        </td>



                        <!-- =================================
                             ASSET TYPE
                        ================================== -->

                        <td>

                            <?php

                            if (
                                !empty(
                                    $row["asset_type"]
                                )
                            ) {

                                echo htmlspecialchars(
                                    ucfirst(
                                        $row["asset_type"]
                                    )
                                );

                            }

                            else {

                            ?>

                                <span class="text-muted">

                                    N/A

                                </span>

                            <?php

                            }

                            ?>

                        </td>



                        <!-- =================================
                             LAB
                        ================================== -->

                        <td>

                            <?php

                            if (
                                !empty(
                                    $row["lab_number"]
                                )
                            ) {

                                echo "Lab "
                                    . htmlspecialchars(
                                        $row["lab_number"]
                                    );

                            }

                            else {

                            ?>

                                <span class="text-muted">

                                    N/A

                                </span>

                            <?php

                            }

                            ?>

                        </td>



                        <!-- =================================
                             STATUS
                        ================================== -->

                        <td>

                            <span
                                class="
                                    status-badge
                                    status-in-progress
                                "

                            >

                                In Progress

                            </span>

                        </td>



                        <!-- =================================
                             ACTION
                        ================================== -->

                        <td>

                            <a
                                href="view_complaint.php?id=<?php
                                    echo $row["id"];
                                ?>"
                                class="
                                    btn
                                    btn-primary
                                    btn-sm
                                "
                            >

                                <i
                                    class="
                                        bi
                                        bi-eye
                                        me-1
                                    "
                                ></i>

                                View

                            </a>

                        </td>


                    </tr>


                <?php

                    }

                }

                else {

                ?>


                    <tr>

                        <td
                            colspan="8"
                            class="text-center"
                        >

                            <div class="py-5">

                                <i
                                    class="
                                        bi
                                        bi-check-circle
                                        fs-1
                                        text-muted
                                    "
                                ></i>


                                <p class="mt-3 mb-0">

                                    You have no in-progress complaints assigned to you.

                                </p>

                            </div>

                        </td>

                    </tr>


                <?php

                }

                ?>


                </tbody>

            </table>

        </div>

    </div>


</div>



<?php

include "it_staff_footer.php";

?>