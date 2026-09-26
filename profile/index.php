
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


$page_title = "My Profile";

$user_id = getUserId();

$error = "";
$success = "";


/*
|--------------------------------------------------------------------------
| Get User Information
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT
        u.name,
        u.email,
        up.phone,
        up.profile_picture,
        up.currency,
        up.monthly_income_target,
        up.monthly_saving_target
     FROM users u
     LEFT JOIN user_profiles up
        ON u.user_id = up.user_id
     WHERE u.user_id = ?"
);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Set Empty Values
|--------------------------------------------------------------------------
*/

$phone = $user["phone"] ?? "";
$profile_picture = $user["profile_picture"] ?? "";
$currency = $user["currency"] ?? "PKR";
$monthly_income_target = $user["monthly_income_target"] ?? "";
$monthly_saving_target = $user["monthly_saving_target"] ?? "";


require_once "../includes/header.php";
require_once "../includes/navbar.php";
require_once "../includes/sidebar.php";

?>

<main class="main-content">

    <h1 class="page-title">
        My Profile
    </h1>

    <p class="page-description">
        View and update your personal information.
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


    <div class="card">

        <h2>
            Account Information
        </h2>

        <p>
            <strong>Name:</strong>
            <?php echo htmlspecialchars($user["name"]); ?>
        </p>

        <p>
            <strong>Email:</strong>
            <?php echo htmlspecialchars($user["email"]); ?>
        </p>

    </div>


    <br>


    <div class="card">

        <h2>
            Profile Details
        </h2>


        <form
            method="POST"
            action="update.php"
        >

            <div style="margin-bottom: 15px;">

                <label>
                    Phone
                </label>

                <br>

                <input
                    type="text"
                    name="phone"
                    value="<?php echo htmlspecialchars($phone); ?>"
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                    "
                >

            </div>


            <div style="margin-bottom: 15px;">

                <label>
                    Profile Picture
                </label>

                <br>

                <input
                    type="text"
                    name="profile_picture"
                    value="<?php echo htmlspecialchars($profile_picture); ?>"
                    placeholder="Image path or filename"
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                    "
                >

            </div>


            <div style="margin-bottom: 15px;">

                <label>
                    Currency
                </label>

                <br>

                <select
                    name="currency"
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                    "
                >

                    <option
                        value="PKR"
                        <?php if ($currency == "PKR") echo "selected"; ?>
                    >
                        PKR
                    </option>

                    <option
                        value="USD"
                        <?php if ($currency == "USD") echo "selected"; ?>
                    >
                        USD
                    </option>

                    <option
                        value="EUR"
                        <?php if ($currency == "EUR") echo "selected"; ?>
                    >
                        EUR
                    </option>

                    <option
                        value="GBP"
                        <?php if ($currency == "GBP") echo "selected"; ?>
                    >
                        GBP
                    </option>

                </select>

            </div>


            <div style="margin-bottom: 15px;">

                <label>
                    Monthly Income Target
                </label>

                <br>

                <input
                    type="number"
                    name="monthly_income_target"
                    step="0.01"
                    min="0"
                    value="<?php echo htmlspecialchars($monthly_income_target); ?>"
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                    "
                >

            </div>


            <div style="margin-bottom: 20px;">

                <label>
                    Monthly Saving Target
                </label>

                <br>

                <input
                    type="number"
                    name="monthly_saving_target"
                    step="0.01"
                    min="0"
                    value="<?php echo htmlspecialchars($monthly_saving_target); ?>"
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
                Save Profile
            </button>

        </form>

    </div>

</main>


<?php

require_once "../includes/footer.php";

?>
