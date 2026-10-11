<?php

session_start();

require_once __DIR__ . '/registration/db.php';


// =====================================================
// CHECK LOGIN
// =====================================================

if (!isset($_SESSION['user_id'])) {

    header("Location: registration/login.php");
    exit();

}

$user_id = intval($_SESSION['user_id']);


// =====================================================
// AUTOMATICALLY MARK ALL VISIBLE NOTIFICATIONS AS READ
// =====================================================

$stmt = mysqli_prepare(
    $conn,
    "INSERT IGNORE INTO notification_reads
     (notification_id, user_id)
     SELECT id, ?
     FROM notifications
     WHERE status = 'Active'
     AND (target_user_id IS NULL OR target_user_id = ?)"
);

if ($stmt) {

    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $user_id,
        $user_id
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
}


// =====================================================
// GET NOTIFICATIONS
// =====================================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        n.id,
        n.title,
        n.message,
        n.type,
        n.created_at
     FROM notifications n
     WHERE n.status = 'Active'
     AND (n.target_user_id IS NULL OR n.target_user_id = ?)
     ORDER BY n.created_at DESC"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$notifications = [];

while ($row = mysqli_fetch_assoc($result)) {

    $notifications[] = $row;

}

mysqli_stmt_close($stmt);


// =====================================================
// TYPE ICON
// =====================================================

function notificationIcon($type)
{

    switch (strtolower($type)) {

        case 'event':
            return '🎉';

        case 'admission':
            return '🎓';

        case 'exam':
            return '📝';

        case 'result':
            return '📊';

        case 'important':
            return '⚠️';

        case 'notice':
            return '📢';

        default:
            return '🔔';

    }

}


// =====================================================
// TIME FORMAT
// =====================================================

