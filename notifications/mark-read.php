```php
<?php

require_once "../config/config.php";


if (!isLoggedIn()) {
    redirect("../authentication/login.php");
}

$user_id = getUserId();


// Check notification ID
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    redirect("index.php");
}

$notification_id = intval($_GET["id"]);


// Mark only the logged-in user's notification as read
$stmt = $conn->prepare(
    "UPDATE notifications
     SET is_read = 1
     WHERE notification_id = ?
     AND user_id = ?"
);

$stmt->bind_param(
    "ii",
    $notification_id,
    $user_id
);

$stmt->execute();

$stmt->close();


redirect("index.php");

?>
```
