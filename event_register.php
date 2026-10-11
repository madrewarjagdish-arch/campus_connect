<?php

include 'db.php';

// Check event ID
if (!isset($_GET['event_id'])) {
    die("Event not selected.");
}

$event_id = intval($_GET['event_id']);

// Get event details
$sql = "SELECT * FROM events WHERE id = $event_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Event not found.");
}

$event = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Event Registration</title>

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
            max-width: 650px;
            margin: auto;
        }

        .title {
            text-align: center;
            margin-bottom: 25px;
        }

        .title h1 {
            color: #222;
            margin-bottom: 8px;
        }

        .title p {
            color: #666;
        }

        .event-box {
            background: #1769aa;
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .event-box h2 {
            margin-bottom: 12px;
        }

        .event-box p {
            margin: 7px 0;
        }

        .form-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.10);
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
            color: #333;
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        input:focus,
        select:focus {
            border-color: #1769aa;
            outline: none;
        }

        .register-btn {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 7px;
            background: #1769aa;
            color: white;
            font-size: 17px;
            font-weight: bold;
            cursor: pointer;
        }

        .register-btn:hover {
            background: #0d4f82;
        }

        .back-btn {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #1769aa;
            text-decoration: none;
        }

        @media (max-width: 600px) {

            body {
                padding: 20px 12px;
            }

            .form-box {
                padding: 20px;
            }

        }
    </style>

</head>

<body>

    <div class="container">

        <div class="title">

            <h1>Event Registration</h1>

            <p>Register for the selected college event</p>

        </div>


        <!-- Event Details -->

        <div class="event-box">



            <p>
                📅 <strong>Date:</strong>
                <?php echo date("d F Y", strtotime($event['event_date'])); ?>
            </p>

            <p>
                📍 <strong>Venue:</strong>
                <?php echo htmlspecialchars($event['venue']); ?>
            </p>

        </div>


        <!-- Registration Form -->

        <div class="form-box">

            <form action="save_event_registration.php" method="POST">

                <!-- Event ID -->

                <input type="hidden"
                    name="event_id"
                    value="<?php echo $event['id']; ?>">


                <!-- Student Name -->

                <label>Student Name</label>

                <input type="text"
                    name="student_name"
                    placeholder="Enter your full name"
                    required>


                <!-- Email -->

                <label>Email Address</label>

                <input type="email"
                    name="student_email"
                    placeholder="Enter your email"
                    required>


                <!-- Mobile -->

                <label>Mobile Number</label>

                <input type="tel"
                    name="student_mobile"
                    placeholder="Enter your mobile number"
                    maxlength="15"
                    required>


                <!-- Department -->

                <label>Department</label>

                <select name="department" required>

                    <option value="">Select Department</option>

                    <option value="Computer Engineering">
                        Computer Engineering
                    </option>

                    <option value="Information Technology">
                        Information Technology
                    </option>

                    <option value="Mechanical Engineering">
                        Mechanical Engineering
                    </option>

                    <option value="Civil Engineering">
                        Civil Engineering
                    </option>

                    <option value="Electrical Engineering">
                        Electrical Engineering
                    </option>

                    <option value="Electronics Engineering">
                        Electronics Engineering
                    </option>

                </select>


                <!-- Submit -->

                <button type="submit"
                    name="register"
                    class="register-btn">

                    Register Now

                </button>

            </form>


            <a href="user_events.php" class="back-btn">
                ← Back to Events
            </a>

        </div>

    </div>

</body>

</html>
```