<?php

session_start();

include 'db.php';


/*
|--------------------------------------------------------------------------
| Check Event ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET['event_id']) || empty($_GET['event_id'])) {

    die("Invalid Event ID.");
}

$event_id = (int) $_GET['event_id'];


/*
|--------------------------------------------------------------------------
| Fetch Event
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
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
    WHERE id = ?
");

if (!$stmt) {

    die("Database Error: " . $conn->error);
}

$stmt->bind_param("i", $event_id);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows === 0) {

    $stmt->close();

    die("Event not found.");
}


$event = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Login Check
|--------------------------------------------------------------------------
*/

$is_logged_in = isset($_SESSION['username']);


/*
|--------------------------------------------------------------------------
| Registration URL
|--------------------------------------------------------------------------
*/

$register_url = "event_register.php?event_id=" . $event_id;


if (!$is_logged_in) {

    $login_url = "login.php?redirect=" . urlencode($register_url);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>

        <?php

        echo htmlspecialchars(
            $event['event_title']
        );

        ?>

        - Event Details

    </title>


    <style>
        * {

            margin: 0;
            padding: 0;
            box-sizing: border-box;

            font-family: Arial, sans-serif;

        }


        body {

            background: #f4f6f9;

            padding: 40px 20px;

        }


        .container {

            max-width: 900px;

            margin: auto;

        }


        .back-btn {

            display: inline-block;

            margin-bottom: 20px;

            text-decoration: none;

            color: #1769aa;

            font-weight: bold;

            font-size: 16px;

        }


        .back-btn:hover {

            color: #0d4f82;

        }


        .event-details {

            background: white;

            border-radius: 15px;

            overflow: hidden;

            box-shadow:
                0 8px 30px rgba(0, 0, 0, 0.12);

        }


        /*
        |--------------------------------------------------------------------------
        | Event Header
        |--------------------------------------------------------------------------
        */

        .event-title {

            background:
                linear-gradient(135deg,
                    #1769aa,
                    #0d4f82);

            color: white;

            padding: 35px;

        }


        .event-title h1 {

            font-size: 32px;

            margin-bottom: 10px;

            line-height: 1.3;

        }


        .event-title p {

            font-size: 15px;

            opacity: 0.9;

        }


        /*
        |--------------------------------------------------------------------------
        | Event Body
        |--------------------------------------------------------------------------
        */

        .event-body {

            padding: 35px;

        }


        .detail-box {

            background: #f7f9fc;

            padding: 18px;

            border-radius: 10px;

            margin-bottom: 18px;

            border-left:
                4px solid #1769aa;

        }


        .detail-box strong {

            display: block;

            color: #1769aa;

            margin-bottom: 7px;

            font-size: 16px;

        }


        .detail-box p {

            color: #444;

            font-size: 16px;

            line-height: 1.6;

        }


        /*
        |--------------------------------------------------------------------------
        | Description
        |--------------------------------------------------------------------------
        */

        .description {

            margin-top: 25px;

        }


        .description h2 {

            margin-bottom: 12px;

            color: #222;

            font-size: 24px;

        }


        .description p {

            color: #555;

            line-height: 1.7;

            font-size: 16px;

        }


        /*
        |--------------------------------------------------------------------------
        | Image
        |--------------------------------------------------------------------------
        */

        .event-image {

            width: 100%;

            max-height: 450px;

            object-fit: cover;

            display: block;

        }


        /*
        |--------------------------------------------------------------------------
        | Register Button
        |--------------------------------------------------------------------------
        */

        .register-btn {

            display: block;

            text-align: center;

            margin-top: 30px;

            padding: 15px;

            background: #1769aa;

            color: white;

            text-decoration: none;

            border-radius: 8px;

            font-weight: bold;

            font-size: 17px;

            transition: 0.3s;

        }


        .register-btn:hover {

            background: #0d4f82;

            transform: translateY(-2px);

        }


        .login-btn {

            background: #e67e22;

        }


        .login-btn:hover {

            background: #c95f0c;

        }


        /*
        |--------------------------------------------------------------------------
        | Registration Closed
        |--------------------------------------------------------------------------
        */

        .closed-btn {

            display: block;

            text-align: center;

            margin-top: 30px;

            padding: 15px;

            background: #dc3545;

            color: white;

            border-radius: 8px;

            font-weight: bold;

            font-size: 17px;

        }


        /*
        |--------------------------------------------------------------------------
        | Login Message
        |--------------------------------------------------------------------------
        */

        .login-message {

            margin-top: 15px;

            padding: 14px;

            background: #fff8e6;

            border: 1px solid #f1d48a;

            border-radius: 8px;

            text-align: center;

            color: #765b16;

            font-size: 14px;

        }


        /*
        |--------------------------------------------------------------------------
        | Mobile
        |--------------------------------------------------------------------------
        */

        @media (max-width: 600px) {

            body {

                padding: 25px 15px;

            }


            .event-title {

                padding: 25px;

            }


            .event-title h1 {

                font-size: 25px;

            }


            .event-body {

                padding: 25px;

            }


            .description h2 {

                font-size: 21px;

            }

        }
    </style>

</head>


<body>


    <div class="container">


        <!-- Back -->

        <a
            href="user_events.php"
            class="back-btn">
            ← Back to Events
        </a>


        <div class="event-details">


            <!-- =====================================================
             EVENT IMAGE
        ====================================================== -->

            <?php

            $image = trim(
                $event['image'] ?? ''
            );

            if ($image !== ''):

                if (
                    strpos($image, 'http://') === 0 ||
                    strpos($image, 'https://') === 0
                ) {

                    $image_src = $image;
                } else {

                    $image_src =
                        ltrim($image, '/');
                }

            ?>

                <img
                    src="<?php echo htmlspecialchars($image_src); ?>"
                    class="event-image"
                    alt="Event Image"
                    onerror="this.style.display='none';">

            <?php endif; ?>


            <!-- =====================================================
             EVENT HEADER
        ====================================================== -->

            <div class="event-title">

                <h1>

                    <?php

                    echo htmlspecialchars(
                        $event['event_title']
                    );

                    ?>

                </h1>


                <p>

                    College Event Details

                </p>

            </div>


            <!-- =====================================================
             EVENT BODY
        ====================================================== -->

            <div class="event-body">


                <!-- DATE -->

                <div class="detail-box">

                    <strong>
                        📅 Event Date
                    </strong>


                    <p>

                        <?php

                        if (!empty($event['event_date'])) {

                            echo date(
                                "d F Y",
                                strtotime(
                                    $event['event_date']
                                )
                            );
                        } else {

                            echo "Date not available";
                        }

                        ?>

                    </p>

                </div>


                <!-- TIME -->

                <?php if (!empty($event['event_time'])): ?>

                    <div class="detail-box">

                        <strong>
                            ⏰ Event Time
                        </strong>

                        <p>

                            <?php

                            echo date(
                                "h:i A",
                                strtotime(
                                    $event['event_time']
                                )
                            );

                            ?>

                        </p>

                    </div>

                <?php endif; ?>


                <!-- VENUE -->

                <div class="detail-box">

                    <strong>
                        📍 Venue
                    </strong>


                    <p>

                        <?php

                        if (!empty($event['venue'])) {

                            echo htmlspecialchars(
                                $event['venue']
                            );
                        } else {

                            echo "Venue not available";
                        }

                        ?>

                    </p>

                </div>


                <!-- ORGANIZER -->

                <?php if (!empty($event['organizer'])): ?>

                    <div class="detail-box">

                        <strong>
                            👤 Organizer
                        </strong>


                        <p>

                            <?php

                            echo htmlspecialchars(
                                $event['organizer']
                            );

                            ?>

                        </p>

                    </div>

                <?php endif; ?>


                <!-- DESCRIPTION -->

                <?php

                if (!empty($event['description'])):

                ?>

                    <div class="description">

                        <h2>
                            About This Event
                        </h2>


                        <p>

                            <?php

                            echo nl2br(
                                htmlspecialchars(
                                    $event['description']
                                )
                            );

                            ?>

                        </p>

                    </div>

                <?php endif; ?>


                <!-- =================================================
                 REGISTRATION
            ================================================== -->

                <?php

                $registration_status =
                    strtolower(
                        trim(
                            $event['registration_status'] ?? 'Open'
                        )
                    );

                ?>


                <?php if ($registration_status === 'closed'): ?>


                    <div class="closed-btn">

                        🔴 Registration Closed

                    </div>


                <?php elseif ($is_logged_in): ?>


                    <a
                        href="<?php echo htmlspecialchars($register_url); ?>"
                        class="register-btn">

                        📝 Register Now

                    </a>


                <?php else: ?>


                    <a
                        href="<?php echo htmlspecialchars($login_url); ?>"
                        class="register-btn login-btn">

                        🔐 Login to Register

                    </a>


                    <div class="login-message">

                        Please login to your student account
                        to register for this event.

                    </div>


                <?php endif; ?>


            </div>

        </div>

    </div>


</body>

</html>

<?php

$conn->close();

?>
```