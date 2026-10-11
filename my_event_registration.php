<?php

include 'db.php';


/*
|--------------------------------------------------------------------------
| CHECK REGISTRATION ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id'])) {
    die("Invalid registration request.");
}

$registration_id = intval($_GET['id']);

if ($registration_id <= 0) {
    die("Invalid registration ID.");
}


/*
|--------------------------------------------------------------------------
| GET REGISTRATION + EVENT DETAILS
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        er.id,
        er.event_id,
        er.student_name,
        er.student_email,
        er.student_mobile,
        er.department,
        er.registered_at,

        e.*

    FROM event_registrations er

    INNER JOIN events e
        ON er.event_id = e.id

    WHERE er.id = ?

    LIMIT 1
");


if (!$stmt) {
    die("Database query error: " . $conn->error);
}


$stmt->bind_param(
    "i",
    $registration_id
);


if (!$stmt->execute()) {
    die("Database execution error: " . $stmt->error);
}


$result = $stmt->get_result();


if ($result->num_rows == 0) {

    $stmt->close();

    die("Registration not found.");
}


$registration = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| EVENT NAME
|--------------------------------------------------------------------------
*/

$event_name = "College Event";

if (
    isset($registration['title']) &&
    !empty($registration['title'])
) {

    $event_name = $registration['title'];

} elseif (
    isset($registration['name']) &&
    !empty($registration['name'])
) {

    $event_name = $registration['name'];

} elseif (
    isset($registration['event_title']) &&
    !empty($registration['event_title'])
) {

    $event_name = $registration['event_title'];

} elseif (
    isset($registration['event_name']) &&
    !empty($registration['event_name'])
) {

    $event_name = $registration['event_name'];
}


/*
|--------------------------------------------------------------------------
| EVENT DATE
|--------------------------------------------------------------------------
*/

$event_date = "";

if (
    isset($registration['event_date']) &&
    !empty($registration['event_date'])
) {

    $event_date = $registration['event_date'];

} elseif (
    isset($registration['date']) &&
    !empty($registration['date'])
) {

    $event_date = $registration['date'];
}


/*
|--------------------------------------------------------------------------
| VENUE
|--------------------------------------------------------------------------
*/

$venue = "Venue not available";

if (
    isset($registration['venue']) &&
    !empty($registration['venue'])
) {

    $venue = $registration['venue'];

} elseif (
    isset($registration['location']) &&
    !empty($registration['location'])
) {

    $venue = $registration['location'];
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Event Registration Receipt</title>


<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}


body {
    background: #f4f6f9;
    padding: 30px 15px;
}


.container {
    max-width: 800px;
    margin: auto;
}


.receipt {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}


/* HEADER */

.receipt-header {
    background: #1769aa;
    color: white;
    padding: 28px;
    text-align: center;
}


.college-name {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 6px;
}


.college-subtitle {
    font-size: 14px;
    opacity: 0.9;
}


.receipt-title {
    margin-top: 20px;
    font-size: 27px;
    font-weight: bold;
}


/* SUCCESS */

.success-area {
    text-align: center;
    padding: 25px 20px 10px;
}


.success-icon {
    width: 65px;
    height: 65px;
    margin: auto;
    background: #28a745;
    color: white;
    border-radius: 50%;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 35px;
    font-weight: bold;
}


.success-area h1 {
    color: #28a745;
    margin-top: 12px;
    font-size: 27px;
}


.success-area p {
    color: #666;
    margin-top: 6px;
}


/* REGISTRATION ID */

.registration-id {
    margin: 20px;
    background: #eaf3ff;
    border: 1px solid #c9e1ff;
    border-radius: 10px;
    padding: 18px;
    text-align: center;
}


.registration-id span {
    display: block;
    color: #666;
    margin-bottom: 5px;
}


.registration-id strong {
    color: #1769aa;
    font-size: 27px;
}


/* CONTENT */

.receipt-content {
    padding: 10px 30px 30px;
}


.section-title {
    color: #1769aa;
    font-size: 21px;
    padding-bottom: 10px;
    border-bottom: 2px solid #1769aa;
    margin-top: 20px;
    margin-bottom: 5px;
}


.detail-row {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    padding: 13px 0;
    border-bottom: 1px solid #eee;
}


.detail-label {
    font-weight: bold;
    color: #555;
}


.detail-value {
    color: #222;
    text-align: right;
    word-break: break-word;
}


/* EVENT */

.event-box {
    background: #f0f7ff;
    border-left: 5px solid #1769aa;
    padding: 20px;
    border-radius: 8px;
    margin-top: 15px;
}


.event-box h2 {
    color: #1769aa;
    margin-bottom: 15px;
    font-size: 21px;
}


.event-info {
    margin: 10px 0;
    color: #444;
    line-height: 1.5;
}


/* STATUS */

.status {
    display: inline-block;
    background: #d4edda;
    color: #155724;
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: bold;
}


/* NOTICE */

