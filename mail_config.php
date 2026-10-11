<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// PHPMailer files
require "PHPMailer/src/PHPMailer.php";
require "PHPMailer/src/SMTP.php";
require "PHPMailer/src/Exception.php";

function sendOTPEmail($toEmail, $username, $otp)
{
    $mail = new PHPMailer(true);

    try {

        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;

        // YOUR GMAIL
        $mail->Username   = 'madrewaranil1@gmail.com
';

        // YOUR GMAIL APP PASSWORD
        $mail->Password   = 'gehq fcfh cdfy bwkv';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;


        // Sender
        $mail->setFrom(
            'madrewaranil1@gmail.com',
            'CampusConnect'
        );


        // Receiver
        $mail->addAddress($toEmail, $username);


        // Email format
        $mail->isHTML(true);

        $mail->Subject = 'CampusConnect - Email Verification OTP';


        $mail->Body = "
        <div style='
            font-family: Arial;
            padding: 20px;
            background: #f5f5f5;
        '>

            <h2>CampusConnect</h2>

            <p>Hello <b>$username</b>,</p>

            <p>
                Thank you for registering with CampusConnect.
            </p>

            <p>
                Your Email Verification OTP is:
            </p>

            <h1 style='
                letter-spacing: 10px;
                background: white;
                padding: 15px;
                width: fit-content;
            '>
                $otp
            </h1>

            <p>
                This OTP is valid for <b>10 minutes</b>.
            </p>

            <p>
                If you did not create this account,
                please ignore this email.
            </p>

            <br>

            <p>
                Regards,<br>
                <b>CampusConnect Team</b>
            </p>

        </div>
        ";


        $mail->AltBody =
            "Your CampusConnect OTP is: $otp";


        // Send email
        $mail->send();

        return true;

    } catch (Exception $e) {

        return false;
    }
}
?>