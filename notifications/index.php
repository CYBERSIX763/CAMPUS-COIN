```php
<?php

require_once "../config/config.php";


// User must be logged in
if (!isLoggedIn()) {
    redirect("../authentication/login.php");
}

$page_title = "Notifications";

$user_id = getUserId();


// Get user's notifications
$stmt = $conn->prepare(
    "SELECT
        notification_id,
        title,
        message,
        type,
        is_read,
        created_at
     FROM notifications
     WHERE user_id = ?
     ORDER BY created_at DESC"
);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$notifications = $stmt->get_result();

$stmt->close();


require_once "../includes/header.php";
require_once "../includes/navbar.php";
require_once "../includes/sidebar.php";

?>

<main class="main-content">

    <h1 class="page-title">
        Notifications
    </h1>

    <p class="page-description">
        View your Campus Coin alerts and updates.
    </p>


    <div class="card">

        <?php if ($notifications->num_rows > 0) { ?>

            <?php while ($notification = $notifications->fetch_assoc()) { ?>

                <div style="
                    padding: 15px;
                    margin-bottom: 12px;
                    border: 1px solid #ddd;
                    border-radius: 6px;
                    background-color:
                    <?php
                    echo ($notification["is_read"] == 0)
                        ? "#f8fafc"
                        : "#ffffff";
                    ?>;
                ">

                    <h3>
                        <?php echo htmlspecialchars($notification["title"]); ?>

                        <?php if ($notification["is_read"] == 0) { ?>

                            <span style="
                                font-size: 12px;
                                padding: 3px 7px;
                                background-color: #e5e7eb;
                                border-radius: 4px;
                            ">
                                New
                            </span>

                        <?php } ?>

                    </h3>


                    <p>
                        <?php echo htmlspecialchars($notification["message"]); ?>
                    </p>


                    <small>
                        Type:
                        <?php echo htmlspecialchars($notification["type"]); ?>

                        <br>

                        <?php echo htmlspecialchars($notification["created_at"]); ?>
                    </small>


                    <?php if ($notification["is_read"] == 0) { ?>

                        <br><br>

                        <a
                            href="mark-read.php?id=<?php echo $notification["notification_id"]; ?>"
                            class="btn"
                        >
                            Mark as Read
                        </a>

                    <?php } ?>

                </div>

            <?php } ?>

        <?php } else { ?>

            <p>
                You don't have any notifications.
            </p>

        <?php } ?>

    </div>

</main>

<?php

require_once "../includes/footer.php";

?>
```
