<?php

require_once "../config/config.php";

// Make sure user is logged in
if (!isLoggedIn()) {
    redirect("../authentication/login.php");
}

$page_title = "Dashboard";

require_once "../includes/header.php";
require_once "../includes/navbar.php";
require_once "../includes/sidebar.php";

?>

<main class="main-content">

    <h1 class="page-title">
        Dashboard
    </h1>

    <p class="page-description">
        Welcome to Campus Coin.
    </p>

    <div class="card">

        <h2>
            Welcome, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>!
        </h2>

        <p>
            You are successfully logged in.
        </p>

        <br>

        <p>
            User ID:
            <?php echo htmlspecialchars($_SESSION["user_id"]); ?>
        </p>

        <p>
            Role:
            <?php echo htmlspecialchars($_SESSION["user_role"]); ?>
        </p>

        <br>

        <a
            href="../authentication/logout.php"
            class="btn btn-primary"
        >
            Logout
        </a>

    </div>

</main>

<?php

require_once "../includes/footer.php";

?>