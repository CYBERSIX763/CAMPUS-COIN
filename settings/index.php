
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


$page_title = "Settings";

$user_id = getUserId();


/*
|--------------------------------------------------------------------------
| Get Current Settings
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT
        dark_mode,
        font_size
     FROM user_profiles
     WHERE user_id = ?"
);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

$settings = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Default Settings
|--------------------------------------------------------------------------
*/

$dark_mode = $settings["dark_mode"] ?? 0;
$font_size = $settings["font_size"] ?? "medium";


require_once "../includes/header.php";
require_once "../includes/navbar.php";
require_once "../includes/sidebar.php";

?>

<main class="main-content">

    <h1 class="page-title">
        Settings
    </h1>

    <p class="page-description">
        Manage your personal display preferences.
    </p>


    <div class="card">

        <h2>
            Display Settings
        </h2>


        <form
            method="POST"
            action="update.php"
        >

            <!-- Dark Mode -->

            <div style="margin-bottom: 20px;">

                <label>
                    <strong>Dark Mode</strong>
                </label>

                <br>

                <select
                    name="dark_mode"
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                    "
                >

                    <option
                        value="0"
                        <?php if ($dark_mode == 0) echo "selected"; ?>
                    >
                        Off
                    </option>

                    <option
                        value="1"
                        <?php if ($dark_mode == 1) echo "selected"; ?>
                    >
                        On
                    </option>

                </select>

            </div>


            <!-- Font Size -->

            <div style="margin-bottom: 20px;">

                <label>
                    <strong>Font Size</strong>
                </label>

                <br>

                <select
                    name="font_size"
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                    "
                >

                    <option
                        value="small"
                        <?php if ($font_size == "small") echo "selected"; ?>
                    >
                        Small
                    </option>

                    <option
                        value="medium"
                        <?php if ($font_size == "medium") echo "selected"; ?>
                    >
                        Medium
                    </option>

                    <option
                        value="large"
                        <?php if ($font_size == "large") echo "selected"; ?>
                    >
                        Large
                    </option>

                </select>

            </div>


            <button
                type="submit"
                class="btn btn-primary"
            >
                Save Settings
            </button>

        </form>

    </div>

</main>


<?php

require_once "../includes/footer.php";

?>