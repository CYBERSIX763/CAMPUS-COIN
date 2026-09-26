
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


$page_title = "Saving Tips";

$user_id = getUserId();


/*
|--------------------------------------------------------------------------
| Get Active Saving Tips
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT
        tip_id,
        title,
        content,
        category_id,
        minimum_impact_score
     FROM saving_tips
     WHERE is_active = 1
     ORDER BY tip_id ASC"
);

$stmt->execute();

$tips = $stmt->get_result();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Create user_tips record when user sees a tip
|--------------------------------------------------------------------------
*/

while ($tip = $tips->fetch_assoc()) {

    $tip_id = $tip["tip_id"];


    // Check if this user has already received this tip
    $check_stmt = $conn->prepare(
        "SELECT user_tip_id
         FROM user_tips
         WHERE user_id = ?
         AND tip_id = ?
         LIMIT 1"
    );

    $check_stmt->bind_param(
        "ii",
        $user_id,
        $tip_id
    );

    $check_stmt->execute();

    $existing = $check_stmt->get_result();

    $check_stmt->close();


    /*
    |--------------------------------------------------------------------------
    | Create user tip record
    |--------------------------------------------------------------------------
    */

    if ($existing->num_rows == 0) {

        $insert_stmt = $conn->prepare(
            "INSERT INTO user_tips
            (
                user_id,
                tip_id,
                is_read,
                is_dismissed,
                is_pinned,
                shown_at
            )
            VALUES (?, ?, 0, 0, 0, NOW())"
        );

        $insert_stmt->bind_param(
            "ii",
            $user_id,
            $tip_id
        );

        $insert_stmt->execute();

        $insert_stmt->close();
    }
}


/*
|--------------------------------------------------------------------------
| Get tips again for displaying
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT
        s.tip_id,
        s.title,
        s.content,
        s.category_id,
        s.minimum_impact_score,
        u.is_read,
        u.is_dismissed,
        u.is_pinned
     FROM saving_tips s
     INNER JOIN user_tips u
        ON s.tip_id = u.tip_id
        AND u.user_id = ?
     WHERE s.is_active = 1
     AND u.is_dismissed = 0
     ORDER BY u.is_pinned DESC, s.tip_id ASC"
);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$user_tips = $stmt->get_result();

$stmt->close();


require_once "../includes/header.php";
require_once "../includes/navbar.php";
require_once "../includes/sidebar.php";

?>

<main class="main-content">

    <h1 class="page-title">
        Saving Tips
    </h1>

    <p class="page-description">
        Simple tips to help you manage your money better.
    </p>


    <div class="card">

        <?php if ($user_tips->num_rows > 0) { ?>

            <?php while ($tip = $user_tips->fetch_assoc()) { ?>

                <div style="
                    border: 1px solid #ddd;
                    padding: 15px;
                    margin-bottom: 15px;
                    border-radius: 6px;
                ">

                    <h2>
                        <?php echo htmlspecialchars($tip["title"]); ?>

                        <?php if ($tip["is_pinned"] == 1) { ?>

                            <span style="
                                font-size: 12px;
                                padding: 4px 7px;
                                background-color: #eee;
                                border-radius: 4px;
                            ">
                                Pinned
                            </span>

                        <?php } ?>

                    </h2>


                    <p>
                        <?php echo htmlspecialchars($tip["content"]); ?>
                    </p>


                    <?php if ($tip["is_read"] == 0) { ?>

                        <span style="
                            font-size: 12px;
                            padding: 4px 7px;
                            background-color: #e5e7eb;
                            border-radius: 4px;
                        ">
                            New
                        </span>

                    <?php } ?>


                    <div style="margin-top: 10px;">

                        <a
                            href="mark-read.php?id=<?php echo $tip["tip_id"]; ?>"
                            class="btn"
                        >
                            Mark as Read
                        </a>


                        <a
                            href="dismiss.php?id=<?php echo $tip["tip_id"]; ?>"
                            class="btn"
                        >
                            Dismiss
                        </a>

                    </div>

                </div>

            <?php } ?>

        <?php } else { ?>

            <p>
                No saving tips available right now.
            </p>

        <?php } ?>

    </div>

</main>


<?php

require_once "../includes/footer.php";

?>
