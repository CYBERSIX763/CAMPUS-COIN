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
    "SELECT
        name,
        is_active
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

$old_status = intval($user["is_active"]);

$new_status = ($old_status == 1) ? 0 : 1;


/*
|--------------------------------------------------------------------------
| Update Status
|--------------------------------------------------------------------------
*/

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


if ($stmt->execute()) {

    /*
    |--------------------------------------------------------------------------
    | Create Admin Log
    |--------------------------------------------------------------------------
    */

    if ($new_status == 1) {

        $action = "activate_user";

        $description =
            "Activated user #" .
            $user_id .
            " (" .
            $user["name"] .
            ").";

    } else {

        $action = "deactivate_user";

        $description =
            "Deactivated user #" .
            $user_id .
            " (" .
            $user["name"] .
            ").";
    }


    logAdminAction(
        getUserId(),
        $action,
        "user",
        $user_id,
        $description
    );
}


$stmt->close();


redirect("index.php");

?>