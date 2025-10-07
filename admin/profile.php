<?php
include('../components/navbar.php');
include('../config/db.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    $user_id = $_SESSION['user']['id'];

    // Delete user from database
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Destroy session
    session_destroy();

    // Redirect to home
    header("Location: ../index.php");
    exit;
}

$user = $_SESSION['user'];
$user_id = $user['id'];

// Check if the admin already has a profile picture
$profilePicture = $user['profile_picture'] ?? 'default_profile.png'; // fallback to default
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Profile</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .profile-container {
            display: flex;
            align-items: flex-start;
            gap: 20px;
        }
        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid #ccc;
        }
        .profile-picture img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profile-info {
            flex: 1;
        }
        #uploadForm {
            display: none;
        }
    </style>
</head>
<body>

<div class="layout">
    <div class="main-content">
        <h2>Welcome, Admin <?php echo htmlspecialchars($user['name']); ?>!</h2>

        <div class="profile-container">
            <!-- Profile Picture -->
            <div class="profile-picture" onclick="document.getElementById('fileInput').click();">
                <img src="../uploads/<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile Picture">
            </div>

            <!-- Profile Information -->
            <div class="profile-info">
                <h2>Admin Profile</h2>
                <div class="card">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                </div>
            </div>
        </div>

        <!-- Hidden Upload Form -->
        <form id="uploadForm" action="upload_profile_picture.php" method="POST" enctype="multipart/form-data">
            <input type="file" id="fileInput" name="profile_picture" accept="image/*" onchange="document.getElementById('uploadForm').submit();">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        </form>

    </div>
</div>

<script>
    function toggleProfileMenu() {
        const menu = document.getElementById('profileMenu');
        menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
    }
</script>

</body>
</html>
