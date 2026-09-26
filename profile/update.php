
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
| Only Allow POST
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    redirect("index.php");
}


/*
|--------------------------------------------------------------------------
| Get Form Data
|--------------------------------------------------------------------------
*/

$phone = trim($_POST["phone"] ?? "");

$profile_picture = trim(
    $_POST["profile_picture"] ?? ""
);

$currency = trim(
    $_POST["currency"] ?? "PKR"
);

$monthly_income_target = trim(
    $_POST["monthly_income_target"] ?? ""
);

$monthly_saving_target = trim(
    $_POST["monthly_saving_target"] ?? ""
);


/*
|--------------------------------------------------------------------------
| Basic Validation
|--------------------------------------------------------------------------
*/

if ($monthly_income_target == "") {
    $monthly_income_target = 0;
}

if ($monthly_saving_target == "") {
    $monthly_saving_target = 0;
}


if (!is_numeric($monthly_income_target)) {
    redirect("index.php");
}


if (!is_numeric($monthly_saving_target)) {
    redirect("index.php");
}


$monthly_income_target = floatval(
    $monthly_income_target
);

$monthly_saving_target = floatval(
    $monthly_saving_target
);


/*
|--------------------------------------------------------------------------
| Check Whether Profile Already Exists
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT profile_id
     FROM user_profiles
     WHERE user_id = ?"
);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

$profile_exists = $result->num_rows > 0;

$stmt->close();


/*
|--------------------------------------------------------------------------
| Update Existing Profile
|--------------------------------------------------------------------------
*/

if ($profile_exists) {

    $stmt = $conn->prepare(
        "UPDATE user_profiles
         SET
            phone = ?,
            profile_picture = ?,
            currency = ?,
            monthly_income_target = ?,
            monthly_saving_target = ?,
            updated_at = NOW()
         WHERE user_id = ?"
    );

    $stmt->bind_param(
        "sssddi",
        $phone,
        $profile_picture,
        $currency,
        $monthly_income_target,
        $monthly_saving_target,
        $user_id
    );

}


/*
|--------------------------------------------------------------------------
| Create Profile If It Does Not Exist
|--------------------------------------------------------------------------
*/

else {

    $stmt = $conn->prepare(
        "INSERT INTO user_profiles
        (
            user_id,
            phone,
            profile_picture,
            currency,
            monthly_income_target,
            monthly_saving_target
        )
        VALUES (?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "isssdd",
        $user_id,
        $phone,
        $profile_picture,
        $currency,
        $monthly_income_target,
        $monthly_saving_target
    );

}


/*
|--------------------------------------------------------------------------
| Execute
|--------------------------------------------------------------------------
*/

$stmt->execute();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Return To Profile
|--------------------------------------------------------------------------
*/

redirect("index.php");

?>
