<?php

session_start();

/*
=====================================================
CHECK LOGIN
=====================================================
*/

if (
    !isset($_SESSION['user_id']) &&
    !isset($_SESSION['username'])
) {
    header("Location: registration/login.php");
    exit();
}


/*
=====================================================
DATABASE CONNECTION
=====================================================
*/

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "campusconnect"
);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}


/*
=====================================================
GET USER
=====================================================
*/

$user = null;

if (isset($_SESSION['user_id'])) {

    $user_id = (int)$_SESSION['user_id'];

    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM users WHERE id = ? LIMIT 1"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $user_id
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $user = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
} else {

    $username_session =
        $_SESSION['username'];

    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM users WHERE username = ? LIMIT 1"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "s",
        $username_session
    );

    mysqli_stmt_execute($stmt);

    $result =
        mysqli_stmt_get_result($stmt);

    $user =
        mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
}


/*
=====================================================
USER NOT FOUND
=====================================================
*/

if (!$user) {

    session_unset();
    session_destroy();

    header("Location: registration/login.php");
    exit();
}


/*
=====================================================
USER INFORMATION
=====================================================
*/

$user_id =
    (int)$user['id'];

$username =
    $user['username'] ?? '';

$name =
    !empty($user['name'])
    ? $user['name']
    : $username;

$email =
    $user['email'] ?? '';

$mobile =
    $user['mobile'] ?? '';

$address =
    $user['address'] ?? '';

$college =
    $user['college'] ?? '';

$course =
    $user['course'] ?? '';

$year =
    $user['year'] ?? '';

$profile_image =
    $user['profile_image'] ?? '';


/*
=====================================================
PROFILE IMAGE
=====================================================
*/

$profile_path = '';

if (!empty($profile_image)) {

    $image_filename =
        basename($profile_image);

    $physical_image_path =
        __DIR__ .
        "/uploads/profile_images/" .
        $image_filename;

    if (
        file_exists($physical_image_path) &&
        is_file($physical_image_path)
    ) {

        $profile_path =
            "uploads/profile_images/" .
            rawurlencode($image_filename);
    }
}


/*
=====================================================
INITIALS
=====================================================
*/

$avatar_name =
    trim($name);

if ($avatar_name === '') {
    $avatar_name = "User";
}

$words =
    preg_split('/\s+/', $avatar_name);

if (count($words) >= 2) {

    $initials =
        strtoupper(
            substr($words[0], 0, 1) .
                substr($words[1], 0, 1)
        );
} else {

    $initials =
        strtoupper(
            substr($avatar_name, 0, 2)
        );
}


/*
=====================================================
SAFE COUNT FUNCTION
=====================================================
*/

function getCount($conn, $sql)
{
    $result = @mysqli_query($conn, $sql);

    if (!$result) {
        return 0;
    }

    $row =
        mysqli_fetch_assoc($result);

    return isset($row['total'])
        ? (int)$row['total']
        : 0;
}


/*
=====================================================
STATISTICS
=====================================================
*/

$total_users =
    getCount(
        $conn,
        "SELECT COUNT(*) AS total FROM users"
    );


$total_courses = 0;

$courses_check =
    @mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total FROM courses"
    );

if ($courses_check) {

    $course_row =
        mysqli_fetch_assoc($courses_check);

    $total_courses =
        (int)$course_row['total'];
}


/*
=====================================================
TOTAL EVENTS
=====================================================
*/

$total_events =
    getCount(
        $conn,
        "SELECT COUNT(*) AS total FROM events"
    );


/*
=====================================================
COURSE DISPLAY
=====================================================
*/

$course_display =
    !empty($course)
    ? $course
    : "N/A";


/*
=====================================================
EMAIL VERIFIED
=====================================================
*/

$email_verified = false;

