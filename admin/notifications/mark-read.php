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


/*
|--------------------------------------------------------------------------
| Only Allow POST
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    redirect("index.php");
}


$notification_id = intval(
    $_POST["notification_id"] ?? 0
);


if ($notification_id <= 0) {
    redirect("index.php");
}


/*
|--------------------------------------------------------------------------
| Get Notification
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT
        notification_id,
        user_id,
        title,
        is_read
     FROM notifications
     WHERE notification_id = ?"
);

$stmt->bind_param(
    "i",
    $notification_id
);

$stmt->execute();

$result = $stmt->get_result();

$notification = $result->fetch_assoc();

$stmt->close();


if (!$notification) {
    redirect("index.php");
}


/*
|--------------------------------------------------------------------------
| Mark Notification As Read
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "UPDATE notifications
     SET is_read = 1
     WHERE notification_id = ?"
);

$stmt->bind_param(
    "i",
    $notification_id
);


if ($stmt->execute()) {

    /*
    |--------------------------------------------------------------------------
    | Only Log When It Was Previously Unread
    |--------------------------------------------------------------------------
    */

    if ($notification["is_read"] == 0) {

        $description =
            "Marked notification #" .
            $notification_id .
            " belonging to user #" .
            $notification["user_id"] .
            " as read. Title: " .
            $notification["title"];


        logAdminAction(
            getUserId(),
            "mark_notification_read",
            "notification",
            $notification_id,
            $description
        );
    }
}


$stmt->close();


redirect("index.php");

?>