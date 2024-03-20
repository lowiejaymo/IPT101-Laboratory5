<?php
session_start();

include "db_conn.php";

if (
    isset($_POST['currentPassword']) && isset($_POST['newPassword'])
    && isset($_POST['retypeNewPassword'])
) {
    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $currentPassword = validate($_POST['currentPassword']);
    $newPassword = validate($_POST['newPassword']);
    $retypeNewPassword = validate($_POST['retypeNewPassword']);

    // Fetch username from session
    $uname = $_SESSION['username'];

    // Check if new passwords match
    if ($newPassword !== $retypeNewPassword) {
        // if the password and retype password didn't match
        header("Location: ../profile.php?passerror=Passwords do not match.");
        exit();
    }

    // Prepare and execute statement to retrieve stored password
    $sql = "SELECT password FROM user WHERE username=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $uname);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $storedPassword = $row['password'];
        // if the current or old password was incorrect
        if (!password_verify($currentPassword, $storedPassword)) {
            header("Location: ../profile.php?passerror=Incorrect current password.");
            exit();
        }
    } else {
        header("Location: ../profile.php?passerror=User not found.");
        exit();
    }

    // Hash the new password
    $hashed_new_password = password_hash($newPassword, PASSWORD_DEFAULT);

    // Prepare and execute statement to update password
    $update_sql = "UPDATE user SET password=? WHERE username=?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "ss", $hashed_new_password, $uname);
    $update_result = mysqli_stmt_execute($update_stmt);

    if ($update_result) {
        $_SESSION['password'] = $hashed_new_password; // Update the stored password in the session
        header("Location: ../profile.php?passsuccess=Password updated successfully.");
        exit();
    } else {
        header("Location: ../profile.php?error=Failed to update password.");
        exit();
    }
} else {
    header("Location: ../profile.php");
    exit();
}
