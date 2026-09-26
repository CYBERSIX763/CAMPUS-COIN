
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


$tip_id = intval($_POST["tip_id"] ?? 0);


if ($tip_id <= 0) {
    redirect("index.php");
}


/*
|--------------------------------------------------------------------------
| Get Current Status
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT is_active
     FROM saving_tips
     WHERE tip_id = ?"
);

$stmt->bind_param(
    "i",
    $tip_id
);

$stmt->execute();

$result = $stmt->get_result();

$tip = $result->fetch_assoc();

$stmt->close();


if (!$tip) {
    redirect("index.php");
}


/*
|--------------------------------------------------------------------------
| Toggle Status
|--------------------------------------------------------------------------
*/

$new_status = ($tip["is_active"] == 1)
    ? 0
    : 1;


$stmt = $conn->prepare(
    "UPDATE saving_tips
     SET is_active = ?
     WHERE tip_id = ?"
);

$stmt->bind_param(
    "ii",
    $new_status,
    $tip_id
);

if ($stmt->execute()) {

            $stmt->close();

          

            redirect("index.php");

        } else {

            $error = "Unable to update saving tip.";

            $stmt->close();
        }


redirect("index.php");

?>
