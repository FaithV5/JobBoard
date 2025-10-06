<?php
include('../components/navbar.php');
include('../config/db.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'employee') {
    header("Location: ../index.php");
    exit;
}


$user = $_SESSION['user'];
$user_id = $user['id'];

// Fetch employee applications
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
    <title>Employee Profile</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="layout">

    <div class="main-content">
        <h2>My Profile</h2>
        <div class="card">
            <p><strong>Name:</strong> <?php echo $user['name']; ?></p>
            <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
            <p><strong>Username:</strong> <?php echo $user['username']; ?></p>
        </div>

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
                        <td><?php echo $app['company_name']; ?></td>
                        <td><?php echo $app['position_title']; ?></td>
                        <td><span class="badge badge-<?php echo $app['status']; ?>"><?php echo ucfirst($app['status']); ?></span></td>
                        <td>
                            <?php if ($app['resume']): ?>
                                <a href="../uploads/<?php echo $app['resume']; ?>" target="_blank">View</a>
                            <?php else: ?>N/A<?php endif; ?>
                        </td>
                        <td>
                            <?php if ($app['cover_letter_file']): ?>
                                <a href="../uploads/<?php echo $app['cover_letter_file']; ?>" target="_blank">View</a>
                            <?php else: ?>N/A<?php endif; ?>
                        </td>
                        <td>
                            <?php if ($app['other_docs']): ?>
                                <a href="../uploads/<?php echo $app['other_docs']; ?>" target="_blank">View</a>
                            <?php else: ?>N/A<?php endif; ?>
                        </td>
                        <td><?php echo $app['submitted_at']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</div>
</body>
<script>
    function toggleProfileMenu() {
        const menu = document.getElementById('profileMenu');
        menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
    }
</script>

</html>
