<?php

session_start();

include 'db.php';
include 'mail_config.php';

if (isset($_POST['send_otp'])) {

    $email = trim($_POST['email']);

    // Check user
    $sql = $conn->prepare(
        "SELECT id, username, email
         FROM users
         WHERE email = ?"
    );

    $sql->bind_param("s", $email);
    $sql->execute();

    $result = $sql->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        $username = $user['username'];

        // Generate 6 digit OTP
        $otp = rand(100000, 999999);

        // OTP valid for 10 minutes
        $expiry = date(
            "Y-m-d H:i:s",
            time() + 600
        );

        // Save OTP in database
        $update = $conn->prepare(
            "UPDATE users
             SET reset_otp = ?,
                 reset_otp_expiry = ?
             WHERE email = ?"
        );

        $update->bind_param(
            "sss",
            $otp,
            $expiry,
            $email
        );

        if ($update->execute()) {

            // Send OTP Email
            $emailSent = sendOTPEmail(
                $email,
                $username,
                $otp
            );

            if ($emailSent) {

                // Store email in session
                $_SESSION['reset_email'] = $email;

                echo "<script>
                        alert('OTP has been sent to your email!');
                        window.location.href='verify_reset_otp.php';
                      </script>";

                exit();

            } else {

                echo "<script>
                        alert('Failed to send OTP email. Please check mail configuration.');
                      </script>";
            }

        } else {

            echo "<script>
                    alert('Failed to save OTP. Please try again.');
                  </script>";
        }

    } else {

        echo "<script>
                alert('Email not registered!');
              </script>";
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Forgot Password</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {

            min-height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;

            font-family: Arial, sans-serif;

            background:
                radial-gradient(
                    circle at top left,
                    #667eea 0%,
                    transparent 35%
                ),
                radial-gradient(
                    circle at bottom right,
                    #764ba2 0%,
                    transparent 35%
                ),
                linear-gradient(
                    135deg,
                    #0f172a,
                    #1e293b
                );
        }

        .container {

            width: 400px;
            max-width: 92%;

            padding: 40px 35px;

            background: rgba(255,255,255,0.10);

            backdrop-filter: blur(20px);

            border: 1px solid rgba(255,255,255,0.20);

            border-radius: 24px;

            box-shadow:
                0 25px 60px rgba(0,0,0,0.35);

            text-align: center;
        }

        h1 {

            color: white;

            margin-bottom: 15px;

            font-size: 30px;
        }

        .description {

            color: rgba(255,255,255,0.75);

            font-size: 14px;

            margin-bottom: 25px;

            line-height: 1.5;
        }

        input {

            width: 100%;

            height: 52px;

            padding: 0 16px;

            border-radius: 12px;

            border: 1px solid rgba(255,255,255,0.25);

            outline: none;

            background: rgba(255,255,255,0.12);

            color: white;

            font-size: 15px;
        }

        input::placeholder {

            color: rgba(255,255,255,0.65);
        }

        input:focus {

            border-color: #8b5cf6;

            box-shadow:
                0 0 0 3px rgba(139,92,246,0.18);
        }

        button {

            width: 100%;

            height: 52px;

            margin-top: 20px;

            border: none;

            border-radius: 12px;

            background:
                linear-gradient(
                    135deg,
                    #6366f1,
                    #8b5cf6,
                    #a855f7
                );

            color: white;

            font-size: 16px;

            font-weight: 600;

            cursor: pointer;

            transition: 0.3s;
        }

        button:hover {

            transform: translateY(-2px);

            box-shadow:
                0 10px 25px rgba(139,92,246,0.40);
        }

        .back {

            display: block;

            margin-top: 22px;

            color: #c4b5fd;

            text-decoration: none;

            font-size: 14px;

            font-weight: 600;
        }

        .back:hover {

            color: white;

            text-decoration: underline;
        }

    </style>

</head>

<body>

    <div class="container">

        <h1>Forgot Password?</h1>

        <p class="description">
            Enter your registered email address.
            We will send you an OTP to reset your password.
        </p>

        <form method="POST">

            <input
                type="email"
                name="email"
                placeholder="Enter Registered Email"
                required
            >

            <button
                type="submit"
                name="send_otp"
            >
                Send OTP
            </button>

        </form>

        <a href="login.php" class="back">
            ← Back to Login
        </a>

    </div>

</body>

</html>