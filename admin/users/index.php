
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


$page_title = "User Management";


/*
|--------------------------------------------------------------------------
| Get Users
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT
        user_id,
        name,
        email,
        role,
        is_active,
        created_at,
        updated_at
     FROM users
     ORDER BY user_id ASC"
);

$stmt->execute();

$users = $stmt->get_result();

$stmt->close();

require_once "../../includes/header.php";
require_once "../../includes/navbar.php";
?>

<div class="admin-layout">

<?php
require_once "../sidebar.php";
?>




<main class="main-content">

    <h1 class="page-title">
        User Management
    </h1>

    <p class="page-description">
        Manage Campus Coin user accounts.
    </p>


    <div class="card">

        <table
            style="
                width: 100%;
                border-collapse: collapse;
            "
        >

            <thead>
                <tbody>

    <?php if ($users->num_rows > 0) { ?>

        <?php while ($user = $users->fetch_assoc()) { ?>

            <tr>

                <tr>

                    <th style="padding: 10px; border: 1px solid #ddd;">
                        ID
                    </th>

                    <th style="padding: 10px; border: 1px solid #ddd;">
                        Name
                    </th>

                    <th style="padding: 10px; border: 1px solid #ddd;">
                        Email
                    </th>

                    <th style="padding: 10px; border: 1px solid #ddd;">
                        Role
                    </th>

                    <th style="padding: 10px; border: 1px solid #ddd;">
                        Status
                    </th>

                    <th style="padding: 10px; border: 1px solid #ddd;">
                        Actions
                    </th>

                </tr>

            </thead>


            <tbody>

                <?php while ($user = $users->fetch_assoc()) { ?>

                    <tr>

                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <?php echo $user["user_id"]; ?>
                        </td>


                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <?php echo htmlspecialchars($user["name"]); ?>
                        </td>


                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <?php echo htmlspecialchars($user["email"]); ?>
                        </td>


                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <?php echo htmlspecialchars($user["role"]); ?>
                        </td>


                        <td style="padding: 10px; border: 1px solid #ddd;">

                            <?php if ($user["is_active"] == 1) { ?>

                                Active

                            <?php } else { ?>

                                Inactive

                            <?php } ?>

                        </td>


                        <td style="padding: 10px; border: 1px solid #ddd;">

                            <a
                                href="edit.php?id=<?php echo $user["user_id"]; ?>"
                            >
                                Edit
                            </a>


                            <?php if ($user["user_id"] != getUserId()) { ?>

                                |
                                
                                <form
                                    method="POST"
                                    action="toggle-status.php"
                                    style="display: inline;"
                                >

                                    <input
                                        type="hidden"
                                        name="user_id"
                                        value="<?php echo $user["user_id"]; ?>"
                                    >
                                    

                                    <button type="submit">

                                        <?php
                                        echo ($user["is_active"] == 1)
                                            ? "Deactivate"
                                            : "Activate";
                                        ?>

                                    </button>

                                </form>

                            <?php } ?>

                        </td>

                    </tr>

                <?php } ?>

            </tbody>
                    <?php } ?>

    <?php } else { ?>

        <tr>

            <td
                colspan="6"
                style="
                    padding: 20px;
                    text-align: center;
                    border: 1px solid #ddd;
                "
            >
                No users found.
            </td>

        </tr>

    <?php } ?>

        </table>

    </div>

</main>

</div>
<?php

require_once "../../includes/footer.php";

?>

