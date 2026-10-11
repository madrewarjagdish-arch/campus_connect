<?php

session_start();

include 'db.php';
include 'mail_config.php';

if (isset($_POST['submit'])) {

    // Get form data
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // ==============================
    // VALIDATION
    // ==============================

    if ($username === '' || $email === '' || $password === '') {

        echo "<script>
                alert('Please fill all fields!');
              </script>";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        echo "<script>
                alert('Invalid email address!');
              </script>";

    } else {

        // ==============================
        // CHECK EMAIL
        // ==============================

        $check = $conn->prepare(
            "SELECT id, email_verified FROM users WHERE LOWER(email) = LOWER(?) LIMIT 1"
        );

        if (!$check) {
            die("Database Error: " . $conn->error);
        }

        $check->bind_param("s", $email);
        $check->execute();

        $result = $check->get_result();

        // ==============================
        // EMAIL ALREADY EXISTS
        // ==============================

        if ($result->num_rows > 0) {

            $existingUser = $result->fetch_assoc();

            if ((int)$existingUser['email_verified'] === 0) {

                echo "<script>
                        alert('This email is registered but not verified. Please use another email or verify the existing account.');
                      </script>";

            } else {

                echo "<script>
                        alert('Email already registered!');
                      </script>";
            }

            $check->close();

        } else {

            // ==============================
            // HASH PASSWORD
            // ==============================

            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // ==============================
            // GENERATE OTP
            // ==============================

            $otp = (string) random_int(100000, 999999);

            // OTP valid for 10 minutes
            $otp_expiry = date(
                "Y-m-d H:i:s",
                time() + (10 * 60)
            );

            $email_verified = 0;

            // ==============================
            // INSERT USER
            // ==============================

            $sql = $conn->prepare(
                "INSERT INTO users
                (
                    username,
                    email,
                    password,
                    email_verified,
                    verification_otp,
                    otp_expiry
                )
                VALUES (?, ?, ?, ?, ?, ?)"
            );

            if (!$sql) {
                die("Insert Query Error: " . $conn->error);
            }

            $sql->bind_param(
                "sssiss",
                $username,
                $email,
                $hashed_password,
                $email_verified,
                $otp,
                $otp_expiry
            );

            if ($sql->execute()) {

                // ==============================
                // SEND OTP EMAIL
                // ==============================

                $emailSent = sendOTPEmail(
                    $email,
                    $username,
                    $otp
                );

                if ($emailSent) {

                    $_SESSION['verify_email'] = $email;

                    echo "<script>
                            alert('Registration successful! OTP sent to your email.');
                            window.location.href='verify_otp.php';
                          </script>";

                    exit();

                } else {

                    // Delete user if OTP email fails
                    $delete = $conn->prepare(
                        "DELETE FROM users WHERE email = ?"
                    );

                    if ($delete) {
                        $delete->bind_param("s", $email);
                        $delete->execute();
                        $delete->close();
                    }

                    echo "<script>
                            alert('OTP could not be sent. Please check Gmail SMTP settings.');
                          </script>";
                }

            } else {

                echo "<script>
                        alert('Registration failed: " .
                        addslashes($sql->error) .
                        "');
                      </script>";
            }

            $sql->close();
            $check->close();
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>CampusConnect - Register</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {

            min-height: 100vh;

            display: flex;

            justify-content: center;

            align-items: center;

            background: linear-gradient(
                135deg,
                #667eea,
                #764ba2
            );
        }

        .register-box {

            width: 400px;

            padding: 40px;

            border-radius: 20px;

            background: rgba(255,255,255,0.15);

            backdrop-filter: blur(15px);

            box-shadow:
                0 8px 32px rgba(0,0,0,0.25);

            border:
                1px solid rgba(255,255,255,0.2);

            color: white;
        }

        .register-box h2 {

            text-align: center;

            margin-bottom: 30px;
        }

        .input-box {

            margin-bottom: 20px;
        }

        .input-box label {

            display: block;

            margin-bottom: 8px;

            font-size: 14px;
        }

        .input-box input {

            width: 100%;

            padding: 13px;

            border: none;

            outline: none;

            border-radius: 10px;

            background: rgba(255,255,255,0.9);

            color: #333;

            font-size: 15px;
        }

        .register-btn {

            width: 100%;

            padding: 13px;

            border: none;

            border-radius: 10px;

            background: white;

            color: #764ba2;

            font-size: 16px;

            font-weight: bold;

            cursor: pointer;

            transition: 0.3s;
        }

        .register-btn:hover {

            transform: scale(1.03);
        }

        .login-link {

            text-align: center;

            margin-top: 20px;
        }

        .login-link a {

            color: white;

            font-weight: bold;

            text-decoration: none;
        }

    </style>

</head>


<body>


<div class="register-box">

    <h2>CampusConnect</h2>


    <form method="POST">

        <div class="input-box">

            <label>Username</label>

            <input
                type="text"
                name="username"
                placeholder="Enter username"
                required
            >

        </div>


        <div class="input-box">

            <label>Email</label>

            <input
                type="email"
                name="email"
                placeholder="Enter email"
                required
            >

        </div>


        <div class="input-box">

            <label>Password</label>

            <input
                type="password"
                name="password"
                placeholder="Enter password"
                required
            >

        </div>


        <button
            type="submit"
            name="submit"
            class="register-btn"
        >
            Register
        </button>


    </form>


    <div class="login-link">

        Already have an account?

        <a href="login.php">
            Login
        </a>

    </div>


</div>


</body>

</html>