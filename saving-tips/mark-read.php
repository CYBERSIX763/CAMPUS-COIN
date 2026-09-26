
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


$user_id = getUserId();


/*
|--------------------------------------------------------------------------
| Check Tip ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    redirect("index.php");
}


$tip_id = intval($_GET["id"]);


/*
|--------------------------------------------------------------------------
| Mark Tip as Read
|--------------------------------------------------------------------------
|
| We also check user_id so one user cannot change
| another user's tip record.
|
*/

$stmt = $conn->prepare(
    "UPDATE user_tips
     SET is_read = 1
     WHERE user_id = ?
     AND tip_id = ?"
);

$stmt->bind_param(
    "ii",
    $user_id,
    $tip_id
);

$stmt->execute();

$stmt->close();


redirect("index.php");

?>
