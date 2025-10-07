<?php
include('../components/navbar.php');
include('../config/db.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Add company
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_company'])) {
    $name = $_POST['name'];
    $stmt = $conn->prepare("INSERT INTO companies (name) VALUES (?)");
    $stmt->bind_param("s", $name);
    $stmt->execute();
}

// Delete company
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM companies WHERE id = $id");
}

// Fetch all companies
$companies = $conn->query("SELECT * FROM companies");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Companies</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="layout">

    <div class="main-content">
        <h2>Manage Companies</h2>
        <form method="post" class="card">
            <input type="text" name="name" placeholder="Company Name" required>
            <button type="submit" name="add_company">Add Company</button>
        </form>

        <div class="card">
            <h3>Company List</h3>
            <table>
                <tr><th>ID</th><th>Name</th><th>Action</th></tr>
                <?php while ($row = $companies->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this company?')">Delete</a></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</div>
</body>
</html>
