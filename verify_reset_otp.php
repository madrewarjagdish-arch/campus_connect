<?php

session_start();
include 'db.php';

// Check reset email session
if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

$email = $_SESSION['reset_email'];

if (isset($_POST['verify_otp'])) {

    $otp = trim($_POST['otp']);

    // Get OTP details
    $sql = $conn->prepare(
        "SELECT reset_otp, reset_otp_expiry
         FROM users
         WHERE email = ?"
    );

    $sql->bind_param("s", $email);
    $sql->execute();

    $result = $sql->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        // Check OTP
        if ($user['reset_otp'] != $otp) {

            echo "<script>
                    alert('Invalid OTP!');
                  </script>";

        }
        // Check OTP expiry
        elseif (strtotime($user['reset_otp_expiry']) < time()) {

            echo "<script>
                    alert('OTP has expired. Please request a new OTP.');
                    window.location.href='forgot_password.php';
                  </script>";

            exit();

        }
        else {

            // OTP verified
            $_SESSION['reset_verified'] = true;

            header("Location: reset_password.php");
            exit();
        }

    } else {

        echo "<script>
                alert('User not found!');
              </script>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>Verify OTP</title>

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

            font-size: 18px;

            text-align: center;

            letter-spacing: 5px;
        }

        input::placeholder {

            color: rgba(255,255,255,0.60);

            letter-spacing: normal;
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

        <h1>Verify OTP</h1>

        <p class="description">
            Enter the 6-digit OTP sent to your registered email.
        </p>

        <form method="POST">

            <input
                type="text"
                name="otp"
                placeholder="Enter OTP"
                maxlength="6"
                pattern="[0-9]{6}"
                required
            >

            <button
                type="submit"
                name="verify_otp"
            >
                Verify OTP
            </button>

        </form>

        <a href="forgot_password.php" class="back">
            ← Request New OTP
        </a>

    </div>

</body>

</html>