function timeAgo($datetime)
{

    $time = strtotime($datetime);

    $current = time();

    $difference = $current - $time;


    if ($difference < 60) {

        return "Just now";

    }


    if ($difference < 3600) {

        return floor($difference / 60) . " minutes ago";

    }


    if ($difference < 86400) {

        return floor($difference / 3600) . " hours ago";

    }


    if ($difference < 604800) {

        return floor($difference / 86400) . " days ago";

    }


    return date("d M Y, h:i A", $time);

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Notifications | CampusConnect</title>


<style>

* {

    margin: 0;

    padding: 0;

    box-sizing: border-box;

}


body {

    font-family: Arial, sans-serif;

    background: #f4f6fb;

    color: #222;

}


/* =====================================================
   HEADER
===================================================== */

.header {

    background: linear-gradient(
        135deg,
        #4f46e5,
        #7c3aed
    );

    color: white;

    padding: 22px 35px;

    display: flex;

    justify-content: space-between;

    align-items: center;

}


.header h1 {

    font-size: 25px;

}


.header p {

    margin-top: 5px;

    opacity: 0.9;

}


.back-btn {

    text-decoration: none;

    color: white;

    background: rgba(255,255,255,0.18);

    padding: 10px 18px;

    border-radius: 8px;

}


/* =====================================================
   MAIN
===================================================== */

.container {

    max-width: 950px;

    margin: 30px auto;

    padding: 0 20px;

}


/* =====================================================
   TOP BAR
===================================================== */

.top-bar {

    margin-bottom: 20px;

}


.top-bar h2 {

    font-size: 22px;

}


/* =====================================================
   NOTIFICATION CARD
===================================================== */

.notification {

    background: white;

    border-radius: 12px;

    padding: 20px;

    margin-bottom: 14px;

    display: flex;

    gap: 16px;

    box-shadow:
        0 3px 15px rgba(0,0,0,0.06);

    border-left: 5px solid #6366f1;

    transition: 0.2s;

}


.notification:hover {

    transform: translateY(-2px);

}


/* =====================================================
   ICON
===================================================== */

.notification-icon {

    width: 48px;

    height: 48px;

    min-width: 48px;

    border-radius: 50%;

    background: #eef2ff;

    display: flex;

    justify-content: center;

    align-items: center;

    font-size: 22px;

}


/* =====================================================
   CONTENT
===================================================== */

.notification-content {

    flex: 1;

}


.notification-title {

    font-size: 18px;

    font-weight: bold;

    margin-bottom: 7px;

}


.notification-message {

    color: #555;

    line-height: 1.6;

    margin-bottom: 10px;

}


.notification-meta {

    display: flex;

    gap: 10px;

    align-items: center;

    flex-wrap: wrap;

}


.type {

    background: #e0e7ff;

    color: #3730a3;

    padding: 4px 9px;

    border-radius: 5px;

    font-size: 12px;

}


.time {

    color: #777;

    font-size: 12px;

}


/* =====================================================
   READ STATUS
===================================================== */

.read-status {

    color: #16a34a;

    font-size: 12px;

    font-weight: bold;

}


/* =====================================================
   EMPTY
===================================================== */

.empty {

    background: white;

    border-radius: 12px;

    padding: 60px 20px;

    text-align: center;

    box-shadow:
        0 3px 15px rgba(0,0,0,0.05);

}


.empty-icon {

    font-size: 55px;

    margin-bottom: 15px;

}


.empty h3 {

    margin-bottom: 8px;

}


.empty p {

    color: #777;

}


/* =====================================================
   MOBILE
===================================================== */

@media(max-width: 600px) {

    .header {

        padding: 18px;

        gap: 15px;

    }


    .header h1 {

        font-size: 21px;

    }


    .container {

        margin-top: 20px;

    }


    .notification {

        padding: 15px;

    }


    .notification-icon {

        width: 40px;

        height: 40px;

        min-width: 40px;

    }

}

</style>

</head>


<body>


<!-- =====================================================
     HEADER
===================================================== -->

<div class="header">

    <div>

        <h1>🔔 Notifications</h1>

        <p>
            CampusConnect Notifications
        </p>

    </div>


    <a
        href="dashboard.php"
        class="back-btn"
    >
        ← Dashboard
    </a>

</div>



<!-- =====================================================
     MAIN
===================================================== -->

<div class="container">


    <div class="top-bar">

        <h2>
            Your Notifications
        </h2>

    </div>



    <!-- =================================================
         NOTIFICATIONS
    ================================================= -->

    <?php if (count($notifications) > 0): ?>


        <?php foreach ($notifications as $notification): ?>


            <div class="notification">


                <!-- ICON -->

                <div class="notification-icon">

                    <?php

                    echo notificationIcon(
                        $notification['type']
                    );

                    ?>

                </div>



                <!-- CONTENT -->

                <div class="notification-content">


                    <!-- TITLE -->

                    <div class="notification-title">

                        <?php

                        echo htmlspecialchars(
                            $notification['title']
                        );

                        ?>

                    </div>



                    <!-- MESSAGE -->

                    <div class="notification-message">

                        <?php

                        echo nl2br(
                            htmlspecialchars(
                                $notification['message']
                            )
                        );

                        ?>

                    </div>



                    <!-- META -->

                    <div class="notification-meta">


                        <span class="type">

                            <?php

                            echo htmlspecialchars(
                                $notification['type']
                            );

                            ?>

                        </span>



                        <span class="time">

                            <?php

                            echo timeAgo(
                                $notification['created_at']
                            );

                            ?>

                        </span>



                        <span class="read-status">

                            ✓ Read

                        </span>


                    </div>


                </div>


            </div>


        <?php endforeach; ?>


    <?php else: ?>


        <!-- =================================================
             NO NOTIFICATIONS
        ================================================= -->

        <div class="empty">

            <div class="empty-icon">
                🔔
            </div>


            <h3>
                No Notifications
            </h3>


            <p>
                You don't have any notifications right now.
            </p>

        </div>


    <?php endif; ?>


</div>


</body>

</html>