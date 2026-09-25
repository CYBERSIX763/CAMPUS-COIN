```php
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


$user_id = intval($_POST["user_id"] ?? 0);


/*
|--------------------------------------------------------------------------
| Validate User
|--------------------------------------------------------------------------
*/

if ($user_id <= 0) {
    redirect("index.php");
}


/*
|--------------------------------------------------------------------------
| Prevent Admin From Deactivating Their Own Account
|--------------------------------------------------------------------------
*/

if ($user_id == getUserId()) {
    redirect("index.php");
}


/*
|--------------------------------------------------------------------------
| Get Current Status
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT is_active
     FROM users
     WHERE user_id = ?"
);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

$stmt->close();


if (!$user) {
    redirect("index.php");
}


/*
|--------------------------------------------------------------------------
| Toggle Status
|--------------------------------------------------------------------------
*/

$new_status = ($user["is_active"] == 1) ? 0 : 1;


$stmt = $conn->prepare(
    "UPDATE users
     SET
        is_active = ?,
        updated_at = NOW()
     WHERE user_id = ?"
);

$stmt->bind_param(
    "ii",
    $new_status,
    $user_id
);

$stmt->execute();

$stmt->close();


redirect("index.php");

?>
```
