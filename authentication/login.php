<?php

require_once "../config/config.php";


$page_title = "Login";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Check empty fields
    if ($email == "" || $password == "") {

        $error = "Please enter your email and password.";

    } else {

        // Find user by email
        $stmt = $conn->prepare(
            "SELECT user_id, name, email, password_hash, role, is_active
             FROM users
             WHERE email = ?"
        );

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $user = $result->fetch_assoc();

            // Check account status
            if ($user["is_active"] != 1) {

                $error = "Your account is inactive.";

            }

            // Check password
            elseif (password_verify($password, $user["password_hash"])) {

                loginUser(
                    $user["user_id"],
                    $user["name"],
                    $user["role"]
                );

                redirect("../dashboard/index.php");

            } else {

                $error = "Invalid email or password.";

            }

        } else {

            $error = "Invalid email or password.";

        }

        $stmt->close();
    }
}

require_once "../includes/header.php";
require_once "../includes/navbar.php";

?>

<div class="main-content">

    <div class="card">

        <h1 class="page-title">
            Login
        </h1>

        <p class="page-description">
            Login to your Campus Coin account.
        </p>

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

        <form method="POST">

            <div style="margin-bottom: 15px;">

                <label>
                    Email
                </label>

                <br>

                <input
                    type="email"
                    name="email"
                    value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                    "
                >

            </div>

            <div style="margin-bottom: 20px;">

                <label>
                    Password
                </label>

                <br>

                <input
                    type="password"
                    name="password"
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                    "
                >

            </div>

            <button
                type="submit"
                class="btn btn-primary"
            >
                Login
            </button>

        </form>

        <br>

        <p>
            Don't have an account?
            <a href="registration.php">
                Register here
            </a>
        </p>

    </div>

</div>

<?php

require_once "../includes/footer.php";

?>