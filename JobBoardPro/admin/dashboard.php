<?php
include('../components/navbar.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="layout">
    <div class="main-content">
        <h2>Welcome, Admin <?php echo $_SESSION['user']['name']; ?>!</h2>
        <div class="card">
            <p>This is your admin dashboard. Use the menu to manage companies and positions.</p>
        </div>
    </div>
</div>
</body>
</html>
