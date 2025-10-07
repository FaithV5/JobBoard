<?php
include('../components/navbar.php');
include('../config/db.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'jobseeker') {
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

// Check if the jobseeker has a profile picture
$profilePicture = $user['profile_picture'] ?? 'default_profile.png'; // fallback if none

// Fetch jobseeker applications
$applications = $conn->query("
    SELECT applications.*, positions.title AS position_title, companies.name AS company_name
    FROM applications
    JOIN positions ON applications.position_id = positions.id
    JOIN companies ON positions.company_id = companies.id
    WHERE applications.user_id = $user_id
    ORDER BY applications.submitted_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Job Seeker Profile</title>
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
        <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>

        <div class="profile-container">
            <!-- Profile Picture -->
            <div class="profile-picture" onclick="document.getElementById('fileInput').click();">
                <img src="../uploads/<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile Picture">
            </div>

            <!-- Profile Information -->
            <div class="profile-info">
                <h2>My Profile</h2>
                <div class="card">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                    <form method="post" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                    <input type="hidden" name="delete_account" value="1">
                    <button type="submit" style="background-color: red; cursor: pointer; font-size: 16px;">Delete My Account</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Upload Form (Hidden) -->
        <form id="uploadForm" action="upload_profile_picture.php" method="POST" enctype="multipart/form-data">
            <input type="file" id="fileInput" name="profile_picture" accept="image/*" onchange="document.getElementById('uploadForm').submit();">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        </form>

        <div class="card">
            <h3>My Applications</h3>
            <table>
                <tr>
                    <th>Company</th>
                    <th>Position</th>
                    <th>Status</th>
                    <th>Resume</th>
                    <th>Cover Letter</th>
                    <th>Other Docs</th>
                    <th>Submitted At</th>
                </tr>
                <?php while ($app = $applications->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($app['company_name']); ?></td>
                        <td><?php echo htmlspecialchars($app['position_title']); ?></td>
                        <td><span class="badge badge-<?php echo htmlspecialchars($app['status']); ?>"><?php echo ucfirst(htmlspecialchars($app['status'])); ?></span></td>
                        <td>
                            <?php if ($app['resume']): ?>
                                <a href="../uploads/<?php echo htmlspecialchars($app['resume']); ?>" target="_blank">View</a>
                            <?php else: ?>N/A<?php endif; ?>
                        </td>
                        <td>
                            <?php if ($app['cover_letter']): ?>
                                <a href="../uploads/<?php echo htmlspecialchars($app['cover_letter']); ?>" target="_blank">View</a>
                            <?php else: ?>N/A<?php endif; ?>
                        </td>
                        <td>
                            <?php if ($app['other_docs']): ?>
                                <a href="../uploads/<?php echo htmlspecialchars($app['other_docs']); ?>" target="_blank">View</a>
                            <?php else: ?>N/A<?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($app['submitted_at']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
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
