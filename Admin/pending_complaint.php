<?php

session_start();

include "../connection.php";
include "admin_header.php";
include "admin_sidebar.php";

/* =========================================
   ACCESS CONTROL
   ========================================= */

if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "hr_admin"
) {

    header("Location: ../login.php");
    exit();

}


/* =========================================
   GET PENDING COMPLAINTS
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
        c.asset_type,

        GROUP_CONCAT(
            cp.problem_detail
            SEPARATOR '|||'
        ) AS problems

    FROM complaints c

    LEFT JOIN complaint_problems cp
        ON c.id = cp.complaint_id

    WHERE c.status = 'Pending'

    GROUP BY c.id

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
                Pending Complaints
            </h4>

            <small class="text-muted">
                Complaints currently awaiting further action
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


        <div class="
            d-flex
            justify-content-between
            align-items-center
            mb-3
        ">

            <h5 class="mb-0">

                <i class="bi bi-clock me-2"></i>

                Pending Complaints

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
                            Role
                        </th>

                        <th>
                            Category
                        </th>

                        <th>
                            Asset
                        </th>

                        <th>
                            Problem
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
                             SUBMITTED BY
                        ================================== -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row["email"]
                            );

                            ?>

                        </td>



                        <!-- =================================
                             ROLE
                        ================================== -->

                        <td>

                            <?php

                            if (
                                $row["role"] === "student"
                            ) {

                                echo "Student";

                            }

                            elseif (
                                $row["role"] === "teacher"
                            ) {

                                echo "Teacher";

                            }

                            else {

                                echo htmlspecialchars(
                                    $row["role"]
                                );

                            }

                            ?>

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
                             PROBLEM
                        ================================== -->

                        <td>

                            <?php

                            if (
                                !empty(
                                    $row["problems"]
                                )
                            ) {

                                $problem_list =
                                    explode(
                                        "|||",
                                        $row["problems"]
                                    );


                                foreach (
                                    $problem_list
                                    as $problem
                                ) {

                            ?>

                                <div class="mb-1">

                                    <i
                                        class="
                                            bi
                                            bi-dot
                                        "
                                    ></i>

                                    <?php

                                    echo htmlspecialchars(
                                        trim($problem)
                                    );

                                    ?>

                                </div>

                            <?php

                                }

                            }

                            else {

                            ?>

                                <span class="text-muted">

                                    No problem specified

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

                                <span
                                    class="text-muted"
                                >

                                    Unassigned

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
                                    status-pending
                                "
                            >

                                Pending

                            </span>

                        </td>



                        <!-- =================================
                             ACTIONS
                        ================================== -->

                        <td>

                            <div
                                class="
                                    d-flex
                                    gap-2
                                    flex-wrap
                                "
                            >


                                <!-- EDIT -->

                                <a
                                    href="edit_complaint.php?id=<?php
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
                                            bi-pencil
                                        "
                                    ></i>

                                    Edit

                                </a>



                                <!-- DELETE -->

                                <a
                                    href="delete_complaint.php?id=<?php
                                        echo $row["id"];
                                    ?>"
                                    class="
                                        btn
                                        btn-outline-danger
                                        btn-sm
                                    "
                                >

                                    <i
                                        class="
                                            bi
                                            bi-trash
                                        "
                                    ></i>

                                    Delete

                                </a>


                            </div>

                        </td>


                    </tr>


                <?php

                    }

                }

                else {

                ?>


                    <tr>

                        <td
                            colspan="9"
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

                                    No pending complaints found.

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

include "admin_footer.php";

?>