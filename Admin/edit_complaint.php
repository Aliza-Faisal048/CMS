<?php

session_start();

include "../connection.php";


$id = $_GET["id"];


// Update complaint

if (isset($_POST["update-btn"])) {

    $c_category = $_POST["c_category"];
    $c_detail = $_POST["c_detail"];
    $c_description = $_POST["c_description"];
    $status = $_POST["status"];


    $query = "UPDATE complaints SET
              c_category='$c_category',
              c_detail='$c_detail',
              c_description='$c_description',
              status='$status'

              WHERE id='$id'";


    $run = mysqli_query($conn, $query);


    if ($run) {

        header("Location: all_complaints.php");
        exit();

    }

}


// Get complaint

$query = "SELECT complaints.*, problem_table.problem_detail
          FROM complaints
          LEFT JOIN problem_table
          ON complaints.c_detail = problem_table.id
          WHERE complaints.id='$id'";

$run = mysqli_query($conn, $query);

$complaint = mysqli_fetch_assoc($run);



include "admin_header.php";
include "admin_sidebar.php";

?>

<div class="main-content">

    <!-- TOP HEADER -->

    <div class="top-header">

        <div>

            <h4 class="mb-1">
                Edit Complaint
            </h4>

            <small class="text-muted">
                View and update complaint details
            </small>

        </div>

    </div>


    <div class="content-card">

        <h4 class="mb-4">
            Complaint #<?php echo $complaint["id"]; ?>
        </h4>

        <form method="POST">
        <!-- Student Email -->

        <div class="mb-3">

            <label class="form-label">
                Student Email
            </label>

            <input
                type="text"
                class="form-control"
                value="<?php echo $complaint["email"]; ?>"
                readonly>

        </div>


        <!-- Category -->

        <div class="mb-3">

            <label class="form-label">
                Category
            </label>

            <select
                name="c_category"
                class="form-select">

                <option value="Academic"
                    <?php
                    if ($complaint["c_category"] == "Academic") {
                        echo "selected";
                    }
                    ?>>
                    Academic
                </option>

                <option value="Facilities"
                    <?php
                    if ($complaint["c_category"] == "Facilities") {
                        echo "selected";
                    }
                    ?>>
                    Facilities
                </option>

                <option value="IT"
                    <?php
                    if ($complaint["c_category"] == "IT") {
                        echo "selected";
                    }
                    ?>>
                    IT / Technical
                </option>

                <option value="Transport"
                    <?php
                    if ($complaint["c_category"] == "Transport") {
                        echo "selected";
                    }
                    ?>>
                    Transport
                </option>

                <option value="Hostel"
                    <?php
                    if ($complaint["c_category"] == "Hostel") {
                        echo "selected";
                    }
                    ?>>
                    Hostel
                </option>

            </select>

        </div>


        <!-- Problem -->

        <div class="mb-3">

            <label class="form-label">
                Problem
            </label>

            <input
                type="text"
                name="c_detail"
                class="form-control"
                value="<?php echo $complaint["problem_detail"]; ?>">

        </div>


        <!-- Description -->

        <div class="mb-3">

            <label class="form-label">
                Description
            </label>

            <textarea
                name="c_description"
                class="form-control"
                rows="5"><?php echo $complaint["c_description"]; ?></textarea>

        </div>


        <!-- Status -->

        <div class="mb-4">

            <label class="form-label">
                Status
            </label>

            <select
                name="status"
                class="form-select">

                <option value="Pending"
                    <?php
                    if ($complaint["status"] == "Pending") {
                        echo "selected";
                    }
                    ?>>
                    Pending
                </option>

                <option value="In Progress"
                    <?php
                    if ($complaint["status"] == "In Progress") {
                        echo "selected";
                    }
                    ?>>
                    In Progress
                </option>

                <option value="Resolved"
                    <?php
                    if ($complaint["status"] == "Resolved") {
                        echo "selected";
                    }
                    ?>>
                    Resolved
                </option>

            </select>

        </div>


        <button
            type="submit"
            name="update-btn"
            class="btn btn-primary">

            Update Complaint

        </button>


        <a
            href="all_complaints.php"
            class="btn btn-secondary">

            Back

        </a>
        </form>
    </div>

</div>


<?php

include "admin_footer.php";

?>