.notice {
    margin-top: 20px;
    background: #d4edda;
    border: 1px solid #b7dfc2;
    color: #155724;
    padding: 14px;
    border-radius: 8px;
    text-align: center;
    line-height: 1.5;
}


/* BUTTONS */

.buttons {
    display: flex;
    gap: 12px;
    margin-top: 25px;
}


.btn {
    flex: 1;
    padding: 14px;
    border-radius: 8px;
    text-align: center;
    text-decoration: none;
    font-weight: bold;
    cursor: pointer;
    border: none;
    font-size: 15px;
}


.print-btn {
    background: #1769aa;
    color: white;
}


.events-btn {
    background: #555;
    color: white;
}


.print-btn:hover {
    background: #0d4f82;
}


.events-btn:hover {
    background: #333;
}


/* MOBILE */

@media (max-width: 600px) {

    body {
        padding: 15px 10px;
    }

    .receipt-header {
        padding: 22px 15px;
    }

    .college-name {
        font-size: 20px;
    }

    .receipt-title {
        font-size: 23px;
    }

    .receipt-content {
        padding: 10px 20px 25px;
    }

    .detail-row {
        flex-direction: column;
        gap: 5px;
    }

    .detail-value {
        text-align: left;
    }

    .buttons {
        flex-direction: column;
    }

}


/* PRINT */

@media print {

    body {
        background: white;
        padding: 0;
    }

    .container {
        max-width: 100%;
    }

    .receipt {
        box-shadow: none;
        border: 1px solid #ddd;
        border-radius: 0;
    }

    .buttons,
    .notice {
        display: none !important;
    }

}

</style>

</head>


<body>

<div class="container">

<div class="receipt">


<!-- COLLEGE HEADER -->

<div class="receipt-header">

    <div class="college-name">
        Yeshwant Mahavidyalaya, Nanded
    </div>

    <div class="college-subtitle">
        Affiliated to S. R. T. M. University, Nanded
    </div>

    <div class="receipt-title">
        EVENT REGISTRATION RECEIPT
    </div>

</div>


<!-- SUCCESS -->

<div class="success-area">

    <div class="success-icon">
        ✓
    </div>

    <h1>
        Registration Successful!
    </h1>

    <p>
        You have successfully registered for this event.
    </p>

</div>


<!-- REGISTRATION ID -->

<div class="registration-id">

    <span>
        Registration ID
    </span>

    <strong>
        #<?php echo $registration_id; ?>
    </strong>

</div>


<div class="receipt-content">


<!-- STUDENT DETAILS -->

<h2 class="section-title">
    👤 Student Details
</h2>


<div class="detail-row">

    <span class="detail-label">
        Student Name
    </span>

    <span class="detail-value">
        <?php
        echo htmlspecialchars(
            $registration['student_name']
        );
        ?>
    </span>

</div>


<div class="detail-row">

    <span class="detail-label">
        Email
    </span>

    <span class="detail-value">
        <?php
        echo htmlspecialchars(
            $registration['student_email']
        );
        ?>
    </span>

</div>


<div class="detail-row">

    <span class="detail-label">
        Mobile Number
    </span>

    <span class="detail-value">
        <?php
        echo htmlspecialchars(
            $registration['student_mobile']
        );
        ?>
    </span>

</div>


<div class="detail-row">

    <span class="detail-label">
        Department
    </span>

    <span class="detail-value">
        <?php
        echo htmlspecialchars(
            $registration['department']
        );
        ?>
    </span>

</div>


<!-- EVENT DETAILS -->

<div class="event-box">

    <h2>
        🎉 Event Details
    </h2>


    <div class="event-info">

        <strong>
            Event Name:
        </strong>

        <?php
        echo htmlspecialchars($event_name);
        ?>

    </div>


    <div class="event-info">

        <strong>
            📅 Event Date:
        </strong>

        <?php

        if (!empty($event_date)) {

            echo date(
                "d F Y",
                strtotime($event_date)
            );

        } else {

            echo "Date not available";

        }

        ?>

    </div>


    <div class="event-info">

        <strong>
            📍 Venue:
        </strong>

        <?php
        echo htmlspecialchars($venue);
        ?>

    </div>


    <div class="event-info">

        <strong>
            🕐 Registered On:
        </strong>

        <?php
        echo date(
            "d M Y h:i A",
            strtotime($registration['registered_at'])
        );
        ?>

    </div>


    <div class="event-info">

        <strong>
            Status:
        </strong>

        <span class="status">
            ✓ Registered
        </span>

    </div>

</div>


<!-- NOTICE -->

<div class="notice">

    ✓ Registration completed successfully.

    <br>

    Please keep your Registration ID for future reference.

</div>


<!-- BUTTONS -->

<div class="buttons">

    <button
        type="button"
        class="btn print-btn"
        onclick="window.print()">

        🖨 Print Registration

    </button>


    <a
        href="user_events.php"
        class="btn events-btn">

        ← View All Events

    </a>

</div>


</div>

</div>

</div>

</body>

</html>