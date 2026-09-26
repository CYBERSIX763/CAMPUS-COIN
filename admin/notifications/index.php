
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


$page_title = "Notifications";


/*
|--------------------------------------------------------------------------
| Get Notifications
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT
        n.notification_id,
        n.user_id,
        n.title,
        n.message,
        n.type,
        n.is_read,
        n.created_at,
        n.notification_key,
        u.name AS user_name,
        u.email AS user_email
     FROM notifications n
     LEFT JOIN users u
        ON n.user_id = u.user_id
     ORDER BY n.created_at DESC"
);

$stmt->execute();

$notifications = $stmt->get_result();

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
        Notifications
    </h1>

    <p class="page-description">
        View and manage system notifications.
    </p>


    <div class="card">

        <table style="width: 100%; border-collapse: collapse;">

            <thead>

                <tr>

                    <th style="padding: 10px; border: 1px solid #ddd;">
                        ID
                    </th>

                    <th style="padding: 10px; border: 1px solid #ddd;">
                        User
                    </th>

                    <th style="padding: 10px; border: 1px solid #ddd;">
                        Title
                    </th>

                    <th style="padding: 10px; border: 1px solid #ddd;">
                        Message
                    </th>

                    <th style="padding: 10px; border: 1px solid #ddd;">
                        Type
                    </th>

                    <th style="padding: 10px; border: 1px solid #ddd;">
                        Status
                    </th>

                    <th style="padding: 10px; border: 1px solid #ddd;">
                        Created
                    </th>

                    <th style="padding: 10px; border: 1px solid #ddd;">
                        Actions
                    </th>

                </tr>

            </thead>


            <tbody>

                <tbody>

    <?php if ($notifications->num_rows > 0) { ?>

        <?php while ($notification = $notifications->fetch_assoc()) { ?>

            <tr>

                    <tr>

                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <?php echo $notification["notification_id"]; ?>
                        </td>


                        <td style="padding: 10px; border: 1px solid #ddd;">

                            <?php
                            echo htmlspecialchars(
                                $notification["user_name"] ?? "Unknown"
                            );
                            ?>

                            <br>

                            <small>
                                <?php
                                echo htmlspecialchars(
                                    $notification["user_email"] ?? ""
                                );
                                ?>
                            </small>

                        </td>


                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <?php
                            echo htmlspecialchars(
                                $notification["title"]
                            );
                            ?>
                        </td>


                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <?php
                            echo htmlspecialchars(
                                $notification["message"]
                            );
                            ?>
                        </td>


                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <?php
                            echo htmlspecialchars(
                                $notification["type"]
                            );
                            ?>
                        </td>


                        <td style="padding: 10px; border: 1px solid #ddd;">

                            <?php
                            if ($notification["is_read"] == 1) {
                                echo "Read";
                            } else {
                                echo "Unread";
                            }
                            ?>

                        </td>


                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <?php
                            echo htmlspecialchars(
                                $notification["created_at"]
                            );
                            ?>
                        </td>


                        <td style="padding: 10px; border: 1px solid #ddd;">

                            <?php if ($notification["is_read"] == 0) { ?>

                                <form
                                    method="POST"
                                    action="mark-read.php"
                                    style="display: inline;"
                                >

                                    <input
                                        type="hidden"
                                        name="notification_id"
                                        value="<?php echo $notification["notification_id"]; ?>"
                                    >

                                    <button type="submit">
                                        Mark Read
                                    </button>

                                </form>

                            <?php } ?>


                            <form
                                method="POST"
                                action="delete.php"
                                style="display: inline;"
                            >

                                <input
                                    type="hidden"
                                    name="notification_id"
                                    value="<?php echo $notification["notification_id"]; ?>"
                                >

                                <button type="submit">
                                    Delete
                                </button>

                            </form>

                        </td>

                    </tr>

            

            </tbody>
                    <?php } ?>

    <?php } else { ?>

        <tr>

            <td
                colspan="8"
                style="
                    padding: 20px;
                    text-align: center;
                    border: 1px solid #ddd;
                "
            >
                No notifications found.
            </td>

        </tr>

    <?php } ?>

</tbody>

        </table>

    </div>

</main>

</div>
<?php

require_once "../../includes/footer.php";

?>