if (isset($user['email_verified'])) {

    $verified_value =
        strtolower(
            trim(
                (string)$user['email_verified']
            )
        );

    $email_verified =
        in_array(
            $verified_value,
            [
                '1',
                'yes',
                'true',
                'verified'
            ],
            true
        );
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Dashboard | Yeshwant Mahavidyalaya
    </title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #f3f7fc;

            color: #173d63;
        }


        /* =========================================
   TOP BAR
========================================= */

        .topbar {

            background: #06458f;

            color: white;

            padding: 12px 5%;

            display: flex;

            justify-content: space-between;

            align-items: center;

            font-size: 14px;
        }

        .topbar i {
            margin-right: 7px;
            color: #ffc400;
        }


        /* =========================================
   HEADER
========================================= */

        .header {

            background: white;

            min-height: 85px;

            padding: 12px 4%;

            display: flex;

            align-items: center;

            justify-content: space-between;

            box-shadow:
                0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .college-info {

            display: flex;

            align-items: center;

            gap: 15px;
        }

        .college-logo {

            width: 65px;

            height: 65px;

            border-radius: 50%;

            object-fit: cover;
        }

        .college-info h1 {

            color: #06458f;

            font-size: 22px;
        }

        .college-info p {

            color: #607d99;

            margin-top: 5px;

            font-size: 13px;
        }

        .nav {

            display: flex;

            gap: 25px;
        }

        .nav a {

            color: #06458f;

            text-decoration: none;

            font-weight: bold;
        }

        .nav a:hover {
            color: #e2a900;
        }


        /* =========================================
   LAYOUT
========================================= */

        .dashboard-container {

            max-width: 1450px;

            margin: 30px auto;

            padding: 0 20px;

            display: grid;

            grid-template-columns:
                290px 1fr;

            gap: 25px;
        }


        /* =========================================
   SIDEBAR
========================================= */

        .sidebar {

            background: white;

            border-radius: 16px;

            padding: 22px;

            box-shadow:
                0 5px 20px rgba(0, 0, 0, 0.07);

            height: fit-content;
        }

        .profile-top {

            text-align: center;

            padding-bottom: 20px;

            border-bottom:
                1px solid #e3eaf2;
        }


        /* =========================================
   SIDEBAR AVATAR
========================================= */

        .avatar {

            width: 85px;

            height: 85px;

            margin: 0 auto 12px;

            border-radius: 50%;

            overflow: hidden;

            background: #123f68;

            color: white;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 28px;

            font-weight: bold;
        }

        .avatar img {

            width: 100%;

            height: 100%;

            object-fit: cover;

            display: block;
        }

        .profile-top h3 {

            font-size: 17px;

            color: #173d63;

            margin-bottom: 5px;
        }

        .profile-top p {

            color: #7190ad;

            font-size: 13px;
        }


        /* =========================================
   SIDEBAR MENU
========================================= */

        .menu {

            list-style: none;

            margin-top: 20px;
        }

        .menu li {
            margin-bottom: 6px;
        }

        .menu a {

            display: flex;

            align-items: center;

            gap: 15px;

            padding: 13px;

            border-radius: 9px;

            color: #385471;

            text-decoration: none;
        }

        .menu a:hover {

            background: #edf4fc;

            color: #06458f;
        }

        .menu a.active {

            background: #0c3558;

            color: white;
        }

        .menu i {

            width: 20px;

            text-align: center;
        }


        /* =========================================
   MAIN
========================================= */

        .main {
            min-width: 0;
        }


        /* =========================================
   WELCOME
========================================= */

        .welcome {

            background: white;

            border-radius: 16px;

            padding: 25px 30px;

            display: flex;

            justify-content: space-between;

            align-items: center;

            box-shadow:
                0 5px 20px rgba(0, 0, 0, 0.06);

            margin-bottom: 25px;
        }

        .welcome h2 {

            color: #123d67;

            font-size: 25px;
        }

        .welcome p {

            margin-top: 7px;

            color: #67839e;
        }

        .user-badge {

            background: #0d365b;

            color: white;

            padding: 10px 18px;

            border-radius: 25px;

            font-size: 14px;
        }


        /* =========================================
   STAT CARDS
========================================= */

        .stats {

            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 20px;

            margin-bottom: 25px;
        }

        .stat {

            background: white;

            border-radius: 15px;

            padding: 22px;

            display: flex;

            align-items: center;

            gap: 16px;

            box-shadow:
                0 5px 18px rgba(0, 0, 0, 0.06);
        }

        .stat-icon {

            width: 54px;

            height: 54px;

            border-radius: 13px;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 23px;
        }

        .users-icon {
            background: #e5efff;
            color: #14559a;
        }

        .course-icon {
            background: #e6f5e8;
            color: #238239;
        }

        .event-icon {
            background: #fff0dd;
            color: #ed7200;
        }

        .mycourse-icon {
            background: #f4e5f5;
            color: #83239a;
        }

        .stat h3 {

            color: #102f4e;

            font-size: 24px;

            margin-bottom: 5px;
        }

        .stat p {

            color: #708aa3;

            font-size: 13px;
        }


        /* =========================================
   PROFILE CARD
========================================= */

        .profile-card {

            background: white;

            border-radius: 16px;

            padding: 30px;

            box-shadow:
                0 5px 20px rgba(0, 0, 0, 0.06);
        }

        .profile-title {

            display: flex;

            align-items: center;

            justify-content: space-between;

            border-bottom:
                1px solid #e2e9f1;

            padding-bottom: 18px;

            margin-bottom: 15px;
        }

        .profile-title h2 {

            font-size: 21px;

            color: #123d67;
        }

        .verified {

            background: #e5f5e8;

            color: #238239;

            padding: 7px 13px;

            border-radius: 20px;

            font-size: 13px;

            font-weight: bold;
        }


        /* =========================================
   PROFILE ROW
========================================= */

        .profile-item {

            display: grid;

            grid-template-columns:
                140px 1fr;

            padding: 17px 0;

            border-bottom:
                1px solid #edf1f5;

            align-items: center;
        }

        .profile-item:last-child {
            border-bottom: none;
        }

        .label {

            color: #63819f;

            font-size: 14px;

            font-weight: bold;
        }

        .label i {

            color: #14558e;

            width: 20px;

            margin-right: 7px;
        }

        .value {

            color: #163b5d;

            font-size: 15px;
        }

        .empty {

            color: #a0afbd;

            font-style: italic;
        }


        /* =========================================
   PROFILE IMAGE
========================================= */

        .profile-image {

            width: 100px;

            height: 100px;

            border-radius: 12px;

            object-fit: cover;

            border: 3px solid #e4ebf3;

            display: block;
        }


        /* =========================================
   EDIT BUTTON
========================================= */

        .edit-btn {

            display: inline-block;

            margin-top: 20px;

            background: #06458f;

            color: white;

            text-decoration: none;

            padding: 11px 18px;

            border-radius: 8px;

            font-weight: bold;
        }

        .edit-btn:hover {
            background: #082f5e;
        }


        /* =========================================
   RESPONSIVE
========================================= */

        @media(max-width: 1100px) {

            .stats {

                grid-template-columns:
                    repeat(2, 1fr);
            }

        }

        @media(max-width: 850px) {

            .dashboard-container {

                grid-template-columns: 1fr;
            }

            .nav {

                display: none;
            }

        }

        @media(max-width: 600px) {

            .topbar {

                flex-direction: column;

                gap: 7px;
            }

            .header {

                justify-content: center;
            }

            .college-info h1 {

                font-size: 18px;
            }

            .stats {

                grid-template-columns: 1fr;
            }

            .welcome {

                flex-direction: column;

                align-items: flex-start;

                gap: 15px;
            }

            .profile-item {

                grid-template-columns: 1fr;

                gap: 8px;
            }

        }

        /* =========================================
   PROFILE IMAGE CLICK
========================================= */

        .avatar {
            cursor: pointer;
        }

        .avatar:hover {
            transform: scale(1.05);
        }


        /* =========================================
   PROFILE IMAGE MODAL
========================================= */

        .profile-modal {

            display: none;

            position: fixed;

            z-index: 9999;

            left: 0;
            top: 0;

            width: 100%;
            height: 100%;

            background: rgba(0, 0, 0, 0.85);

            align-items: center;

            justify-content: center;

            padding: 30px;
        }


        /* LARGE IMAGE */

        .profile-modal-image {

            max-width: 90%;

            max-height: 85vh;

            object-fit: contain;

            border-radius: 12px;

            box-shadow:
                0 10px 40px rgba(0, 0, 0, 0.5);

            background: white;
        }


        /* CLOSE BUTTON */

        .profile-close {

            position: absolute;

            top: 20px;

            right: 35px;

            color: white;

            font-size: 45px;

            font-weight: bold;

            cursor: pointer;

            line-height: 1;
        }

        .profile-close:hover {
            color: #ffcc00;
        }
    </style>

</head>
<!-- PROFILE IMAGE POPUP -->

<div id="profileImageModal" class="profile-modal">

    <span
        class="profile-close"
        onclick="closeProfileImage()">
        &times;
    </span>

    <img
        id="profileModalImage"
        class="profile-modal-image"
        src=""
        alt="Profile Picture">

</div>


<script>
    function openProfileImage() {

        const image =
            document.getElementById("sidebarProfileImage");

        if (!image) {
            return;
        }

        const modal =
            document.getElementById("profileImageModal");

        const modalImage =
            document.getElementById("profileModalImage");

        modalImage.src = image.src;

        modal.style.display = "flex";

    }


    function closeProfileImage() {

        document.getElementById(
            "profileImageModal"
        ).style.display = "none";

    }


    /* Close when clicking outside image */

    document.getElementById(
        "profileImageModal"
    ).addEventListener("click", function(e) {

        if (e.target === this) {
            closeProfileImage();
        }

    });


    /* Close with ESC key */

    document.addEventListener("keydown", function(e) {

        if (e.key === "Escape") {
            closeProfileImage();
        }

    });
</script>

<body>


    <!-- =========================================
     TOP BAR
========================================= -->

    <div class="topbar">

        <div>

            <i class="fas fa-building-columns"></i>

            Shri Sharda Bhavan Education Society's

        </div>

        <div>

            <i class="fas fa-envelope"></i>

            principalymn@gmail.com

        </div>

    </div>


    <!-- =========================================
     HEADER
========================================= -->

    <header class="header">

        <div class="college-info">

            <img
                src="https://maxx8680.github.io/College-Website/assets/images/logo/ymn.png"
                class="college-logo"
                alt="College Logo">

            <div>

                <h1>
                    Yeshwant Mahavidyalaya, Nanded
                </h1>

                <p>
                    Affiliated to S. R. T. M. University, Nanded
                </p>

            </div>

        </div>


        <nav class="nav">

            <a href="index.php">
                <i class="fas fa-home"></i>
                Home
            </a>

            <a href="about.php">
                <i class="fas fa-info-circle"></i>
                About
            </a>

            <a href="user_events.php">
                <i class="fas fa-calendar"></i>
                Event
            </a>

            <a href="edit_profile.php">
                <i class="fas fa-user"></i>
                Profile
            </a>

        </nav>

    </header>


    <!-- =========================================
     DASHBOARD
========================================= -->

    <div class="dashboard-container">


        <!-- =====================================
         SIDEBAR
    ====================================== -->

        <aside class="sidebar">
            <div class="avatar" onclick="openProfileImage()">

                <?php if ($profile_path !== ''): ?>

                    <img
                        src="<?php echo htmlspecialchars($profile_path); ?>"
                        alt="Profile"
                        id="sidebarProfileImage">

                <?php else: ?>

                    <?php echo htmlspecialchars($initials); ?>

                <?php endif; ?>

            </div>

            <!-- MENU -->

            <ul class="menu">


                <li>

                    <a
                        href="dashboard.php"
                        class="active">

                        <i class="fas fa-table-cells-large"></i>

                        Dashboard

                    </a>

                </li>
                <!-- 

            <li>

                <a href="dashboard.php">

                    <i class="fas fa-id-card"></i>

                    My Profile

                </a>

            </li> -->


                <li>

                    <a href="edit_profile.php">

                        <i class="fas fa-pen-to-square"></i>

                        Edit Profile

                    </a>

                </li>


                <li>

                    <a href="change_password.php">

                        <i class="fas fa-key"></i>

                        Change Password

                    </a>

                </li>


                <li>

                    <a href="my_courses.php">

                        <i class="fas fa-book"></i>

                        My Courses

                    </a>

                </li>


                <li>

                    <a href="notifications.php" class="notification-link" title="Notifications">
                        <i class="fas fa-bell"></i>
                        <span id="notificationBadge" class="notification-badge">Notification</span>
                    </a>

                </li>


                <li>

                    <a href="settings.php">

                        <i class="fas fa-gear"></i>

                        Settings

                    </a>

                </li>


            </ul>

        </aside>


        <!-- =====================================
         MAIN CONTENT
    ====================================== -->

        <main class="main">


            <!-- WELCOME -->

            <div class="welcome">

                <div>

                    <h2>

                        <i class="fas fa-table-cells-large"></i>

                        Dashboard

                    </h2>

                    <p>
                        Welcome back! Here's what's happening with your account.
                    </p>

                </div>


                <div class="user-badge">

                    <i class="fas fa-hand-wave"></i>

                    <?php
                    echo htmlspecialchars($username);
                    ?>

                </div>

            </div>


            <!-- =================================
             STATISTICS
        ================================== -->

            <div class="stats">


                <!-- USERS -->

                <div class="stat">

                    <div class="stat-icon users-icon">

                        <i class="fas fa-users"></i>

                    </div>

                    <div>

                        <h3>
                            <?php
                            echo $total_users;
                            ?>
                        </h3>

                        <p>
                            Total Users
                        </p>

                    </div>

                </div>


                <!-- COURSES -->

                <div class="stat">

                    <div class="stat-icon course-icon">

                        <i class="fas fa-book"></i>

                    </div>

                    <div>

                        <h3>
                            <?php
                            echo $total_courses;
                            ?>
                        </h3>

                        <p>
                            Total Courses
                        </p>

                    </div>

                </div>


                <!-- EVENTS -->

                <div class="stat">

                    <div class="stat-icon event-icon">

                        <i class="fas fa-calendar-days"></i>

                    </div>

                    <div>

                        <h3>
                            <?php
                            echo $total_events;
                            ?>
                        </h3>

                        <p>
                            Total Events
                        </p>

                    </div>

                </div>


                <!-- USER COURSE -->

                <div class="stat">

                    <div class="stat-icon mycourse-icon">

                        <i class="fas fa-user-graduate"></i>

                    </div>

                    <div>

                        <h3 style="font-size:18px;">

                            <?php
                            echo htmlspecialchars(
                                $course_display
                            );
                            ?>

                        </h3>

                        <p>
                            Your Course
                        </p>

                    </div>

                </div>


            </div>


            <!-- =================================
             PROFILE INFORMATION
        ================================== -->

            <div class="profile-card">


                <div class="profile-title">

                    <h2>

                        <i class="fas fa-id-card"></i>

                        Profile Information

                    </h2>


                    <?php if ($email_verified): ?>

                        <span class="verified">

                            <i class="fas fa-circle-check"></i>

                            Email Verified

                        </span>

                    <?php endif; ?>

                </div>


                <!-- PROFILE IMAGE

            <div
                class="profile-item"
                style="grid-template-columns:140px 1fr;"
            >

                <span class="label">

                    <i class="fas fa-image"></i>

                    Profile Picture

                </span>


                <span class="value">


                    <?php if ($profile_path !== ''): ?>

                        <img
                            src="<?php echo htmlspecialchars($profile_path); ?>"
                            class="profile-image"
                            alt="Profile Picture"
                        >

                    <?php else: ?>

                        <span class="empty">

                            No image uploaded

                        </span>

                    <?php endif; ?>


                </span>

            </div> -->


                <!-- NAME -->

                <div class="profile-item">

                    <span class="label">

                        <i class="fas fa-user"></i>

                        Full Name

                    </span>

                    <span class="value">

                        <?php
                        echo htmlspecialchars($name);
                        ?>

                    </span>

                </div>


                <!-- USERNAME -->

                <div class="profile-item">

                    <span class="label">

                        <i class="fas fa-at"></i>

                        Username

                    </span>

                    <span class="value">

                        <?php
                        echo htmlspecialchars($username);
                        ?>

                    </span>

                </div>


                <!-- EMAIL -->

                <div class="profile-item">

                    <span class="label">

                        <i class="fas fa-envelope"></i>

                        Email

                    </span>

                    <span class="value">

                        <?php
                        echo htmlspecialchars($email);
                        ?>

                    </span>

                </div>


                <!-- MOBILE -->

                <div class="profile-item">

                    <span class="label">

                        <i class="fas fa-phone"></i>

                        Mobile

                    </span>

                    <span class="value">

                        <?php if (!empty($mobile)): ?>

                            <?php
                            echo htmlspecialchars($mobile);
                            ?>

                        <?php else: ?>

                            <span class="empty">
                                Not provided
                            </span>

                        <?php endif; ?>

                    </span>

                </div>


                <!-- ADDRESS -->

                <div class="profile-item">

                    <span class="label">

                        <i class="fas fa-location-dot"></i>

                        Address

                    </span>

                    <span class="value">

                        <?php if (!empty($address)): ?>

                            <?php
                            echo nl2br(
                                htmlspecialchars($address)
                            );
                            ?>

                        <?php else: ?>

                            <span class="empty">
                                Not provided
                            </span>

                        <?php endif; ?>

                    </span>

                </div>


                <!-- COLLEGE -->

                <div class="profile-item">

                    <span class="label">

                        <i class="fas fa-building-columns"></i>

                        College

                    </span>

                    <span class="value">

                        <?php if (!empty($college)): ?>

                            <?php
                            echo htmlspecialchars($college);
                            ?>

                        <?php else: ?>

                            <span class="empty">
                                Not provided
                            </span>

                        <?php endif; ?>

                    </span>

                </div>


                <!-- COURSE -->

                <div class="profile-item">

                    <span class="label">

                        <i class="fas fa-book"></i>

                        Course

                    </span>

                    <span class="value">

                        <?php if (!empty($course)): ?>

                            <?php
                            echo htmlspecialchars($course);
                            ?>

                        <?php else: ?>

                            <span class="empty">
                                Not provided
                            </span>

                        <?php endif; ?>

                    </span>

                </div>


                <!-- YEAR -->

                <div class="profile-item">

                    <span class="label">

                        <i class="fas fa-calendar"></i>

                        Year

                    </span>

                    <span class="value">

                        <?php if (!empty($year)): ?>

                            <?php
                            echo htmlspecialchars($year);
                            ?>

                        <?php else: ?>

                            <span class="empty">
                                Not provided
                            </span>

                        <?php endif; ?>

                    </span>

                </div>


                <!-- EDIT -->

                <a
                    href="edit_profile.php"
                    class="edit-btn">

                    <i class="fas fa-pen-to-square"></i>

                    Edit Profile

                </a>


            </div>


        </main>

    </div>


</body>

</html>