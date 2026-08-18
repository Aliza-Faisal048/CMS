<?php

session_start();

include "../connection.php";

include "../includes/header.php";
include "../includes/sidebar.php";

$email = $_SESSION["email"];

$query = "SELECT * FROM complaints WHERE email='$email'";

$run = mysqli_query($conn, $query);

?>

<div class="main-content">

    <!-- TOP HEADER -->

    <div class="top-header">

        <div>

            <h4 class="mb-1">
                All Complaints
            </h4>

            <small class="text-muted">
                View all complaints you have submitted
            </small>

        </div>

    </div>


    <!-- COMPLAINTS TABLE -->

    <div class="content-card">

        <h4 class="mb-4">
            My Complaints
        </h4>


        <div class="table-responsive">

            <table class="table table-bordered table-hover">

    <thead>
        <tr>
            <th>Complaint ID</th>
            <th>Category</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>

        <?php

        if (mysqli_num_rows($run) > 0) {

            while ($row = mysqli_fetch_assoc($run)) {

        ?>

            <tr>

                <td>
                    <?php echo $row["id"]; ?>
                </td>

                <td>
                    <?php echo $row["c_category"]; ?>
                </td>

                <td>

                    <?php if ($row["status"] == "Pending") { ?>

                        <span class="status-badge status-pending">
                            Pending
                        </span>

                    <?php } elseif ($row["status"] == "In Progress") { ?>

                        <span class="status-badge status-progress">
                            In Progress
                        </span>

                    <?php } elseif ($row["status"] == "Resolved") { ?>

                        <span class="status-badge status-resolved">
                            Resolved
                        </span>

                    <?php } else { ?>

                        <span class="status-badge">
                            <?php echo $row["status"]; ?>
                        </span>

                    <?php } ?>

                </td>

                <td>

                    <a
                        href="view_complaint.php?id=<?php echo $row["id"]; ?>"
                        class="btn btn-primary btn-sm">

                        View

                    </a>

                </td>

            </tr>

        <?php

            }

        } else {

        ?>

            <tr>
                <td colspan="4" class="text-center">
                    No complaints found.
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