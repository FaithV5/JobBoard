<?php
session_start();
$user = $_SESSION['user'] ?? null;
?>

<?php if ($user): ?>
    <div class="navbar">
        <span class="logo">JobBoard Pro</span>
        <div class="nav-links">
            <?php if ($user['role'] === 'admin'): ?>
                <a href="/JobBoardPro/admin/dashboard.php">Dashboard</a>
                <a href="/JobBoardPro/admin/manage_companies.php">Companies</a>
                <a href="/JobBoardPro/admin/manage_positions.php">Positions</a>
                <a href="/JobBoardPro/admin/update_applications.php">Update Applications</a>
            <?php else: ?>
                <a href="/JobBoardPro/employee/profile.php">My Profile</a>
                <a href="/JobBoardPro/employee/dashboard.php">Dashboard</a>
                <a href="/JobBoardPro/employee/apply.php">Apply</a>
            <?php endif; ?>
            <a href="/JobBoardPro/logout.php">Logout</a>
        </div>
    </div>
<?php endif; ?>
