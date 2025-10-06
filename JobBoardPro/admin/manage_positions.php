<?php
include('../components/navbar.php');
include('../config/db.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Add position
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_position'])) {
    $company_id = $_POST['company_id'];
    $title = $_POST['title'];
    $required_employees = $_POST['required_employees'];
    $salary = $_POST['salary'];
    $employment_duration = $_POST['employment_duration'];
    $preferred_sex = $_POST['preferred_sex'];
    $sector_of_vacancy = $_POST['sector_of_vacancy'];
    $qualification = $_POST['qualification'];
    $job_description = $_POST['job_description'];
    $employer = $_POST['employer'];
    $location = $_POST['location'];

    $stmt = $conn->prepare("INSERT INTO positions 
        (company_id, title, required_employees, salary, employment_duration, preferred_sex, sector_of_vacancy, qualification, job_description, employer, location) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isidsssssss", $company_id, $title, $required_employees, $salary, $employment_duration, $preferred_sex, $sector_of_vacancy, $qualification, $job_description, $employer, $location);
    $stmt->execute();
}

// Delete position
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM positions WHERE id = $id");
}

// Fetch companies and positions
$companies = $conn->query("SELECT * FROM companies");
$positions = $conn->query("
    SELECT positions.id, positions.title, companies.name AS company 
    FROM positions 
    JOIN companies ON positions.company_id = companies.id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Positions</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="layout">

    <div class="main-content">
        <h2>Manage Job Positions</h2>
            <form method="post" class="card">
                <label>Company:</label>
                <select name="company_id" required>
                    <option value="">Select Company</option>
                    <?php while ($c = $companies->fetch_assoc()): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option>
                    <?php endwhile; ?>
                </select><br><br>

                <label>Job Title:</label>
                <input type="text" name="title" placeholder="Position Title" required><br><br>

                <label>Required No. of Employees:</label>
                <input type="number" name="required_employees" required><br><br>

                <label>Salary:</label>
                <input type="text" name="salary" required><br><br>

                <label>Duration of Employment:</label>
                <input type="text" name="employment_duration" required><br><br>

                <label>Preferred Sex:</label>
                <select name="preferred_sex" required>
                    <option value="Any">Any</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select><br><br>

                <label>Sector of Vacancy:</label>
                <input type="text" name="sector_of_vacancy" required><br><br>

                <label>Qualification / Work Experience:</label>
                <textarea name="qualification" required></textarea><br><br>

                <label>Job Description:</label>
                <textarea name="job_description" required></textarea><br><br>

                <label>Employer:</label>
                <input type="text" name="employer" required><br><br>

                <label>Location:</label>
                <input type="text" name="location" required><br><br>

                <button type="submit" name="add_position">Add Position</button>
            </form>


        <div class="card">
            <h3>Position List</h3>
            <table>
                <tr><th>ID</th><th>Company</th><th>Position</th><th>Action</th></tr>
                <?php while ($p = $positions->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $p['id']; ?></td>
                    <td><?php echo $p['company']; ?></td>
                    <td><?php echo $p['title']; ?></td>
                    <td><a href="?delete=<?php echo $p['id']; ?>" onclick="return confirm('Delete this position?')">Delete</a></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</div>
</body>
</html>
