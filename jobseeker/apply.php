<?php
include('../components/navbar.php');
include('../config/db.php');

// Fetch companies
$companies = $conn->query("SELECT * FROM companies");

// Fetch all positions with full info (for JS filtering)
$positionsData = $conn->query("
    SELECT positions.*, companies.id AS company_id, companies.name AS company_name
    FROM positions
    JOIN companies ON positions.company_id = companies.id
");

$positions = [];
while ($row = $positionsData->fetch_assoc()) {
    $positions[] = $row;
}

// Handle application form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user']['id'];
    $position_id = $_POST['position'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $birthday = $_POST['birthday'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];

    // Handle cover letter file (PDF only)
    $cover_letter = $_FILES['cover_letter']['name'];
    $cover_letter_tmp = $_FILES['cover_letter']['tmp_name'];
    $cover_letter_ext = strtolower(pathinfo($cover_letter, PATHINFO_EXTENSION));

    // Handle resume file (PDF only)
    $resume = $_FILES['resume']['name'];
    $resume_tmp = $_FILES['resume']['tmp_name'];
    $resume_ext = strtolower(pathinfo($resume, PATHINFO_EXTENSION));

    if ($cover_letter_ext !== 'pdf' || $resume_ext !== 'pdf') {
        $error = "Both Cover Letter and Resume must be PDF files.";
    } else {
        move_uploaded_file($cover_letter_tmp, "../uploads/" . $cover_letter);
        move_uploaded_file($resume_tmp, "../uploads/" . $resume);

        $docs = $_FILES['docs']['name'];
        $docs_tmp = $_FILES['docs']['tmp_name'];
        move_uploaded_file($docs_tmp, "../uploads/" . $docs);

        $stmt = $conn->prepare("INSERT INTO applications 
            (user_id, position_id, phone, address, birthday, age, gender, resume, other_docs, cover_letter) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("iisssissss", $user_id, $position_id, $phone, $address, $birthday, $age, $gender, $resume, $docs, $cover_letter);



        if ($stmt->execute()) {
            header("Location: dashboard.php?success=1");
            exit;
        } else {
            $error = "Application submission failed.";
        }
    }
}
?>

<?php
$selectedCompany = $_GET['company'] ?? '';
$selectedPosition = $_GET['position'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Apply for a Job</title>
    <link rel="stylesheet" href="../style.css">
    <script>
        const positions = <?php echo json_encode($positions); ?>;

        function updatePositions() {
            const companyId = document.getElementById('company').value;
            const positionSelect = document.getElementById('position');
            const infoBox = document.getElementById('positionInfo');

            positionSelect.innerHTML = '<option value="">Select Position</option>';
            infoBox.innerHTML = '';

            if (companyId) {
                positions.forEach(pos => {
                    if (pos.company_id === companyId) return;
                });

                positions
                    .filter(pos => pos.company_id == companyId)
                    .forEach(pos => {
                        const option = document.createElement('option');
                        option.value = pos.id;
                        option.textContent = pos.title;
                        positionSelect.appendChild(option);
                    });
            }
        }

        function showPositionDetails() {
            const positionId = document.getElementById('position').value;
            const infoBox = document.getElementById('positionInfo');
            infoBox.innerHTML = '';

            const position = positions.find(p => p.id == positionId);
            if (position) {
                infoBox.innerHTML = `
                    <div class="card">
                        <strong>Required No. of Employees:</strong> ${position.required_employees}<br>
                        <strong>Salary:</strong> ₱${Number(position.salary).toLocaleString()}<br>
                        <strong>Duration of Employment:</strong> ${position.employment_duration}<br>
                        <strong>Preferred Sex:</strong> ${position.preferred_sex}<br>
                        <strong>Sector of Vacancy:</strong> ${position.sector_of_vacancy}<br><br>

                        <strong>Qualification / Work Experience:</strong><br>
                        ${position.qualification}<br><br>

                        <strong>Job Description:</strong><br>
                        ${position.job_description}<br><br>

                        <strong>Employer:</strong> ${position.employer}<br>
                        <strong>Location:</strong> ${position.location}
                    </div>
                `;
            }
        }

        window.onload = function () {
    updatePositions();
    setTimeout(() => {
        const posSelect = document.getElementById('position');
        posSelect.value = "<?php echo $selectedPosition; ?>";
        showPositionDetails();
    }, 100); // Small delay to wait for options to populate
};

    </script>
</head>
<body>
<h2>Job Application Form</h2>
<form method="post" enctype="multipart/form-data">
    <label for="company">Select Company:</label>
    <select name="company" id="company" onchange="updatePositions()" required>
        <option value="">Select Company</option>
        <?php while ($row = $companies->fetch_assoc()): ?>
            <option value="<?php echo $row['id']; ?>" <?php if ($selectedCompany == $row['id']) echo 'selected'; ?>>
                <?php echo $row['name']; ?>
            </option>
        <?php endwhile; ?>
    </select><br><br>

    <label for="position">Select Position:</label>
    <select name="position" id="position" onchange="showPositionDetails()" required>
        <option value="">Select Position</option>
        <!-- Will be populated dynamically -->
    </select><br><br>

    <div id="positionInfo" style="margin-bottom: 20px;"></div>

    Phone: <input type="text" name="phone" required><br><br>
    Address: <textarea name="address" required></textarea><br><br>
    Birthday: <input type="date" name="birthday" required><br><br>
    Age: <input type="number" name="age" required><br><br>
    Gender: 
    <select name="gender" required>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
    </select><br><br>

    Upload Cover Letter (PDF only): <input type="file" name="cover_letter" accept="application/pdf" required><br><br>
    Upload Resume (PDF only): <input type="file" name="resume" accept="application/pdf" required><br><br>
    Upload Other Documents: <input type="file" name="docs"><br><br>

    <button type="submit">Apply</button>
</form>
<p style="color:red;"><?php echo $error ?? ''; ?></p>
<p><a href="dashboard.php">Back to Dashboard</a></p>

<script>
    // Function to calculate age from birthdate
    function calculateAge() {
        const birthDate = document.querySelector('input[name="birthday"]').value;
        const ageInput = document.querySelector('input[name="age"]');

        if (birthDate) {
            const birthDateObj = new Date(birthDate);
            const currentDate = new Date();

            let age = currentDate.getFullYear() - birthDateObj.getFullYear();
            const month = currentDate.getMonth() - birthDateObj.getMonth();

            // Adjust age if birthday hasn't occurred yet this year
            if (month < 0 || (month === 0 && currentDate.getDate() < birthDateObj.getDate())) {
                age--;
            }

            ageInput.value = age;
        } else {
            ageInput.value = '';
        }
    }

    // Attach event listener to birthday input field
    document.querySelector('input[name="birthday"]').addEventListener('change', calculateAge);
</script>

</body>
</html>
