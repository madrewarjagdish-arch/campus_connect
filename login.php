<?php

session_start();
include 'db.php';

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Get user details
    $sql = $conn->prepare(
        "SELECT id, username, email, password, email_verified
         FROM users
         WHERE email = ?"
    );

    $sql->bind_param("s", $email);
    $sql->execute();

    $result = $sql->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        // Check password
        if (password_verify($password, $user['password'])) {

            // Check Email Verification
            if ($user['email_verified'] == 0) {

                // Store email for OTP verification
                $_SESSION['verify_email'] = $user['email'];

                echo "<script>
                        alert('Please verify your email first!');
                        window.location.href='verify_otp.php';
                      </script>";

                exit();
            }

            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];

            // Your dashboard path
            header("Location: ../dashboard.php");
            exit();
        } else {

            echo "<script>
                    alert('Wrong Password!');
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

    <title>Login</title>

    <link rel="stylesheet" href="style.css">

    <style>
        /* ================================
       LOGIN PAGE - PROFESSIONAL STYLE
    ================================= */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            font-family: "Poppins", Arial, sans-serif;

            display: flex;
            justify-content: center;
            align-items: center;

            background:
                radial-gradient(circle at top left, #667eea 0%, transparent 35%),
                radial-gradient(circle at bottom right, #764ba2 0%, transparent 35%),
                linear-gradient(135deg, #0f172a, #1e293b);

            overflow: hidden;
        }

        /* Background decorative circles */

        body::before,
        body::after {
            content: "";
            position: fixed;
            border-radius: 50%;
            filter: blur(2px);
            z-index: -1;
        }

        body::before {
            width: 300px;
            height: 300px;
            background: rgba(99, 102, 241, 0.25);
            top: -100px;
            left: -100px;
        }

        body::after {
            width: 350px;
            height: 350px;
            background: rgba(168, 85, 247, 0.20);
            bottom: -150px;
            right: -100px;
        }

        /* Login Card */

        .container {
            width: 400px;
            max-width: 92%;
            padding: 42px 38px;

            background: rgba(255, 255, 255, 0.10);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);

            border: 1px solid rgba(255, 255, 255, 0.20);
            border-radius: 24px;

            box-shadow:
                0 25px 60px rgba(0, 0, 0, 0.35),
                inset 0 1px 1px rgba(255, 255, 255, 0.15);

            text-align: center;

            animation: loginBox 0.7s ease;
        }

        /* Heading */

        .container h1 {
            color: #ffffff;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 30px;
            letter-spacing: 0.5px;
        }

        /* Inputs */

        .container input {
            width: 100%;
            height: 52px;

            padding: 0 17px;

            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 12px;

            outline: none;

            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;

            font-size: 15px;

            transition: all 0.3s ease;
        }

        /* Placeholder */

        .container input::placeholder {
            color: rgba(255, 255, 255, 0.65);
        }

        /* Input focus */

        .container input:focus {
            border-color: #8b5cf6;

            background: rgba(255, 255, 255, 0.17);

            box-shadow:
                0 0 0 3px rgba(139, 92, 246, 0.18),
                0 8px 20px rgba(0, 0, 0, 0.15);

            transform: translateY(-1px);
        }

        /* Login Button */

        .container button {
            width: 100%;
            height: 52px;

            margin-top: 8px;

            border: none;
            border-radius: 12px;

            background: linear-gradient(135deg,
                    #6366f1,
                    #8b5cf6,
                    #a855f7);

            color: #ffffff;

            font-size: 16px;
            font-weight: 600;

            cursor: pointer;

            box-shadow:
                0 10px 25px rgba(99, 102, 241, 0.35);

            transition: all 0.3s ease;
        }

        /* Button Hover */

        .container button:hover {
            transform: translateY(-3px);

            box-shadow:
                0 15px 30px rgba(139, 92, 246, 0.45);

            background: linear-gradient(135deg,
                    #4f46e5,
                    #7c3aed,
                    #9333ea);
        }

        /* Button Click */

        .container button:active {
            transform: translateY(0);
        }

        /* Register Text */

        .container p {
            margin-top: 25px;

            color: rgba(255, 255, 255, 0.75);

            font-size: 14px;
        }

        /* Register Link */

        .container a {
            color: #c4b5fd;

            font-weight: 600;
            text-decoration: none;

            margin-left: 4px;

            transition: 0.3s ease;
        }

        .container a:hover {
            color: #ffffff;
            text-decoration: underline;
        }

        /* Animation */

        @keyframes loginBox {

            from {
                opacity: 0;
                transform: translateY(30px) scale(0.96);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Mobile Responsive */

        @media (max-width: 480px) {

            .container {
                padding: 35px 25px;
                border-radius: 20px;
            }

            .container h1 {
                font-size: 27px;
            }

            .container input,
            .container button {
                height: 50px;
            }
        }
    </style>

</head>

<body>

    <div class="container">

        <h1>Login</h1>

        <form action="" method="POST">

            <input
                type="email"
                name="email"
                placeholder="Enter Email"
                required>

            <br><br>

            <input
                type="password"
                name="password"
                placeholder="Enter Password"
                required>

            <br><br>

            <button
                type="submit"
                name="login">
                Login
            </button>

        </form>

        <p>
            Don't have an account?
            <a href="register.php">Register</a>

        </p>

        <p>Don't have forgot an account password?
            <a href="forgot_password.php">Forgot Password</a>
        </p>

    </div>

</body>

</html>