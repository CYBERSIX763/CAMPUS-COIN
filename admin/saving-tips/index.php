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


$page_title = "Saving Tips";


/*
|--------------------------------------------------------------------------
| Get Saving Tips
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT
        tip_id,
        title,
        content,
        category_id,
        minimum_impact_score,
        is_active,
        created_at
     FROM saving_tips
     ORDER BY tip_id DESC"
);

$stmt->execute();

$tips = $stmt->get_result();

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
        Saving Tips
    </h1>

    <p class="page-description">
        Manage saving tips shown to users.
    </p>


    <p>
        <a href="add.php">
            Add Saving Tip
        </a>
    </p>


    <div class="card">

        <table style="width: 100%; border-collapse: collapse;">

            <thead>

                <tr>

                    <th style="padding: 10px; border: 1px solid #ddd;">
                        ID
                    </th>

                    <th style="padding: 10px; border: 1px solid #ddd;">
                        Title
                    </th>

                    <th style="padding: 10px; border: 1px solid #ddd;">
                        Content
                    </th>

                    <th style="padding: 10px; border: 1px solid #ddd;">
                        Category ID
                    </th>

                    <th style="padding: 10px; border: 1px solid #ddd;">
                        Minimum Impact Score
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

                <?php while ($tip = $tips->fetch_assoc()) { ?>

                    <tr>

                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <?php echo $tip["tip_id"]; ?>
                        </td>


                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <?php echo htmlspecialchars($tip["title"]); ?>
                        </td>


                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <?php echo htmlspecialchars($tip["content"]); ?>
                        </td>


                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <?php
                            echo $tip["category_id"] === null
                                ? "All"
                                : $tip["category_id"];
                            ?>
                        </td>


                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <?php echo $tip["minimum_impact_score"]; ?>
                        </td>


                        <td style="padding: 10px; border: 1px solid #ddd;">

                            <?php
                            if ($tip["is_active"] == 1) {
                                echo "Active";
                            } else {
                                echo "Inactive";
                            }
                            ?>

                        </td>


                        <td style="padding: 10px; border: 1px solid #ddd;">

                            <a href="edit.php?id=<?php echo $tip["tip_id"]; ?>">
                                Edit
                            </a>

                            |

                            <form
                                method="POST"
                                action="toggle-status.php"
                                style="display: inline;"
                            >

                                <input
                                    type="hidden"
                                    name="tip_id"
                                    value="<?php echo $tip["tip_id"]; ?>"
                                >

                                <button type="submit">

                                    <?php
                                    if ($tip["is_active"] == 1) {
                                        echo "Deactivate";
                                    } else {
                                        echo "Activate";
                                    }
                                    ?>

                                </button>

                            </form>

                        </td>

                    </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>

</main>

</div>

<?php

require_once "../../includes/footer.php";

?>
```
