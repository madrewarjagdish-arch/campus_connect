<?php

include 'db.php';

/*
|--------------------------------------------------------------------------
| ONLY POST REQUEST ALLOWED
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: user_events.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| GET FORM DATA
|--------------------------------------------------------------------------
*/

$event_id = isset($_POST['event_id'])
    ? intval($_POST['event_id'])
    : 0;

$student_name = isset($_POST['student_name'])
    ? trim($_POST['student_name'])
    : '';

$student_email = isset($_POST['student_email'])
    ? trim($_POST['student_email'])
    : '';

$student_mobile = isset($_POST['student_mobile'])
    ? trim($_POST['student_mobile'])
    : '';

$department = isset($_POST['department'])
    ? trim($_POST['department'])
    : '';


/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if ($event_id <= 0) {
    die("Invalid Event ID.");
}

if (
    empty($student_name) ||
    empty($student_email) ||
    empty($student_mobile) ||
    empty($department)
) {
    die("Please fill all required fields.");
}


/*
|--------------------------------------------------------------------------
| EMAIL VALIDATION
|--------------------------------------------------------------------------
*/

if (!filter_var($student_email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email address.");
}


/*
|--------------------------------------------------------------------------
| CHECK EVENT EXISTS
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT *
    FROM events
    WHERE id = ?
    LIMIT 1
");

if (!$stmt) {
    die("Event query error: " . $conn->error);
}

$stmt->bind_param("i", $event_id);

if (!$stmt->execute()) {
    die("Event query execution error: " . $stmt->error);
}

$event_result = $stmt->get_result();

if ($event_result->num_rows == 0) {
    $stmt->close();
    die("Event not found.");
}

$event = $event_result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| CHECK ALREADY REGISTERED
|--------------------------------------------------------------------------
| Same email + same event = cannot register again
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT id
    FROM event_registrations
    WHERE event_id = ?
    AND LOWER(TRIM(student_email)) = LOWER(TRIM(?))
    LIMIT 1
");

if (!$stmt) {
    die("Registration check query error: " . $conn->error);
}

$stmt->bind_param(
    "is",
    $event_id,
    $student_email
);

if (!$stmt->execute()) {
    die("Registration check execution error: " . $stmt->error);
}

$check_result = $stmt->get_result();


/*
|--------------------------------------------------------------------------
| ALREADY REGISTERED
|--------------------------------------------------------------------------
*/

if ($check_result->num_rows > 0) {

    $existing = $check_result->fetch_assoc();

    $registration_id = intval($existing['id']);

    $stmt->close();

    /*
    | Redirect to existing registration receipt
    */

    header(
        "Location: my_event_registration.php?id="
        . $registration_id
    );

    exit;
}

$stmt->close();


/*
|--------------------------------------------------------------------------
| INSERT NEW REGISTRATION
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    INSERT INTO event_registrations
    (
        event_id,
        student_name,
        student_email,
        student_mobile,
        department,
        registered_at
    )
    VALUES (?, ?, ?, ?, ?, NOW())
");

if (!$stmt) {
    die(
        "Registration insert query error: "
        . $conn->error
    );
}


/*
|--------------------------------------------------------------------------
| BIND VALUES
|--------------------------------------------------------------------------
*/

$stmt->bind_param(
    "issss",
    $event_id,
    $student_name,
    $student_email,
    $student_mobile,
    $department
);


/*
|--------------------------------------------------------------------------
| INSERT
|--------------------------------------------------------------------------
*/

if ($stmt->execute()) {

    $registration_id = $conn->insert_id;

    $stmt->close();

    /*
    | IMPORTANT:
    | Do NOT redirect to save_event_registration.php
    | Redirect to receipt page.
    */

    header(
        "Location: my_event_registration.php?id="
        . $registration_id
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| ERROR
|--------------------------------------------------------------------------
*/

$error = $stmt->error;

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Registration Error</title>

<style>

* {
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    margin: 0;
    background: #f4f6f9;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 20px;
}

.error-box {
    background: white;
    padding: 35px;
    max-width: 550px;
    width: 100%;
    text-align: center;
    border-radius: 15px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.12);
}

h1 {
    color: #dc3545;
    margin-bottom: 15px;
}

p {
    color: #666;
    line-height: 1.6;
}

.error-message {
    background: #f8d7da;
    color: #721c24;
    padding: 12px;
    border-radius: 7px;
    margin-top: 15px;
    word-break: break-word;
}

.btn {
    display: inline-block;
    margin-top: 20px;
    padding: 12px 20px;
    background: #1769aa;
    color: white;
    text-decoration: none;
    border-radius: 7px;
    font-weight: bold;
}

</style>

</head>

<body>

<div class="error-box">

    <h1>Registration Failed</h1>

    <p>
        Something went wrong while saving your registration.
    </p>

    <div class="error-message">

        <?php
        echo htmlspecialchars($error);
        ?>

    </div>

    <a href="user_events.php" class="btn">
        ← Back to Events
    </a>

</div>

</body>

</html>