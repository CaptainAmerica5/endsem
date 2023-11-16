<?php

$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "login_register";
$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);
if (!$conn) {
    die("Something went wrong;");
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    
</head>
<body>
    <div class="container">
        <?php
        require_once "process.php";

        if (isset($_POST["submit"])) {
           $fullName = $_POST["fullname"];
           $email = $_POST["email"];
           $phone = $_POST["phone"];
           $password = $_POST["password"];
           $passwordRepeat = $_POST["repeat_password"];
           $address = $_POST["address"];
           $reg_no= $_POST["reg_no"];

           $passwordHash = password_hash($password, PASSWORD_DEFAULT);

           $errors = array();

           if (empty($fullName) || empty($email) || empty($password) ||empty($reg_no)|| empty($phone) || empty($passwordRepeat) || empty($address)) {
            array_push($errors, "All fields are required");
           }
           if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($errors, "Email is not valid");
           }
           if (strlen($password) < 8) {
            array_push($errors, "Password must be at least 8 characters long");
           }
           if ($password !== $passwordRepeat) {
            array_push($errors, "Password does not match");
           }
          
           
           


           $sql = "SELECT * FROM user WHERE email = ?";
           $stmt = mysqli_stmt_init($conn);

           if (mysqli_stmt_prepare($stmt, $sql)) {
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $rowCount = mysqli_num_rows($result);

                if ($rowCount > 0) {
                    array_push($errors, "Email already exists!");
                }
           }

           if (count($errors) > 0) {
                foreach ($errors as $error) {
                    echo "<div class='alert alert-danger'>$error</div>";
                }
           } else {

                $sql = "INSERT INTO user (full_name, email, phone, password, address,reg_no) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);

                if (mysqli_stmt_prepare($stmt, $sql)) {
                    mysqli_stmt_bind_param($stmt, "ssssss", $fullName, $email, $phone, $passwordHash, $address,$reg_no);
                    mysqli_stmt_execute($stmt);
                    echo "<div class='alert alert-success'>You are registered successfully.</div>";
                } else {
                    die("Something went wrong");
                }
           }
        }
        ?>

        <form action="" method="post">
            <div class="form-group">
                <input type="text" class="form-control" name="fullname" placeholder="Full Name:">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email:">
            </div>
            <div class="form-group">
                <input type="tel" class="form-control" name="phone" placeholder="Phone:">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password:">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password:">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="address" placeholder="Address:">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="reg_no" placeholder="reg_no">
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Register" name="submit">
            </div>
        </form>
        <div>
            <p>Already Registered <a href="login.php">Login Here</a></p>
        </div>
    </div>
</body>
</html>