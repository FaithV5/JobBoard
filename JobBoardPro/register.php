<?php
include('config/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, username, password, role) VALUES (?, ?, ?, ?, 'employee')");
    $stmt->bind_param("ssss", $name, $email, $username, $password);
    
    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        $error = "Username or Email already exists!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - JobBoard Pro</title>
    <link rel="stylesheet" type="text/css" href="../style.css"> 
<!-- Use this for files in /admin or /employee -->

<!-- Or use this for files in root folder like index.php -->
<link rel="stylesheet" type="text/css" href="style.css">

</head>
<body>
<h2>Employee Registration</h2>
<form method="post">
    Name: <input type="text" name="name" required><br><br>
    Email: <input type="email" name="email" required><br><br>
    Username: <input type="text" name="username" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <button type="submit">Register</button>
</form>
<p style="color:red;"><?php echo $error ?? ''; ?></p>
<p>Already have an account? <a href="index.php">Login</a></p>
</body>
</html>
