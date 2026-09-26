<nav class="navbar">

    <div class="navbar-brand">
        <a href="<?php echo BASE_URL; ?>dashboard/index.php">
            Campus Coin
        </a>
    </div>

    <div class="navbar-user">

        <?php if (isLoggedIn()) { ?>

            Welcome,
            <?php echo htmlspecialchars($_SESSION["user_name"]); ?>

        <?php } ?>

    </div>

</nav>