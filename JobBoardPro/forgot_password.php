<?php
session_start();
include('config/db.php');

require 'C:\xampp\htdocs\JobBoardPro\PHPMailer-master\PHPMailer-master\src\PHPMailer.php';
require 'C:\xampp\htdocs\JobBoardPro\PHPMailer-master\PHPMailer-master\src\SMTP.php';
require 'C:\xampp\htdocs\JobBoardPro\PHPMailer-master\PHPMailer-master\src\Exception.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $step = $_POST['step'];

    // Step 1: Send OTP
    if ($step === 'request_otp') {
        $username = trim($_POST['username']);
        $_SESSION['reset_username'] = $username;

        // Look up user's email
        $stmt = $conn->prepare("SELECT email FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            $email = $user['email'];
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;

            // Send email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                // ✅ SMTP Server settings — update with real credentials
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // or your SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'christianbacay143@gmail.com'; // your email
                $mail->Password = 'dlya odro bwlr zoxw';    // app password (not Gmail password)
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('admin@jobboardpro.local', 'JobBoard Pro');
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Your OTP for Password Reset';
                $mail->Body = "Hello <strong>$username</strong>,<br><br>Your OTP is: <strong>$otp</strong><br><br>Do not share this code with anyone.";

                $mail->send();
                $success = 'OTP sent to your email. Please enter it below along with your new password.';
                $_SESSION['otp_sent'] = true;
            } catch (Exception $e) {
                $error = 'Could not send OTP. Mailer Error: ' . $mail->ErrorInfo;
            }
        } else {
            $error = 'Username not found.';
        }

    // Step 2: Verify OTP and Reset Password
    } elseif ($step === 'reset_password') {
        $otp_input = $_POST['otp'];
        $new_password = $_POST['new_password'];
        $username = $_SESSION['reset_username'] ?? '';

        if ($otp_input == $_SESSION['otp']) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
            $stmt->bind_param("ss", $hashed, $username);

            if ($stmt->execute()) {
                $success = 'Password reset successful. You may now <a href="index.php">login</a>.';
                unset($_SESSION['otp'], $_SESSION['reset_username'], $_SESSION['otp_sent']);
            } else {
                $error = 'Failed to update password.';
            }
        } else {
            $error = 'Invalid OTP.';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password - JobBoard Pro</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Forgot Password</h1>

    <?php if ($error): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php elseif ($success): ?>
        <p style="color:green;"><?php echo $success; ?></p>
    <?php endif; ?>

    <?php if (empty($_SESSION['otp_sent'])): ?>
        <!-- Step 1: Request OTP -->
        <form method="post">
            <input type="hidden" name="step" value="request_otp">
            Username: <input type="text" name="username" required><br><br>
            <button type="submit">Send OTP</button>
        </form>
    <?php else: ?>
        <!-- Step 2: Verify OTP & Reset Password -->
        <form method="post">
            <input type="hidden" name="step" value="reset_password">
            OTP Code: <input type="text" name="otp" required><br><br>
            New Password: <input type="password" name="new_password" required><br><br>
            <button type="submit">Reset Password</button>
        </form>
    <?php endif; ?>
</body>
</html>
