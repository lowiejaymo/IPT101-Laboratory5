<?php

require('db_conn.php');
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//this function use for sending verification button and verification code
function sendMail($email, $v_code)
{
    require("PHPMailer/PHPMailer.php");
    require("PHPMailer/SMTP.php");
    require("PHPMailer/Exception.php");

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'lowiejayorillolaboratory@gmail.com';
        $mail->Password = 'kscu rsfy rupo qvtg';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('lowiejayorillolaboratory@gmail.com', 'Account New Verification Code | ORILLO IPT101 LABORATORY');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Account Verification from ORILLO IPT101 LABORATORY';
        $mail->Body = "
        <html>
        <head>
            <style>
                /* Add your CSS styles here */
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    padding: 20px;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #fff;
                    padding: 30px;
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }
                h1 {
                    color: #333;
                }
                p {
                    color: #666;
                }
                a {
                    text-decoration: none;
                    color: #111;
                }
                .button {
                    display: inline-block;
                    background-color: #111111;
                    color: #ffffff;
                    padding: 10px 20px;
                    text-decoration: none;
                    border-radius: 5px;
                } 
                
                .button:hover {
                    background-color: #808080; 
                }
            </style>
        </head>
        <body>
            <div class='container'>
            <h1>New Verification Code!</h1>
            <h3>Thank you for registering with us. To finalize your account setup, please verify your account using the following verification code:</h3>
            <h3> New Verification Code: $v_code</h3>
            <hr>
            <p>Thank you</p>
            </div>
        </body>
        </html>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Check if the 'resend' form is submitted
if (isset($_POST['resend'])) {
    // Check if the email is provided, if not, set it to an empty string
    $email = isset($_POST['email']) ? $_POST['email'] : '';

    // Validate and sanitize the email
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // Check if the email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Redirect with an error message if the email is invalid
        header("Location: ../createdsuccessfully.php?newerror=Invalid email address.");
        exit();
    }

    // Generate a random 6-digit verification code
    $v_code = rand(100000, 999999);

    // Prepare and execute the SQL statement to update the user table with the new verification code
    $sql = "UPDATE user SET verification_code = ? WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "is", $v_code, $email);
    $result = mysqli_stmt_execute($stmt);

    // Check if the update was successful
    if ($result) {
        // Send the verification code via email
        if (sendMail($email, $v_code)) {
            // Redirect with a success message if the email is sent successfully
            header("Location: ../createdsuccessfully.php?newsuccess=Your new Verification Code has been sent to your email.");
            exit();
        } else {
            // Redirect with an error message if the email fails to send
            header("Location: ../createdsuccessfully.php?newerror=Failed to send the new Verification Code to your email.");
            exit();
        }
    } else {
        // Redirect with an error message if the database update fails
        header("Location: ../createdsuccessfully.php?newerror=Failed to update verification code.");
        exit();
    }
} else {
    header("Location: ../createdsuccessfully.php");
    exit();
}
?>