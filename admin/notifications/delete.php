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
| Get Notification Before Deleting
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT
        notification_id,
        user_id,
        title,
        type
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
| Delete Notification
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "DELETE FROM notifications
     WHERE notification_id = ?"
);

$stmt->bind_param(
    "i",
    $notification_id
);


if ($stmt->execute()) {

    /*
    |--------------------------------------------------------------------------
    | Admin Log
    |--------------------------------------------------------------------------
    */

    $description =
        "Deleted notification #" .
        $notification_id .
        " belonging to user #" .
        $notification["user_id"] .
        ". Title: " .
        $notification["title"];


    logAdminAction(
        getUserId(),
        "delete_notification",
        "notification",
        $notification_id,
        $description
    );
}


$stmt->close();


redirect("index.php");

?>