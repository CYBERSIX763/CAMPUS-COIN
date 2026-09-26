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


$page_title = "Admin Profile";

$user_id = getUserId();

$error = "";
$success = "";


/*
|--------------------------------------------------------------------------
| Get Current Admin
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT
        user_id,
        name,
        email,
        role,
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

$admin = $result->fetch_assoc();

$stmt->close();


if (!$admin) {
    logoutUser();
    redirect("../../authentication/login.php");
}


/*
|--------------------------------------------------------------------------
| Update Profile
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");


    /*
    |--------------------------------------------------------------------------
    | Validate
    |--------------------------------------------------------------------------
    */

    if ($name == "" || $email == "") {

        $error = "Name and email are required.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } else {


        /*
        |--------------------------------------------------------------------------
        | Check Duplicate Email
        |--------------------------------------------------------------------------
        */

        $check_stmt = $conn->prepare(
            "SELECT user_id
             FROM users
             WHERE email = ?
             AND user_id != ?"
        );

        $check_stmt->bind_param(
            "si",
            $email,
            $user_id
        );

        $check_stmt->execute();

        $existing = $check_stmt->get_result();

        $check_stmt->close();


        if ($existing->num_rows > 0) {

            $error = "That email address is already being used.";

        } else {


            /*
            |--------------------------------------------------------------------------
            | Update Admin Profile
            |--------------------------------------------------------------------------
            */

            $update_stmt = $conn->prepare(
                "UPDATE users
                 SET
                    name = ?,
                    email = ?,
                    updated_at = NOW()
                 WHERE user_id = ?"
            );

            $update_stmt->bind_param(
                "ssi",
                $name,
                $email,
                $user_id
            );


            if ($update_stmt->execute()) {

                /*
                |--------------------------------------------------------------------------
                | Update Session Name
                |--------------------------------------------------------------------------
                */

                $_SESSION["user_name"] = $name;


                /*
                |--------------------------------------------------------------------------
                | Admin Log
                |--------------------------------------------------------------------------
                */

                logAdminAction(
                    $user_id,
                    "update_profile",
                    "user",
                    $user_id,
                    "Updated their own admin profile."
                );


                $success = "Profile updated successfully.";


                /*
                |--------------------------------------------------------------------------
                | Update Displayed Data
                |--------------------------------------------------------------------------
                */

                $admin["name"] = $name;
                $admin["email"] = $email;

            } else {

                $error = "Failed to update profile.";
            }


            $update_stmt->close();
        }
    }
}


/*
|--------------------------------------------------------------------------
| Page
|--------------------------------------------------------------------------
*/

require_once "../../includes/header.php";
require_once "../../includes/navbar.php";

?>

<div class="admin-layout">

<?php
require_once "../sidebar.php";
?>


<main class="main-content">

    <h1 class="page-title">
        Admin Profile
    </h1>

    <p class="page-description">
        Manage your administrator account information.
    </p>


    <?php if ($success != "") { ?>

        <div style="
            background-color: #dcfce7;
            color: #166534;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
        ">
            <?php echo htmlspecialchars($success); ?>
        </div>

    <?php } ?>


    <?php if ($error != "") { ?>

        <div style="
            background-color: #fee2e2;
            color: #991b1b;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
        ">
            <?php echo htmlspecialchars($error); ?>
        </div>

    <?php } ?>


    <div class="card">

        <h2>
            Account Information
        </h2>

        <br>


        <form method="POST">


            <div style="margin-bottom: 15px;">

                <label>
                    Name
                </label>

                <br>

                <input
                    type="text"
                    name="name"
                    value="<?php echo htmlspecialchars($admin["name"]); ?>"
                    required
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                    "
                >

            </div>


            <div style="margin-bottom: 15px;">

                <label>
                    Email
                </label>

                <br>

                <input
                    type="email"
                    name="email"
                    value="<?php echo htmlspecialchars($admin["email"]); ?>"
                    required
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                    "
                >

            </div>


            <div style="margin-bottom: 15px;">

                <label>
                    Role
                </label>

                <br>

                <input
                    type="text"
                    value="Administrator"
                    readonly
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                        background-color: #f3f4f6;
                    "
                >

            </div>


            <div style="margin-bottom: 20px;">

                <label>
                    Account Status
                </label>

                <br>

                <input
                    type="text"
                    value="<?php echo ($admin["is_active"] == 1) ? "Active" : "Inactive"; ?>"
                    readonly
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                        background-color: #f3f4f6;
                    "
                >

            </div>


            <button
                type="submit"
                class="btn btn-primary"
            >
                Save Changes
            </button>

        </form>

    </div>

</main>

</div>


<?php

require_once "../../includes/footer.php";

?>