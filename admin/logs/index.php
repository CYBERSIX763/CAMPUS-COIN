
<?php

require_once "../../config/config.php";


/*
|--------------------------------------------------------------------------
| Admin Access
|--------------------------------------------------------------------------
*/

if (!isLoggedIn()) {
    redirect("../../authentication/login.php");
}

if ($_SESSION["user_role"] != "admin") {
    redirect("../../dashboard/index.php");
}


$page_title = "Admin Logs";


/*
|--------------------------------------------------------------------------
| Get Admin Logs
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT
        l.log_id,
        l.admin_id,
        l.action,
        l.target_type,
        l.target_id,
        l.description,
        l.created_at,
        u.name AS admin_name,
        u.email AS admin_email
     FROM admin_logs l
     LEFT JOIN users u
        ON l.admin_id = u.user_id
     ORDER BY l.created_at DESC"
);

$stmt->execute();

$logs = $stmt->get_result();

$stmt->close();



require_once "../../includes/header.php";
require_once "../../includes/navbar.php";
?>

<div class="admin-layout">

<?php
require_once "../sidebar.php";
?>


<main class="main-content">

    <h1 class="page-title">
        Admin Logs
    </h1>

    <p class="page-description">
        View administrator activity and system actions.
    </p>


    <div class="card">

        <table style="width: 100%; border-collapse: collapse;">

            <thead>

                <tr>

                    <th style="padding: 10px; border: 1px solid #ddd;">
                        ID
                    </th>

                    <th style="padding: 10px; border: 1px solid #ddd;">
                        Admin
                    </th>

                    <th style="padding: 10px; border: 1px solid #ddd;">
                        Action
                    </th>

                    <th style="padding: 10px; border: 1px solid #ddd;">
                        Target Type
                    </th>

                    <th style="padding: 10px; border: 1px solid #ddd;">
                        Target ID
                    </th>

                    <th style="padding: 10px; border: 1px solid #ddd;">
                        Description
                    </th>

                    <th style="padding: 10px; border: 1px solid #ddd;">
                        Date
                    </th>

                </tr>

            </thead>


            <tbody>

                <?php if ($logs->num_rows > 0) { ?>

                    <?php while ($log = $logs->fetch_assoc()) { ?>

                        <tr>

                            <td style="padding: 10px; border: 1px solid #ddd;">
                                <?php echo $log["log_id"]; ?>
                            </td>


                            <td style="padding: 10px; border: 1px solid #ddd;">

                                <?php
                                echo htmlspecialchars(
                                    $log["admin_name"] ?? "Unknown"
                                );
                                ?>

                                <br>

                                <small>
                                    <?php
                                    echo htmlspecialchars(
                                        $log["admin_email"] ?? ""
                                    );
                                    ?>
                                </small>

                            </td>


                            <td style="padding: 10px; border: 1px solid #ddd;">
                                <?php
                                echo htmlspecialchars(
                                    $log["action"]
                                );
                                ?>
                            </td>


                            <td style="padding: 10px; border: 1px solid #ddd;">
                                <?php
                                echo htmlspecialchars(
                                    $log["target_type"]
                                );
                                ?>
                            </td>


                            <td style="padding: 10px; border: 1px solid #ddd;">
                                <?php
                                echo $log["target_id"] === null
                                    ? "-"
                                    : htmlspecialchars($log["target_id"]);
                                ?>
                            </td>


                            <td style="padding: 10px; border: 1px solid #ddd;">
                                <?php
                                echo htmlspecialchars(
                                    $log["description"]
                                );
                                ?>
                            </td>


                            <td style="padding: 10px; border: 1px solid #ddd;">
                                <?php
                                echo htmlspecialchars(
                                    $log["created_at"]
                                );
                                ?>
                            </td>

                        </tr>

                    <?php } ?>

                <?php } else { ?>

                    <tr>

                        <td
                            colspan="7"
                            style="
                                padding: 20px;
                                text-align: center;
                                border: 1px solid #ddd;
                            "
                        >
                            No admin activity has been recorded yet.
                        </td>

                    </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>

</main>


<?php

require_once "../../includes/footer.php";

?>

