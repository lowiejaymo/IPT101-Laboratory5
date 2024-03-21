<?php
session_start();
include 'indexes/db_conn.php';

if(!isset($_SESSION['access_token'])){
    header("Location: login-v2.php");
    exit();
}

$facebookemail = $_SESSION['userData']['email'];

//checking if email is existing in the database
$sql = "SELECT * FROM user WHERE Email = '$facebookemail'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  $row = mysqli_fetch_assoc($result);
  if ($row['is_verified'] == 1) {
    // Fetch user profile data
    $sql2 = "SELECT * FROM user_profile WHERE user_id='" . $row['user_id'] . "'";
    $result2 = mysqli_query($conn, $sql2);
    
    if ($profile_row = mysqli_fetch_assoc($result2)) {
        // Populate session variables
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['Lastname'] = $row['Lastname'];
        $_SESSION['First_name'] = $row['First_name'];
        $_SESSION['Middle_name'] = $row['Middle_name'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['email'] = $row['Email'];
        $_SESSION['password'] = $row['password'];
        $_SESSION['new_email'] = $row['new_email']; 
        
        $_SESSION['phone_number'] = $profile_row['phone_number'];
        $_SESSION['Birthday'] = $profile_row['Birthday'];
        $_SESSION['gender'] = $profile_row['gender'];
        $_SESSION['street_building_house'] = $profile_row['street_building_house'];
        $_SESSION['Barangay'] = $profile_row['Barangay'];
        $_SESSION['City'] = $profile_row['City'];
        $_SESSION['Province'] = $profile_row['Province'];
        $_SESSION['Region'] = $profile_row['Region'];
        $_SESSION['Postal_Code'] = $profile_row['Postal_Code'];
        $_SESSION['Occupation'] = $profile_row['Occupation'];
        $_SESSION['Education'] = $profile_row['Education'];
        $_SESSION['Skills'] = $profile_row['Skills'];
        $_SESSION['Notes'] = $profile_row['Notes'];
        $_SESSION['profile_picture'] = $profile_row['profile_picture'];
        $_SESSION['new_email'] = $profile_row['new_email'];

        header("Location: dashboard.php");
        exit();
    } else {
        header("Location: login-v2.php?error=Profile not found");
        exit();
    }
} else {
    // Account is not verified
$_SESSION['verify'] = true;
$_SESSION['username'] = $row['username'];
$_SESSION['email'] = $row['Email'];
    header("Location: createdsuccessfully.php?error=Your account is not yet verified, please check your registered email and provide the valid verification code.");
    exit;
}
} else {
  header("Location: login-v2.php?error=Email is not registered.");
  exit();
}

?>