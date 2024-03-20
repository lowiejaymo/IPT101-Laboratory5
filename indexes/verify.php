<?php
require('db_conn.php'); // Include the database script to establish a connection with the database

if(isset($_GET['email']) && isset($_GET['v_code'])) {
    // Prepare and execute the SQL query using prepared statements
    $query = "SELECT * FROM `user` WHERE `Email` = ? AND `verification_code`= ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $_GET['email'], $_GET['v_code']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if($result) {
        if(mysqli_num_rows($result) == 1) { // If email exists in the database
            $result_fetch = mysqli_fetch_assoc($result);
            if($result_fetch['is_verified'] == 0) { // If the email is not verified
                // Update the verification status in the database
                $update = "UPDATE user SET is_verified='1' WHERE Email = ?";
                $stmt_update = mysqli_prepare($conn, $update);
                mysqli_stmt_bind_param($stmt_update, "s", $result_fetch['Email']);
                if(mysqli_stmt_execute($stmt_update)) { // If update is successful
                    header("Location: ../login-v2.php?success=Email verification successful.");
                    exit();
                } else { // If update is unsuccessful
                    header("Location: ../login-v2.php?error=Unknown error occurred.");
                    exit();
                }
            } else { // If the email is already verified
                header("Location: ../login-v2.php?error=Email address is already registered");
                exit();
            }
        } else { // If data was not found in the database
            header("Location: ../login-v2.php?error=Unknown error occurred.");
            exit();
        }
    } else { // If query execution fails
        header("Location: ../login-v2.php?error=Unknown error occurred.");
        exit();
    }
}
?>