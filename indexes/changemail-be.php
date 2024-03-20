<?php
require('db_conn.php');
session_start();

if (isset($_POST['change_email_password'])) {

    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Validate and sanitize input
    $requestCode = validate($_POST['request_code']);
    $password = validate($_POST['change_email_password']);
    $currentEmail = $_SESSION['email'];
    $username = $_SESSION['username'];
    $newEmail = $_SESSION['new_email'];

    $stored_password = $_SESSION['password'];// getting the stored password

    $user_data = 'request_code_data=' . $requestCode;

    // Prepare and execute statement to check for new email
    $checkNewEmail = "SELECT * FROM user WHERE username = ? AND new_email = ?";
    $checkNewEmailstmt = mysqli_prepare($conn, $checkNewEmail);
    mysqli_stmt_bind_param($checkNewEmailstmt, "ss", $username, $newEmail);
    mysqli_stmt_execute($checkNewEmailstmt);
    $checkNewEmailresult = mysqli_stmt_get_result($checkNewEmailstmt);

    if (empty($requestCode)) {
        //if the request code was empty
        header("Location: ../profile.php?requestcodeerror=Verification Code is required&$user_data");
        exit();
    } elseif (empty($password)) {
        // if the password is empty
        header("Location: ../profile.php?requestcodeerror=Password is required&$user_data");
        exit();
    } elseif (!password_verify($password, $stored_password)) {
        //if the input password and the stored password didn't match
        header("Location: ../profile.php?requestcodeerror=Incorrect Password");
        exit();
    } else {
        // Prepare and execute statement to check verification code
        $sql = "SELECT * FROM user WHERE Email = ? AND verification_code = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $currentEmail, $requestCode);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) == 0) {
            //if the verification code is incorrect
            header("Location: ../profile.php?requestcodeerror=Verification Code is incorrect");
            exit();
        } else {
            // Check if new email is not empty or blank
            if (mysqli_num_rows($checkNewEmailresult) == 0) {
                header("Location: ../profile.php?requestcodeerror=Email Address is already existing");
                exit();
            }

            // Verification successful, update email in the database
            $updateSql = "UPDATE user SET Email = ? WHERE username = ?";
            $updateStmt = mysqli_prepare($conn, $updateSql);
            mysqli_stmt_bind_param($updateStmt, "ss", $newEmail, $username);
            if (mysqli_stmt_execute($updateStmt)) {
                $updateSql2 = "UPDATE user SET new_email = '' WHERE username = ?";
                $updateStmt2 = mysqli_prepare($conn, $updateSql2);
                mysqli_stmt_bind_param($updateStmt2, "s", $username);
                mysqli_stmt_execute($updateStmt2);

                $_SESSION['email'] = $newEmail;
                $_SESSION['new_email'] = "";
                header("Location: ../profile.php?sencodesuccess=Email updated successfully");
                exit();
            } else {
                header("Location: ../profile.php?requestcodeerror=Failed to update email");
                exit();
            }
        }
    }
} else {
    header("Location: ../profile.php");
    exit();
}
