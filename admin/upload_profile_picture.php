<?php
session_start();
include('../config/db.php'); // Adjust the path if needed

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
    $user_id = $_POST['user_id'];
    $file = $_FILES['profile_picture'];

    if ($file['error'] === 0) {
        $filename = uniqid() . '-' . basename($file['name']);
        $targetPath = '../uploads/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Update database
            $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $stmt->bind_param("si", $filename, $user_id);
            $stmt->execute();

            // Update session too
            $_SESSION['user']['profile_picture'] = $filename;
        }
    }
}

// Redirect back to profile page
header('Location: profile.php');
exit();
?>
