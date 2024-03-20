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

        $mail->setFrom('lowiejayorillolaboratory@gmail.com', 'Account Verification | ORILLO IPT101 LABORATORY');
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
                <h1>Thank you for signing up!</h1>
                <h3>Thank you for registering with us. To finalize your account setup, please verify your account using the following verification code:</h3>
                <h3> Verification Code: $v_code</h3>
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
// Check if the registration form is submitted
if (isset($_POST['register'])) {

    // Function to validate and sanitize user input
    function validate($data)
    {
        $data = trim($data); // Remove whitespace from the beginning and end of string
        $data = stripslashes($data); // Remove backslashes
        $data = htmlspecialchars($data); // Convert special characters to HTML entities
        return $data;
    }

    // Validate and sanitize form inputs
    $lname = validate($_POST['lastname']);
    $fname = validate($_POST['firstname']);
    $mname = validate($_POST['middlename']);
    $uname = validate($_POST['uname']);
    $email = validate($_POST['email']);
    $pass = validate($_POST['password']);
    $repass = validate($_POST['repassword']);
    $tandc = isset($_POST['tandc']) ? $_POST['tandc'] : '';

    // Build query string to preserve user data for error handling
    $user_data = 'lname=' . $lname . '&fname=' . $fname . '&mname=' . $mname . '&uname=' . $uname . '&email=' . $email;

    // Check if all required fields are provided
    if (empty($lname) || empty($fname) || empty($mname) || empty($uname) || empty($email) || empty($pass) || empty($repass) || empty($tandc)) {
        header("Location: ../register-v2.php?error=All fields are required&$user_data");
        exit();
    }

    // Check if passwords match
    if ($pass !== $repass) {
        header("Location: ../register-v2.php?error=Passwords do not match&$user_data");
        exit();
    }

    // Hash the password
    $hashed_pass = password_hash($pass, PASSWORD_BCRYPT);

    // Prepare and execute SQL statement to check if username is taken
    $sql_username_check = "SELECT * FROM user WHERE username=?";
    $stmt_username_check = mysqli_prepare($conn, $sql_username_check);
    mysqli_stmt_bind_param($stmt_username_check, "s", $uname);
    mysqli_stmt_execute($stmt_username_check);
    $result_username_check = mysqli_stmt_get_result($stmt_username_check);

    // Check if username is already taken
    if (mysqli_num_rows($result_username_check) > 0) {
        header("Location: ../register-v2.php?error=Username is already taken&$user_data");
        exit();
    }

    // Prepare and execute SQL statement to check if email is already registered
    $sql_email_check = "SELECT * FROM user WHERE email=?";
    $stmt_email_check = mysqli_prepare($conn, $sql_email_check);
    mysqli_stmt_bind_param($stmt_email_check, "s", $email);
    mysqli_stmt_execute($stmt_email_check);
    $result_email_check = mysqli_stmt_get_result($stmt_email_check);

    // Check if email is already registered
    if (mysqli_num_rows($result_email_check) > 0) {
        header("Location: ../register-v2.php?error=Email address is already registered&$user_data");
        exit();
    }

    // Generate a random 6-digit verification code
    $v_code = rand(100000, 999999);

    // Prepare and execute SQL statement to insert new user data
    $sql_insert_user = "INSERT INTO user(username, password, Lastname, First_name, Middle_name, Email, verification_code, is_verified) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, '0')";
    $stmt_insert_user = mysqli_prepare($conn, $sql_insert_user);
    mysqli_stmt_bind_param($stmt_insert_user, "sssssss", $uname, $hashed_pass, $lname, $fname, $mname, $email, $v_code);
    $result_insert_user = mysqli_stmt_execute($stmt_insert_user);

    if ($result_insert_user && sendMail($email, $v_code)) {
        // Set session variables for email and verification status
        $_SESSION['verify'] = true;
        $_SESSION['email'] = $email;

        // Redirect to success page
        header("Location: ../createdsuccessfully.php?success=Your account has been registered. To complete the registration, please check your email.");
        exit();
    } else {
        // Redirect with error message if insertion fails
        header("Location: ../register-v2.php?error=Failed to register user");
        exit();
    }
} else {
    // Redirect if registration form is not submitted
    header("Location: ../register-v2.php");
    exit();
}
?>