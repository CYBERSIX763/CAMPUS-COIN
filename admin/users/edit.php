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

$user_id = intval($_GET["id"] ?? 0);

$error = "";


/*
|--------------------------------------------------------------------------
| Validate User ID
|--------------------------------------------------------------------------
*/

if ($user_id <= 0) {
    redirect("index.php");
}


/*
|--------------------------------------------------------------------------
| Get User
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

$user = $result->fetch_assoc();

$stmt->close();


if (!$user) {
    redirect("index.php");
}


/*
|--------------------------------------------------------------------------
| Update User
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $role = trim($_POST["role"] ?? "");


    /*
    |----------------------------------------------------------------------
    | Validate
    |----------------------------------------------------------------------
    */

    if ($name == "" || $email == "") {

        $error = "Name and email are required.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif ($role != "student" && $role != "admin") {

        $error = "Invalid user role.";

    } else {


        /*
        |------------------------------------------------------------------
        | Check Duplicate Email
        |------------------------------------------------------------------
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
            |--------------------------------------------------------------
            | Update
            |--------------------------------------------------------------
            */

            $update_stmt = $conn->prepare(
                "UPDATE users
                 SET
                    name = ?,
                    email = ?,
                    role = ?,
                    updated_at = NOW()
                 WHERE user_id = ?"
            );

            $update_stmt->bind_param(
                "sssi",
                $name,
                $email,
                $role,
                $user_id
            );

            $update_stmt->execute();

            $update_stmt->close();


            /*
            |--------------------------------------------------------------
            | If Admin Changed Their Own Role
            |--------------------------------------------------------------
            */

            if ($user_id == getUserId()) {

                $_SESSION["user_role"] = $role;

                if ($role != "admin") {
                    redirect("../../dashboard/index.php");
                }
            }


            redirect("index.php");
        }
    }
}


$page_title = "Edit User";


require_once "../../includes/header.php";
require_once "../../includes/navbar.php";
?>

<div class="admin-layout">

<?php
require_once "../sidebar.php";
?>



<main class="main-content">

    <h1 class="page-title">
        Edit User
    </h1>


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

        <form method="POST">


            <div style="margin-bottom: 15px;">

                <label>
                    Name
                </label>

                <br>

                <input
                    type="text"
                    name="name"
                    value="<?php echo htmlspecialchars($user["name"]); ?>"
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
                    value="<?php echo htmlspecialchars($user["email"]); ?>"
                    required
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                    "
                >

            </div>


            <div style="margin-bottom: 20px;">

                <label>
                    Role
                </label>

                <br>

                <select
                    name="role"
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                    "
                >

                    <option
                        value="student"
                        <?php if ($user["role"] == "student") echo "selected"; ?>
                    >
                        Student
                    </option>

                    <option
                        value="admin"
                        <?php if ($user["role"] == "admin") echo "selected"; ?>
                    >
                        Admin
                    </option>

                </select>

            </div>


            <button
                type="submit"
                class="btn btn-primary"
            >
                Save Changes
            </button>


            <a
                href="index.php"
                style="margin-left: 10px;"
            >
                Cancel
            </a>

        </form>

    </div>

</main>

    
</div>
<?php

require_once "../../includes/footer.php";

?>
```
