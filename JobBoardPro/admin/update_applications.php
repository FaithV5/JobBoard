<?php
include('../components/navbar.php');
include('../config/db.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Update status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $id = $_POST['application_id'];

    $stmt = $conn->prepare("UPDATE applications SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
}

// Delete application
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    // Optionally delete uploaded files too (resume/docs) — safety check:
    $result = $conn->query("SELECT resume, other_docs FROM applications WHERE id = $delete_id");
    $row = $result->fetch_assoc();
    if ($row) {
        if (!empty($row['resume'])) unlink("../uploads/" . $row['resume']);
        if (!empty($row['other_docs'])) unlink("../uploads/" . $row['other_docs']);
    }

    $conn->query("DELETE FROM applications WHERE id = $delete_id");
    header("Location: update_applications.php");
    exit;
}


// Fetch all applications
$applications = $conn->query("
    SELECT applications.id, applications.status, applications.resume, applications.other_docs,
           users.name, users.email,
           positions.title AS position_title, companies.name AS company_name
    FROM applications
    JOIN users ON applications.user_id = users.id
    JOIN positions ON applications.position_id = positions.id
    JOIN companies ON positions.company_id = companies.id
");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Applications</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="layout">

    <div class="main-content">
        <h2>Update Application Status</h2>
        <table>
            <tr>
                <th>Applicant</th>
                <th>Email</th>
                <th>Company</th>
                <th>Position</th>
                <th>Status</th>
                <th>Resume</th>
                <th>Docs</th>
                <th>Update</th>
                <th>Delete</th>
            </tr>

    <?php while ($app = $applications->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $app['name']; ?></td>
                    <td><?php echo $app['email']; ?></td>
                    <td><?php echo $app['company_name']; ?></td>
                    <td><?php echo $app['position_title']; ?></td>
                    <td><span class="badge badge-<?php echo $app['status']; ?>"><?php echo ucfirst($app['status']); ?></span></td>
                    <td>
                        <?php if ($app['resume']): ?>
                            <a href="../uploads/<?php echo $app['resume']; ?>" target="_blank">View</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($app['other_docs']): ?>
                            <a href="../uploads/<?php echo $app['other_docs']; ?>" target="_blank">View</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="application_id" value="<?php echo $app['id']; ?>">
                            <select name="status" required>
                                <option value="">Select</option>
                                <option value="pending">Pending</option>
                                <option value="interview">Interview</option>
                                <option value="hired">Hired</option>
                                <option value="rejected">Rejected</option>
                            </select>
                            <button type="submit">Update</button>
                        </form>
                    </td>
                    <td>
                        <a href="?delete=<?php echo $app['id']; ?>" onclick="return confirm('Are you sure you want to delete this application?')" style="color:red;">Delete</a>
                    </td>

                </tr>

            <?php endwhile; ?>
        </table>
    </div>
</div>
</body>
</html>
