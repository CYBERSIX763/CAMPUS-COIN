<?php

require_once "../config/config.php";

$page_title = "Register";

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Check empty fields
    if ($name == "" || $email == "" || $password == "" || $confirm_password == "") {

        $error = "Please fill in all fields.";

    }

    // Check email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    }

    // Check password length
    elseif (strlen($password) < 6) {

        $error = "Password must be at least 6 characters.";

    }

    // Check passwords
    elseif ($password != $confirm_password) {

        $error = "Passwords do not match.";

    }

    else {

        // Check if email already exists
        $stmt = $conn->prepare(
            "SELECT user_id FROM users WHERE email = ?"
        );

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $error = "This email is already registered.";

        } else {

            // Hash password
            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Insert user
            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, password_hash)
                 VALUES (?, ?, ?)"
            );

            $stmt->bind_param(
                "sss",
                $name,
                $email,
                $hashed_password
            );

            if ($stmt->execute()) {

                $success = "Registration successful!";

                // Clear form values
                $name = "";
                $email = "";

            } else {

                $error = "Registration failed. Please try again.";

            }

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
            Create Your Account
        </h1>

        <p class="page-description">
            Register for Campus Coin.
        </p>

        <?php if ($success != "") { ?>

            <div style="
                background-color: #dcfce7;
                color: #166534;
                padding: 12px;
                margin-bottom: 20px;
                border-radius: 6px;
            ">
                <?php echo $success; ?>
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
                <?php echo $error; ?>
            </div>

        <?php } ?>

        <form method="POST">

            <div style="margin-bottom: 15px;">

                <label>
                    Name
                </label>

                <br>

                <input
                    type="text"
                    name="name"
                    value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>"
                    style="width: 100%; padding: 10px; margin-top: 5px;"
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
                    value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                    style="width: 100%; padding: 10px; margin-top: 5px;"
                >

            </div>

            <div style="margin-bottom: 15px;">

                <label>
                    Password
                </label>

                <br>

                <input
                    type="password"
                    name="password"
                    style="width: 100%; padding: 10px; margin-top: 5px;"
                >

            </div>

            <div style="margin-bottom: 20px;">

                <label>
                    Confirm Password
                </label>

                <br>

                <input
                    type="password"
                    name="confirm_password"
                    style="width: 100%; padding: 10px; margin-top: 5px;"
                >

            </div>

            <button
                type="submit"
                class="btn btn-primary"
            >
                Register
            </button>

        </form>

    </div>

</div>

<?php

require_once "../includes/footer.php";

?>