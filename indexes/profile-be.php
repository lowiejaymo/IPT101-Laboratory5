<?php
session_start();
include "db_conn.php";

if (isset($_POST['profile_update'])) {
    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $lastname = validate($_POST['lastname']);
    $firstname = validate($_POST['firstname']);
    $middlename = validate($_POST['middlename']);
    $uname = validate($_POST['uname']);
    $password = validate($_POST['profile_password']);

    $userid = $_SESSION['user_id']; // Get user ID from session or database
    $stored_password = $_SESSION['password']; // Make sure to replace with actual session variable

    // Verify the entered password
    if (empty($password)) {
        //if the password input was empty
        header("Location: ../profile.php?updateprofileerror=Password is required");
        exit();
    } elseif (empty($lastname)) {
        //if the lastname input was empty
        header("Location: ../profile.php?updateprofileerror=Lastname is required");
        exit();
    } elseif (empty($firstname)) {
        //if the first name input was empty
        header("Location: ../profile.php?updateprofileerror=Firstname is required");
        exit();
    } elseif (empty($uname)) {
        //if the username input was empty
        header("Location: ../profile.php?updateprofileerror=User Name is required");
        exit();
    } elseif (!password_verify($password, $stored_password)) {
        //if the input password and the stored password didn't match
        header("Location: ../profile.php?updateprofileerror=Password is incorrect.");
        exit();
    } else {
       // Check if the new username is already taken (excluding current user's username)
       $current_username = $_SESSION['username'];
       $check_username_sql = "SELECT * FROM user WHERE username=? AND username != ?";
       $stmt_check_username = mysqli_prepare($conn, $check_username_sql);
       mysqli_stmt_bind_param($stmt_check_username, "ss", $uname, $current_username);
       mysqli_stmt_execute($stmt_check_username);
       $check_username_result = mysqli_stmt_get_result($stmt_check_username);

       if (mysqli_num_rows($check_username_result) > 0) {
           header("Location: ../profile.php?updateprofileerror=Username is already taken.");
           exit();
       }

       // Update the user's registration details
       $update_sql = "UPDATE user SET Lastname=?, First_name=?, Middle_name=?, username=? WHERE username=?";
       $stmt_update_user = mysqli_prepare($conn, $update_sql);
       mysqli_stmt_bind_param($stmt_update_user, "sssss", $lastname, $firstname, $middlename, $uname, $current_username);
       $update_result = mysqli_stmt_execute($stmt_update_user);

       // Prepare and execute the update query for other user info using prepared statements
       $update_other_info_sql = "UPDATE user_profile SET phone_number=?, Birthday=?, gender=?, street_building_house=?, 
           Barangay=?, City=?, Province=?, Region=?, Postal_code=?, Occupation=?, Education=?, Skills=?, Notes=? WHERE user_id=?";
       $stmt_update_other_info = mysqli_prepare($conn, $update_other_info_sql);
       mysqli_stmt_bind_param($stmt_update_other_info, "isssssssissssi",
           $_POST['phone_number'],
           $_POST['birthday'],
           $_POST['gender'],
           $_POST['street_building_house'],
           $_POST['barangay'],
           $_POST['city'],
           $_POST['province'],
           $_POST['region'],
           $_POST['postal_code'],
           $_POST['occupation'],
           $_POST['education'],
           $_POST['skills'],
           $_POST['notes'],
           $userid
       );
       $update_result_other_info = mysqli_stmt_execute($stmt_update_other_info);

       if ($update_result && $update_result_other_info) {
           // Update session variables with new information
           $_SESSION['Lastname'] = $lastname;
           $_SESSION['First_name'] = $firstname;
           $_SESSION['Middle_name'] = $middlename;
           $_SESSION['username'] = $uname;
           $_SESSION['phone_number'] = $_POST['phone_number'];
           $_SESSION['Birthday'] = $_POST['birthday'];
           $_SESSION['gender'] = $_POST['gender'];
           $_SESSION['street_building_house'] = $_POST['street_building_house'];
           $_SESSION['Barangay'] = $_POST['barangay'];
           $_SESSION['City'] = $_POST['city'];
           $_SESSION['Province'] = $_POST['province'];
           $_SESSION['Region'] = $_POST['region'];
           $_SESSION['Postal_Code'] = $_POST['postal_code'];
           $_SESSION['Occupation'] = $_POST['occupation'];
           $_SESSION['Education'] = $_POST['education'];
           $_SESSION['Skills'] = $_POST['skills'];
           $_SESSION['Notes'] = $_POST['notes'];

           // Redirect with success message
           header("Location: ../profile.php?updateprofilesuccess=Your Personal Information has been successfully updated.");
           exit();
       } else {
           // Redirect with error message
           header("Location: ../profile.php?updateprofileerror=Failed to update profile.");
           exit();
       }
   }
} else {
   // Redirect if the form is not submitted
   header("Location: ../profile.php?updateprofileerror=Form submission failed.");
   exit();
}
?>