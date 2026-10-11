<?php
session_start();
include 'db.php';

// =====================================================
// FETCH ALL EVENTS
// =====================================================

$sql = "
    SELECT
        id,
        event_title,
        description,
        event_date,
        event_time,
        venue,
        organizer,
        image,
        registration_status
    FROM events
    ORDER BY event_date ASC, event_time ASC
";

$result = $conn->query($sql);

if (!$result) {
    die("Error fetching events: " . $conn->error);
}

// =====================================================
// LOGIN STATUS
// =====================================================

$is_logged_in = isset($_SESSION['username']);

// =====================================================
// GET LOGGED-IN STUDENT EMAIL
// =====================================================

$student_email = '';

if ($is_logged_in && isset($_SESSION['email'])) {
    $student_email = trim($_SESSION['email']);
}

// =====================================================
// FETCH STUDENT'S REGISTERED EVENTS
// =====================================================

$registered_events = [];

if ($is_logged_in && !empty($student_email)) {

    $reg_sql = $conn->prepare("
        SELECT id, event_id
        FROM event_registrations
        WHERE LOWER(TRIM(student_email)) = LOWER(TRIM(?))
    ");

    if ($reg_sql) {

        $reg_sql->bind_param("s", $student_email);
        $reg_sql->execute();

        $reg_result = $reg_sql->get_result();

        while ($reg_row = $reg_result->fetch_assoc()) {

            $registered_events[(int)$reg_row['event_id']] =
                (int)$reg_row['id'];
        }

        $reg_sql->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>College Events</title>

    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f5f9ff;
            color: #1e2a41;
            line-height: 1.5;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* =====================================================
           TOP HEADER
        ===================================================== */

        .top-header {
            background: #00337a;
            color: rgba(255, 255, 255, 0.9);

            padding: 7px 5%;

            display: flex;
            justify-content: space-between;
            align-items: center;

            font-size: 0.85rem;
            letter-spacing: 0.3px;

            border-bottom: 2px solid #ffc107;

            flex-wrap: wrap;
            gap: 10px;
        }

        .top-header i {
            margin-right: 6px;
            color: #ffc107;
        }

        /* =====================================================
           NAVBAR
        ===================================================== */

        .navbar {
            background: rgba(255, 255, 255, 0.98);

            backdrop-filter: blur(4px);

            min-height: 80px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 8px 4%;

            box-shadow: 0 6px 24px rgba(0, 40, 80, 0.10);

            position: sticky;
            top: 0;

            z-index: 1000;

            border-bottom: 1px solid rgba(0, 70, 150, 0.08);
        }

        /* =====================================================
           LOGO
        ===================================================== */

        .logo-area {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .logo {
            width: 60px;
            height: 60px;

            border-radius: 50%;

            object-fit: cover;

            border: 2px solid #004b91;

            box-shadow: 0 2px 8px rgba(0, 60, 120, 0.2);

            background: white;

            padding: 2px;
        }

        .college-name {
            color: #00337a;

            font-size: 1.2rem;

            font-weight: 700;

            line-height: 1.2;
        }

        .college-name small {
            display: block;

            font-size: 0.7rem;

            font-weight: 500;

            color: #3b5f7a;

            letter-spacing: 0.3px;

            margin-top: 2px;
        }

        /* =====================================================
           NAV LINKS
        ===================================================== */

        .nav-links {
            display: flex;

            align-items: center;

            flex-wrap: wrap;

            gap: 0;

            list-style: none;
        }

        .nav-links a {
            color: #174a7c;

            font-size: 0.82rem;

            font-weight: 600;

            padding: 10px 12px;

            border-radius: 6px;

            transition: 0.25s ease;

            display: inline-block;
        }

        .nav-links a:hover {
            background: #004b91;

            color: white;

            box-shadow: 0 4px 10px rgba(0, 75, 145, 0.25);

            transform: translateY(-1px);
        }

        /* =====================================================
           MAIN CONTAINER
        ===================================================== */

        .container {
            max-width: 1150px;

            margin: 0 auto;

            padding: 50px 20px;
        }

        /* =====================================================
           PAGE TITLE
        ===================================================== */

        .page-title {
            text-align: center;

            margin-bottom: 35px;
        }

        .page-title h1 {
            font-size: 36px;

            color: #222;

            margin-bottom: 10px;
        }

        .page-title p {
            color: #666;

            font-size: 16px;
        }

        /* =====================================================
           EVENTS GRID
        ===================================================== */

        .events-grid {
            display: grid;

            grid-template-columns: repeat(3, 1fr);

            gap: 25px;
        }

        /* =====================================================
           EVENT CARD
        ===================================================== */

        .event-card {
            background: white;

            border-radius: 14px;

            overflow: hidden;

            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.10);

            transition: 0.3s;

            cursor: pointer;
        }

        .event-card:hover {
            transform: translateY(-7px);

            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        /* =====================================================
           EVENT IMAGE
        ===================================================== */

        .event-image {
            width: 100%;
            height: 210px;

            overflow: hidden;

            background: #eeeeee;

            position: relative;
        }

        .event-image img {
            width: 100%;
            height: 100%;

            object-fit: cover;

            display: block;

            transition: transform 0.4s ease;
        }

        .event-card:hover .event-image img {
            transform: scale(1.05);
        }

        /* =====================================================
           NO IMAGE
        ===================================================== */

        .no-image {
            width: 100%;
            height: 100%;

            display: flex;

            justify-content: center;
            align-items: center;

            flex-direction: column;

            color: #777;

            font-size: 15px;
        }

        .no-image i {
            font-size: 45px;

            margin-bottom: 8px;

            color: #aaa;
        }

        /* =====================================================
           EVENT HEADER
        ===================================================== */

        .event-header {
            background: #1769aa;

            color: white;

            padding: 22px;
        }

        .event-header h2 {
            font-size: 21px;

            line-height: 1.4;
        }

        /* =====================================================
           EVENT CONTENT
        ===================================================== */

        .event-content {
            padding: 22px;
        }

        /* =====================================================
           DATE BOX
        ===================================================== */

        .date-box {
            display: inline-block;

            background: #eaf3ff;

            color: #1769aa;

            padding: 8px 14px;

            border-radius: 6px;

            font-weight: bold;

            margin-bottom: 15px;
        }

        /* =====================================================
           EVENT INFO
        ===================================================== */

        .event-info {
            margin-bottom: 12px;

            color: #555;

            font-size: 15px;

            line-height: 1.6;
        }

        .event-info strong {
            color: #222;
        }

        /* =====================================================
           EVENT TIME
        ===================================================== */

        .event-time {
            margin-bottom: 15px;

            color: #555;

            font-size: 15px;
        }

        .event-time strong {
            color: #222;
        }

        /* =====================================================
           VENUE
        ===================================================== */

        .venue {
            background: #f7f7f7;

            padding: 12px;

            border-radius: 7px;

            color: #555;

            line-height: 1.6;

            margin-bottom: 12px;
        }

        .venue strong {
            color: #222;
        }

        /* =====================================================
           ORGANIZER
        ===================================================== */

        .organizer {
            color: #555;

            font-size: 14px;

            margin-bottom: 10px;
        }

        .organizer strong {
            color: #222;
        }

        /* =====================================================
           STATUS BADGE
        ===================================================== */

        .status-badge {
            display: inline-block;

            margin-top: 5px;

            padding: 6px 11px;

            border-radius: 20px;

            font-size: 12px;

            font-weight: bold;
        }

        .status-open {
            background: #e8f7ee;

            color: #198754;
        }

        .status-closed {
            background: #f8d7da;

            color: #842029;
        }

        /* =====================================================
           REGISTER BUTTON
        ===================================================== */

        .register-btn {
            display: block;

            text-align: center;

            margin-top: 18px;

            padding: 12px;

            background: #1769aa;

            color: white;

            border-radius: 7px;

            font-weight: bold;

            transition: 0.3s;
        }

        .register-btn:hover {
            background: #0d4f82;

            transform: translateY(-2px);
        }

        /* =====================================================
           LOGIN BUTTON
        ===================================================== */

        .login-btn {
            background: #f39c12;
        }

        .login-btn:hover {
            background: #d68910;
        }

        /* =====================================================
           PRINT BUTTON
        ===================================================== */

        .print-btn {
            background: #198754;
        }

        .print-btn:hover {
            background: #146c43;
        }

        /* =====================================================
           REGISTERED BADGE
        ===================================================== */

        .registered-badge {
            display: block;

            text-align: center;

            margin-top: 12px;

            padding: 7px;

            background: #e8f7ee;

            color: #198754;

            border-radius: 6px;

            font-size: 13px;

            font-weight: 600;
        }

        /* =====================================================
           NO EVENTS
        ===================================================== */

        .no-events {
            text-align: center;

            background: white;

            padding: 40px;

            border-radius: 10px;

            color: #777;

            font-size: 18px;

            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 1000px) {

            .events-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .nav-links {
                justify-content: center;
            }

        }

        @media (max-width: 700px) {

            .navbar {
                flex-direction: column;

                gap: 15px;

                padding: 15px;
            }

            .logo-area {
                justify-content: center;

                text-align: center;
            }

            .nav-links {
                justify-content: center;
            }

            .events-grid {
                grid-template-columns: 1fr;
            }

            .page-title h1 {
                font-size: 28px;
            }

        }

        @media (max-width: 500px) {

            .top-header {
                justify-content: center;

                text-align: center;
            }

            .college-name {
                font-size: 1rem;
            }

            .logo {
                width: 50px;
                height: 50px;
            }

            .event-image {
                height: 190px;
            }

        }
    </style>

</head>

<body>


    <!-- =====================================================
     TOP HEADER
===================================================== -->

    <div class="top-header">

        <div>

            <i class="fas fa-university"></i>

            Shri Sharda Bhavan Education Society's

        </div>

        <div>

            <i class="fas fa-envelope"></i>

            principalymn@gmail.com

        </div>

    </div>


    <!-- =====================================================
     NAVBAR
===================================================== -->

    <nav class="navbar">

        <div class="logo-area">

            <img
                src="https://tse2.mm.bing.net/th/id/OIP.nYyQmvWkM4rlNO85xpY68AAAAA?r=0&pid=Api&h=220&P=0g"
                alt="College Logo"
                class="logo">

            <div class="college-name">

                Yeshwant Mahavidyalaya, Nanded

                <small>
                    Affiliated to S. R. T. M. University, Nanded
                </small>

            </div>

        </div>


        <ul class="nav-links">

            <li>
                <a href="index.php">
                    <i class="fas fa-home"></i> Home
                </a>
            </li>

            <li>
                <a href="about.php">
                    About
                </a>
            </li>

            <li>
                <a href="admission.php">
                    Admissions
                </a>
            </li>

            <li>
                <a href="#">
                    Programs
                </a>
            </li>

            <li>
                <a href="departments.php">
                    Departments
                </a>
            </li>

            <li>
                <a href="#">
                    Facilities
                </a>
            </li>

            <li>
                <a href="#">
                    Students
                </a>
            </li>

            <li>
                <a href="#">
                    NAAC
                </a>
            </li>

            <li>
                <a href="#">
                    Alumni
                </a>
            </li>

            <li>
                <a href="#">
                    Feedback
                </a>
            </li>

            <li>
                <a href="user_events.php">
                    Event
                </a>
            </li>

            <li>
                <a href="contact.php">
                    Contact
                </a>
            </li>

            <li>
                <a href="dashboard.php">
                    Profile
                </a>
            </li>

        </ul>

    </nav>


    <!-- =====================================================
     MAIN CONTENT
===================================================== -->

    <div class="container">


        <!-- PAGE TITLE -->

        <div class="page-title">

            <h1>
                College Events
            </h1>

            <p>
                Stay updated with our latest college events
            </p>

        </div>


        <!-- =================================================
         EVENTS
    ================================================= -->

        <?php if ($result->num_rows > 0) { ?>

            <div class="events-grid">


                <?php while ($row = $result->fetch_assoc()) { ?>


                    <?php

                    // Event ID
                    $event_id = (int)$row['id'];


                    // =================================================
                    // CHECK REGISTRATION
                    // =================================================

                    $is_registered =
                        isset($registered_events[$event_id]);


                    // =================================================
                    // REGISTRATION STATUS
                    // =================================================

                    $registration_status =
                        strtolower(
                            trim(
                                $row['registration_status'] ?? 'open'
                            )
                        );


                    $is_closed =
                        ($registration_status === 'closed');


                    // =================================================
                    // IMAGE PATH
                    // =================================================

                    $image = trim($row['image'] ?? '');

                    $image_src = '';


                    if ($image !== '') {

                        // Full URL
                        if (
                            strpos($image, 'http://') === 0 ||
                            strpos($image, 'https://') === 0
                        ) {

                            $image_src = $image;
                        } else {

                            // Remove starting slash
                            $image = ltrim($image, '/');

                            /*
                         * If database contains only filename:
                         *
                         * event1.jpg
                         *
                         * use:
                         * uploads/events/event1.jpg
                         */

                            if (
                                strpos($image, '/') === false &&
                                strpos($image, '\\') === false
                            ) {

                                $image_src =
                                    'uploads/events/' . $image;
                            } else {

                                $image_src = $image;
                            }
                        }
                    }

                    ?>


                    <!-- =================================================
                     EVENT CARD
                ================================================= -->

                    <div
                        class="event-card"
                        onclick="openEvent(<?php echo $event_id; ?>)">


                        <!-- =================================================
                         EVENT IMAGE
                    ================================================= -->

                        <div class="event-image">

                            <?php if (!empty($image_src)) { ?>

                                <img
                                    src="<?php echo htmlspecialchars($image_src); ?>"
                                    alt="<?php echo htmlspecialchars($row['event_title']); ?>"
                                    loading="lazy"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">

                                <div
                                    class="no-image"
                                    style="display:none;">

                                    <i class="fas fa-image"></i>

                                    <span>
                                        Image not available
                                    </span>

                                </div>

                            <?php } else { ?>

                                <div class="no-image">

                                    <i class="fas fa-image"></i>

                                    <span>
                                        No Image Available
                                    </span>

                                </div>

                            <?php } ?>

                        </div>


                        <!-- =================================================
                         EVENT HEADER
                    ================================================= -->

                        <div class="event-header">

                            <h2>

                                <?php

                                echo htmlspecialchars(
                                    $row['event_title']
                                );

                                ?>

                            </h2>

                        </div>


                        <!-- =================================================
                         EVENT CONTENT
                    ================================================= -->

                        <div class="event-content">


                            <!-- DATE -->

                            <div class="date-box">

                                📅

                                <?php

                                if (!empty($row['event_date'])) {

                                    echo date(
                                        "d M Y",
                                        strtotime($row['event_date'])
                                    );
                                } else {

                                    echo "Date not available";
                                }

                                ?>

                            </div>


                            <!-- EVENT DATE -->

                            <div class="event-info">

                                <strong>
                                    Event Date:
                                </strong>

                                <?php

                                if (!empty($row['event_date'])) {

                                    echo date(
                                        "d F Y",
                                        strtotime($row['event_date'])
                                    );
                                } else {

                                    echo "Date not available";
                                }

                                ?>

                            </div>


                            <!-- EVENT TIME -->

                            <?php if (!empty($row['event_time'])) { ?>

                                <div class="event-time">

                                    <strong>

                                        <i class="fas fa-clock"></i>

                                        Event Time:

                                    </strong>

                                    <?php

                                    echo date(
                                        "h:i A",
                                        strtotime($row['event_time'])
                                    );

                                    ?>

                                </div>

                            <?php } ?>


                            <!-- VENUE -->

                            <div class="venue">

                                📍

                                <strong>
                                    Venue:
                                </strong>

                                <br>

                                <?php

                                echo htmlspecialchars(
                                    $row['venue'] ??
                                        'Venue not available'
                                );

                                ?>

                            </div>


                            <!-- ORGANIZER -->

                            <?php if (!empty($row['organizer'])) { ?>

                                <div class="organizer">

                                    <strong>

                                        <i class="fas fa-user-tie"></i>

                                        Organizer:

                                    </strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $row['organizer']
                                    );

                                    ?>

                                </div>

                            <?php } ?>


                            <!-- REGISTRATION STATUS -->

                            <?php if ($is_closed) { ?>

                                <div class="status-badge status-closed">

                                    🔴 Registration Closed

                                </div>

                            <?php } else { ?>

                                <div class="status-badge status-open">

                                    🟢 Registration Open

                                </div>

                            <?php } ?>


                            <!-- =================================================
                             REGISTRATION BUTTON
                        ================================================= -->

                            <?php if ($is_closed) { ?>


                                <!-- REGISTRATION CLOSED -->

                                <div
                                    class="register-btn"
                                    style="
                                    background:#6c757d;
                                    cursor:not-allowed;
                                "
                                    onclick="event.stopPropagation();">

                                    <i class="fas fa-lock"></i>

                                    Registration Closed

                                </div>


                            <?php } elseif (!$is_logged_in) { ?>


                                <!-- NOT LOGGED IN -->

                                <?php

                                $redirect =
                                    'event_register.php?event_id='
                                    . $event_id;

                                ?>

                                <a
                                    href="registration/login.php?redirect=<?php echo urlencode($redirect); ?>"
                                    class="register-btn login-btn"
                                    onclick="event.stopPropagation();">

                                    <i class="fas fa-right-to-bracket"></i>

                                    Login to Register

                                </a>


                            <?php } elseif ($is_registered) { ?>


                                <!-- ALREADY REGISTERED -->

                                <div class="registered-badge">

                                    <i class="fas fa-circle-check"></i>

                                    Already Registered

                                </div>


                                <a
                                    href="my_event_registration.php?id=<?php echo $registered_events[$event_id]; ?>"
                                    class="register-btn print-btn"
                                    onclick="event.stopPropagation();">

                                    <i class="fas fa-print"></i>

                                    Print Registration

                                </a>


                            <?php } else { ?>


                                <!-- NOT REGISTERED -->

                                <a
                                    href="event_register.php?event_id=<?php echo $event_id; ?>"
                                    class="register-btn"
                                    onclick="event.stopPropagation();">

                                    <i class="fas fa-user-plus"></i>

                                    Register Now

                                </a>


                            <?php } ?>


                        </div>

                    </div>


                <?php } ?>


            </div>


        <?php } else { ?>


            <!-- NO EVENTS -->

            <div class="no-events">

                📅 No events available at the moment.

            </div>


        <?php } ?>


    </div>


    <!-- =====================================================
     JAVASCRIPT
===================================================== -->

    <script>
        function openEvent(eventId) {

            window.location.href =
                "event_details.php?event_id=" + eventId;

        }
    </script>


</body>

</html>
```