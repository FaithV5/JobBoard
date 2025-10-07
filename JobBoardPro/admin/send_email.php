<?php
include('../components/navbar.php');
include('../config/db.php');

require 'C:\xampp\htdocs\JobBoardPro\PHPMailer-master\PHPMailer-master\src\PHPMailer.php';
require 'C:\xampp\htdocs\JobBoardPro\PHPMailer-master\PHPMailer-master\src\SMTP.php';
require 'C:\xampp\htdocs\JobBoardPro\PHPMailer-master\PHPMailer-master\src\Exception.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Initialize variables to avoid undefined variable warnings
$to_email = $_GET['email'] ?? '';  // Default to an empty string if not set
$name = $_GET['name'] ?? '';        // Default to an empty string if not set
$subject = '';
$message = '';
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $to = $_POST['to'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';

    // Check if fields are not empty
    if (!empty($to) && !empty($subject) && !empty($message)) {
        $mail = new PHPMailer\PHPMailer\PHPMailer();

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'christianbacay143@gmail.com'; // Your Gmail address
            $mail->Password = 'dlya odro bwlr zoxw'; // Your Gmail app password
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('admin@jobboardpro.local', 'JobBoard Admin');
            $mail->addAddress($to, $name);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = nl2br($message);

            // Send the email
            $mail->send();
            $success = "Email sent successfully!";
        } catch (Exception $e) {
            $error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $error = "All fields are required.";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Send Email</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="layout">
    <div class="main-content">
        <h2>Send Email to <?php echo htmlspecialchars($name); ?></h2>

        <form method="post" class="card">
            <label>Email Address:</label>
            <input type="email" name="to" value="<?php echo htmlspecialchars($to_email); ?>" readonly required><br><br>

            <label>Subject:</label>
            <input type="text" name="subject" value="<?php echo htmlspecialchars($subject); ?>" required><br><br>

            <label>Message:</label>
            <textarea name="message" rows="8" required><?php echo htmlspecialchars($message); ?></textarea><br><br>

            <button type="submit">Send Email</button>
        </form>

        <?php if (isset($success)): ?>
            <p style="color: green;"><?php echo $success; ?></p>
        <?php elseif (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <p><a href="dashboard.php">← Back to Dashboard</a></p>
    </div>
</div>
</body>
</html>
