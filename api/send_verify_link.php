<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
session_start();

require '../vendor/autoload.php';
function send_verify_email($email, $verification_code) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP(); 
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "hulomjosuanleonardo@gmail.com";
        $mail->Password = "xxzr ktwt dstj ugtx";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('no-reply@email.com', 'School');
        $mail->addAddress($email);

        $mail->isHTML(true);  
        $mail->Subject = 'Verify Your Email Address';

        $mail->Body = "
            <html>
            <head>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        color: #333;
                        background-color: #f4f4f4;
                        padding: 20px;
                    }
                    .container {
                        background-color: #ffffff;
                        padding: 30px;
                        border-radius: 10px;
                        width: 80%;
                        max-width: 600px;
                        margin: 0 auto;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                    }
                    .header {
                        text-align: center;
                        font-size: 24px;
                        margin-bottom: 20px;
                    }
                    .verification-code {
                        display: block;
                        margin: 20px 0;
                        font-size: 36px;
                        font-weight: bold;
                        text-align: center;
                        color: #2a9d8f;
                        background-color: #e9f6f1;
                        padding: 10px 20px;
                        border-radius: 8px;
                        letter-spacing: 3px;
                    }
                    .footer {
                        margin-top: 30px;
                        text-align: center;
                        font-size: 14px;
                        color: #666;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Verify Your Email Address</h1>
                    </div>
                    <p>Thank you for signing up. To complete the verification process, please use the following 6-digit code:</p>
                    <div class='verification-code'>
                        $verification_code
                    </div>
                    <p>This code will expire in 15 minutes. If you did not request this, please ignore this email.</p>
                    <div class='footer'>
                        <p>&copy; 2025 School. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";

        $mail->send();
    } catch (Exception $e) {
        throw new Exception("Email could not be sent. Error: " . $mail->ErrorInfo);
    }
}
