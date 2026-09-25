
<?php

require_once "../config/config.php";

/*
|--------------------------------------------------------------------------
| Check Login
|--------------------------------------------------------------------------
*/

if (!isLoggedIn()) {
    redirect("../authentication/login.php");
}


/*
|--------------------------------------------------------------------------
| Check Admin Role
|--------------------------------------------------------------------------
*/

if ($_SESSION["user_role"] != "admin") {
    redirect("../dashboard/index.php");
}


$page_title = "Admin Dashboard";


/*
|--------------------------------------------------------------------------
| Get Basic System Statistics
|--------------------------------------------------------------------------
*/


// Total users
$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total_users
     FROM users"
);

$stmt->execute();

$result = $stmt->get_result();

$data = $result->fetch_assoc();

$total_users = $data["total_users"];

$stmt->close();


// Total students
$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total_students
     FROM users
     WHERE role = 'student'"
);

$stmt->execute();

$result = $stmt->get_result();

$data = $result->fetch_assoc();

$total_students = $data["total_students"];

$stmt->close();


// Total admins
$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total_admins
     FROM users
     WHERE role = 'admin'"
);

$stmt->execute();

$result = $stmt->get_result();

$data = $result->fetch_assoc();

$total_admins = $data["total_admins"];

$stmt->close();


// Active users
$stmt = $conn->prepare(
    "SELECT COUNT(*) AS active_users
     FROM users
     WHERE is_active = 1"
);

$stmt->execute();

$result = $stmt->get_result();

$data = $result->fetch_assoc();

$active_users = $data["active_users"];

$stmt->close();


require_once "../includes/header.php";
require_once "../includes/navbar.php";
?>

<div class="admin-layout">

<?php
require_once "sidebar.php";
?>

<

<main class="main-content">

    <h1 class="page-title">
        Admin Dashboard
    </h1>

    <p class="page-description">
        Campus Coin system overview.
    </p>


    <div class="card">

        <h2>
            System Statistics
        </h2>


        <p>
            <strong>Total Users:</strong>
            <?php echo $total_users; ?>
        </p>


        <p>
            <strong>Total Students:</strong>
            <?php echo $total_students; ?>
        </p>


        <p>
            <strong>Total Admins:</strong>
            <?php echo $total_admins; ?>
        </p>


        <p>
            <strong>Active Users:</strong>
            <?php echo $active_users; ?>
        </p>

    </div>


    <br>


    <div class="card">

        <h2>
            Admin Panel
        </h2>

        <p>
            Use the admin panel to manage Campus Coin users,
            categories, saving tips, notifications and system data.
        </p>

    </div>

</main>

</div>
<?php

require_once "../includes/footer.php";

?>

