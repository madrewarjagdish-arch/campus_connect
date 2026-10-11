<?php

session_start();

include 'db.php';
include 'mail_config.php';


// =====================================================
// CHECK EMAIL SESSION
// =====================================================

if (!isset($_SESSION['verify_email'])) {

    header("Location: register.php");
    exit();

}

$email = $_SESSION['verify_email'];


// =====================================================
// VERIFY OTP
// =====================================================

if (isset($_POST['verify'])) {

    $entered_otp = trim($_POST['otp']);

    if (!preg_match('/^[0-9]{6}$/', $entered_otp)) {

        echo "<script>
                alert('Please enter a valid 6-digit OTP.');
              </script>";

    } else {

        $sql = $conn->prepare(
            "SELECT id, username, verification_otp, otp_expiry
             FROM users
             WHERE email = ?
             LIMIT 1"
        );

        $sql->bind_param("s", $email);
        $sql->execute();

        $result = $sql->get_result();

        if ($result->num_rows == 1) {

            $user = $result->fetch_assoc();


            // Check OTP
            if ($entered_otp === $user['verification_otp']) {

                // Check expiry
                if (
                    !empty($user['otp_expiry']) &&
                    strtotime($user['otp_expiry']) >= time()
                ) {

                    // Verify email
                    $update = $conn->prepare(
                        "UPDATE users
                         SET email_verified = 1,
                             verification_otp = NULL,
                             otp_expiry = NULL
                         WHERE id = ?"
                    );

                    $update->bind_param("i", $user['id']);

                    if ($update->execute()) {

                        unset($_SESSION['verify_email']);

                        echo "<script>
                                alert('Email verified successfully!');
                                window.location.href='login.php';
                              </script>";

                        exit();

                    } else {

                        echo "<script>
                                alert('Something went wrong. Please try again.');
                              </script>";

                    }

                } else {

                    echo "<script>
                            alert('OTP has expired. Please resend OTP.');
                          </script>";

                }

            } else {

                echo "<script>
                        alert('Invalid OTP! Please enter the correct OTP.');
                      </script>";

            }

        } else {

            echo "<script>
                    alert('User not found!');
                  </script>";

        }

    }

}


// =====================================================
// RESEND OTP
// =====================================================

if (isset($_POST['resend'])) {

    $sql = $conn->prepare(
        "SELECT id, username, email_verified
         FROM users
         WHERE email = ?
         LIMIT 1"
    );

    $sql->bind_param("s", $email);
    $sql->execute();

    $result = $sql->get_result();


    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();


        // Check already verified
        if ($user['email_verified'] == 1) {

            echo "<script>
                    alert('This email is already verified.');
                    window.location.href='login.php';
                  </script>";

            exit();

        }


        // Generate new OTP
        $new_otp = str_pad(
            random_int(0, 999999),
            6,
            '0',
            STR_PAD_LEFT
        );


        // OTP valid for 10 minutes
        $new_expiry = date(
            'Y-m-d H:i:s',
            time() + (10 * 60)
        );


        // Update OTP in database
        $update = $conn->prepare(
            "UPDATE users
             SET verification_otp = ?,
                 otp_expiry = ?
             WHERE id = ?"
        );

        $update->bind_param(
            "ssi",
            $new_otp,
            $new_expiry,
            $user['id']
        );


        if ($update->execute()) {

            // Send new OTP
            $mail_sent = sendOTPEmail(
                $email,
                $user['username'],
                $new_otp
            );


            if ($mail_sent) {

                echo "<script>
                        alert('New OTP has been sent to your email.');
                      </script>";

            } else {

                // If email failed, clear OTP
                $clear = $conn->prepare(
                    "UPDATE users
                     SET verification_otp = NULL,
                         otp_expiry = NULL
                     WHERE id = ?"
                );

                $clear->bind_param("i", $user['id']);
                $clear->execute();

                echo "<script>
                        alert('Failed to send OTP. Please check mail configuration.');
                      </script>";

            }

        } else {

            echo "<script>
                    alert('Failed to generate new OTP.');
                  </script>";

        }

    } else {

        echo "<script>
                alert('User not found!');
                window.location.href='register.php';
              </script>";

        exit();

    }

}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Verify OTP - CampusConnect</title>


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


        .otp-box {

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

            text-align: center;

        }


        .otp-box h2 {

            margin-bottom: 15px;

        }


        .otp-box p {

            margin-bottom: 25px;

            font-size: 14px;

        }


        .otp-input {

            width: 100%;

            padding: 15px;

            border: none;

            outline: none;

            border-radius: 10px;

            text-align: center;

            font-size: 22px;

            letter-spacing: 8px;

            margin-bottom: 20px;

        }


        .verify-btn {

            width: 100%;

            padding: 14px;

            border: none;

            border-radius: 10px;

            background: white;

            color: #764ba2;

            font-size: 16px;

            font-weight: bold;

            cursor: pointer;

        }


        .verify-btn:hover {

            transform: scale(1.03);

        }


        .resend-form {

            margin-top: 15px;

        }


        .resend-btn {

            width: 100%;

            padding: 12px;

            border: 1px solid white;

            border-radius: 10px;

            background: transparent;

            color: white;

            font-size: 15px;

            font-weight: bold;

            cursor: pointer;

        }


        .resend-btn:hover {

            background: rgba(255,255,255,0.15);

        }


        .email-text {

            font-size: 13px;

            margin-top: 10px;

            opacity: 0.9;

            word-break: break-word;

        }

    </style>

</head>


<body>


<div class="otp-box">

    <h2>Verify Email</h2>


    <p>
        Enter the 6-digit OTP sent to your email.
    </p>


    <div class="email-text">
        <?php echo htmlspecialchars($email); ?>
    </div>


    <br>


    <!-- VERIFY OTP -->

    <form method="POST">

        <input
            type="text"
            name="otp"
            class="otp-input"
            placeholder="000000"
            maxlength="6"
            pattern="[0-9]{6}"
            inputmode="numeric"
            required
        >


        <button
            type="submit"
            name="verify"
            class="verify-btn"
        >
            Verify OTP
        </button>

    </form>


    <!-- RESEND OTP -->

    <form method="POST" class="resend-form">

        <button
            type="submit"
            name="resend"
            class="resend-btn"
        >
            Resend OTP
        </button>

    </form>

</div>


</body>

</html>