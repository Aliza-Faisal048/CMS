<?php

session_start();

include "../connection.php";
include "../includes/header.php";
include "../includes/sidebar.php";


/* =========================================
   LOGIN CHECK
========================================= */

if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["email"]) ||
    !isset($_SESSION["role"])
) {

    header("Location: ../login.php");
    exit();

}


/* =========================================
   CURRENT USER
========================================= */

$user_id = $_SESSION["user_id"];
$email = $_SESSION["email"];
$role = $_SESSION["role"];


/* =========================================
   ESCAPE EMAIL
========================================= */

$email_safe = mysqli_real_escape_string(
    $conn,
    $email
);


/* =========================================
   GET CURRENT USER'S IN PROGRESS COMPLAINTS
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

    WHERE c.email = '$email_safe'
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
                My In Progress Complaints
            </h4>

            <small class="text-muted">
                Complaints currently in progress and being addressed by the support team.
            </small>

        </div>


        <div>

            <i class="bi bi-clock-history fs-3"></i>

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

                <i class="bi bi-clock me-2"></i>

                My In Progress Complaints

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
                            Assigned To
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
                             ASSIGNED TO
                        ================================== -->

                        <td>

                            <?php

                            if (
                                !empty(
                                    $row["assigned_to"]
                                )
                            ) {

                                echo htmlspecialchars(
                                    $row["assigned_to"]
                                );

                            }

                            else {

                            ?>

                                <span class="text-muted">

                                    Waiting for assignment

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
                                    status-progress
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

                                    You have no in progress complaints.

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

include "../includes/footer.php";

?>