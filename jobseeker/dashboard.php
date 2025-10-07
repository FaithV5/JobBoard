<?php
include('../components/navbar.php');
include('../config/db.php');

// Fetch all companies and their positions with details
$positions = $conn->query("
    SELECT positions.*, companies.name AS company_name, companies.id AS company_id
    FROM positions
    JOIN companies ON positions.company_id = companies.id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Job Seeker Dashboard</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="layout">

    <div class="main-content">
        <h2>Available Positions</h2>
        <?php while ($p = $positions->fetch_assoc()): ?>
            <div class="card" style="margin-bottom: 15px;">
                <h3><?php echo $p['title']; ?> <small style="color: #555;">(<?php echo $p['company_name']; ?>)</small></h3>
                <p><strong>Required Employees:</strong> <?php echo $p['required_employees']; ?></p>
                <p><strong>Salary:</strong> ₱<?php echo number_format($p['salary']); ?></p>
                <p><strong>Duration:</strong> <?php echo $p['employment_duration']; ?></p>
                <p><strong>Preferred Sex:</strong> <?php echo $p['preferred_sex']; ?></p>
                <p><strong>Sector:</strong> <?php echo $p['sector_of_vacancy']; ?></p>
                <p><strong>Qualifications:</strong><br><?php echo nl2br($p['qualification']); ?></p>
                <p><strong>Description:</strong><br><?php echo nl2br($p['job_description']); ?></p>
                <p><strong>Employer:</strong> <?php echo $p['employer']; ?></p>
                <p><strong>Location:</strong> <?php echo $p['location']; ?></p>
                <a href="apply.php?company=<?php echo $p['company_id']; ?>&position=<?php echo $p['id']; ?>" class="button">Apply Now</a>
            </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>
