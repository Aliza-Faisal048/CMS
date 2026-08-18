```php
<?php

include "../connection.php";

include "../includes/header.php";

include "../includes/sidebar.php";
$email = $_SESSION["email"];

// Total complaints

$query = "SELECT COUNT(*) AS total
          FROM complaints WHERE email='$email'";

$run = mysqli_query($conn, $query);

$row = mysqli_fetch_assoc($run);

$total_complaints = $row["total"];


// Pending complaints

$query = "SELECT COUNT(*) AS pending
          FROM complaints
          WHERE status='Pending' && email='$email'";

$run = mysqli_query($conn, $query);

$row = mysqli_fetch_assoc($run);

$pending_complaints = $row["pending"];

// Resolved complaints

$query = "SELECT COUNT(*) AS resolved
          FROM complaints
          WHERE status='Resolved' && email='$email'";

$run = mysqli_query($conn, $query);

$row = mysqli_fetch_assoc($run);

$resolved_complaints = $row["resolved"];

?>


<!-- =========================================
     MAIN CONTENT
     ========================================= -->

<div class="main-content">


    <!-- TOP HEADER -->

    <div class="top-header">

        <div>

            <h4 class="mb-1">
                Student Dashboard
            </h4>

            <small class="text-muted">
                Complaint Management System
            </small>

        </div>


        <div>

            <i class="bi bi-person-circle fs-3"></i>

        </div>

    </div>



    <!-- WELCOME -->

    <div class="content-card">

        <h2>

            Welcome, Student <i class="bi bi-backpack fs-3"></i>

        </h2>


        <p>

            Welcome to the Student Complaint
            Management Panel.

        </p>

    </div>



    <!-- STATISTICS -->

    <div class="row g-4">


        <!-- Total Complaints -->

        <div class="col-md-4">

            <div class="stat-card">


                <div class="d-flex
                            justify-content-between
                            align-items-center">


                    <div>

                        <p class="text-muted mb-1">

                            Total Complaints

                        </p>

                        <h4><?php echo $total_complaints ?></h4>

                    </div>


                    <div class="stat-icon">

                        <i class="bi bi-file-text"></i>

                    </div>


                </div>


            </div>

        </div>



        <!-- Pending -->

        <div class="col-md-4">

            <div class="stat-card">


                <div class="d-flex
                            justify-content-between
                            align-items-center">


                    <div>

                        <p class="text-muted mb-1">

                            Pending

                        </p>

                          <h4><?php echo $pending_complaints ?></h4>

                    </div>


                    <div class="stat-icon">

                        <i class="bi bi-clock"></i>

                    </div>


                </div>


            </div>

        </div>



        <!-- Resolved -->

        <div class="col-md-4">

            <div class="stat-card">


                <div class="d-flex
                            justify-content-between
                            align-items-center">


                    <div>

                        <p class="text-muted mb-1">

                            Resolved

                        </p>
                              <h4><?php echo $resolved_complaints ?></h4>

                    </div>


                    <div class="stat-icon">

                        <i class="bi bi-check-circle"></i>

                    </div>


                </div>


            </div>
        </div>


    </div>


</div>


<?php

include "includes/footer.php";

?>
```
