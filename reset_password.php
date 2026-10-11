<?php

session_start();
include 'db.php';

// Check whether OTP was verified
if (!isset($_SESSION['reset_verified']) || $_SESSION['reset_verified'] !== true) {
    header("Location: forgot_password.php");
    exit();
}

// Check reset email
if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

$email = $_SESSION['reset_email'];

if (isset($_POST['reset_password'])) {

    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check password match
    if ($password !== $confirm_password) {

        echo "<script>
                alert('Passwords do not match!');
              </script>";
    } elseif (strlen($password) < 6) {

        echo "<script>
                alert('Password must be at least 6 characters!');
              </script>";
    } else {

        // Hash new password
        $hashed_password = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        // Update password and clear reset OTP
        $sql = $conn->prepare(
            "UPDATE users
             SET password = ?,
                 reset_otp = NULL,
                 reset_otp_expiry = NULL
             WHERE email = ?"
        );

        $sql->bind_param(
            "ss",
            $hashed_password,
            $email
        );

        if ($sql->execute()) {

            // Clear reset session
            unset($_SESSION['reset_email']);
            unset($_SESSION['reset_verified']);

            echo "<script>
                    alert('Password reset successfully!');
                    window.location.href='login.php';
                  </script>";

            exit();
        } else {

            echo "<script>
                    alert('Something went wrong. Please try again.');
                  </script>";
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Reset Password</title>

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
                radial-gradient(circle at top left,
                    #667eea 0%,
                    transparent 35%),
                radial-gradient(circle at bottom right,
                    #764ba2 0%,
                    transparent 35%),
                linear-gradient(135deg,
                    #0f172a,
                    #1e293b);
        }

        .container {

            width: 400px;
            max-width: 92%;

            padding: 40px 35px;

            background: rgba(255, 255, 255, 0.10);

            backdrop-filter: blur(20px);

            border: 1px solid rgba(255, 255, 255, 0.20);

            border-radius: 24px;

            box-shadow:
                0 25px 60px rgba(0, 0, 0, 0.35);

            text-align: center;
        }

        h1 {

            color: white;

            margin-bottom: 15px;

            font-size: 30px;
        }

        .description {

            color: rgba(255, 255, 255, 0.75);

            font-size: 14px;

            margin-bottom: 25px;

            line-height: 1.5;
        }

        input {

            width: 100%;

            height: 52px;

            padding: 0 16px;

            margin-bottom: 15px;

            border-radius: 12px;

            border: 1px solid rgba(255, 255, 255, 0.25);

            outline: none;

            background: rgba(255, 255, 255, 0.12);

            color: white;

            font-size: 15px;
        }

        input::placeholder {

            color: rgba(255, 255, 255, 0.65);
        }

        input:focus {

            border-color: #8b5cf6;

            box-shadow:
                0 0 0 3px rgba(139, 92, 246, 0.18);
        }

        button {

            width: 100%;

            height: 52px;

            margin-top: 5px;

            border: none;

            border-radius: 12px;

            background:
                linear-gradient(135deg,
                    #6366f1,
                    #8b5cf6,
                    #a855f7);

            color: white;

            font-size: 16px;

            font-weight: 600;

            cursor: pointer;

            transition: 0.3s;
        }

        button:hover {

            transform: translateY(-2px);

            box-shadow:
                0 10px 25px rgba(139, 92, 246, 0.40);
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



        .password-box {
            position: relative;
            width: 100%;
            margin-bottom: 15px;
        }

        .password-box input {
            width: 100%;
            margin-bottom: 0;
            padding-right: 50px;
        }

        .eye-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);

            color: white;
            cursor: pointer;

            font-size: 20px;
            user-select: none;
        }

        .eye-icon:hover {
            opacity: 0.7;
        }
    </style>

</head>


<body>

    <div class="container">

        <h1>Reset Password</h1>

        <p class="description">
            Create a new password for your account.
        </p>

        <form method="POST">
            <div class="password-box">

                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Enter New Password"
                    minlength="6"
                    required>

                <span class="eye-icon" onclick="togglePassword('password', 'eye1')">
                    <i id="eye1">👁</i>
                </span>

            </div>


            <div class="password-box">

                <input
                    type="password"
                    name="confirm_password"
                    id="confirm_password"
                    placeholder="Confirm New Password"
                    minlength="6"
                    required>

                <span class="eye-icon" onclick="togglePassword('confirm_password', 'eye2')">
                    <i id="eye2">👁</i>
                </span>

            </div>
            <button
                type="submit"
                name="reset_password">
                Reset Password
            </button>

        </form>

        <a href="login.php" class="back">
            ← Back to Login
        </a>

    </div>


    <script>
        function togglePassword(inputId, eyeId) {

            const password = document.getElementById(inputId);
            const eye = document.getElementById(eyeId);

            if (password.type === "password") {

                password.type = "text";
                eye.innerHTML = "👁";

            } else {

                password.type = "password";
                eye.innerHTML = "👁";

            }
        }
    </script>

</body>

</html>