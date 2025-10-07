<?php
include('../components/navbar.php');
include('../config/db.php');

require 'C:\xampp\htdocs\JobBoardPro\PHPMailer-master\PHPMailer-master\src\PHPMailer.php';
require 'C:\xampp\htdocs\JobBoardPro\PHPMailer-master\PHPMailer-master\src\SMTP.php';
require 'C:\xampp\htdocs\JobBoardPro\PHPMailer-master\PHPMailer-master\src\Exception.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Handle updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $id = $_POST['application_id'];
    $interview_schedule = isset($_POST['interview_schedule']) ? $_POST['interview_schedule'] : null;

    // Update application status
    $stmt = $conn->prepare("UPDATE applications SET status = ?, interview_schedule = ? WHERE id = ?");
    $stmt->bind_param("ssi", $status, $interview_schedule, $id);
    $stmt->execute();

    // Fetch applicant email and name
    $stmt = $conn->prepare("
        SELECT users.name, users.email
        FROM applications
        JOIN users ON applications.user_id = users.id
        WHERE applications.id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $applicant = $result->fetch_assoc();

    if ($status === 'interview' && $applicant) {
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';  // your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'christianbacay143@gmail.com';  // your email
            $mail->Password = 'dlya odro bwlr zoxw';   // your email password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('admin@jobboardpro.local', 'JobBoard Pro');
            $mail->addAddress($applicant['email'], $applicant['name']);

            // Format interview schedule if available
            if ($interview_schedule) {
                $formattedDate = date("F j, Y g:i A", strtotime($interview_schedule));
            } else {
                $formattedDate = 'TBD'; // If no interview schedule is provided
            }

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Interview Schedule';
            $mail->Body = "
                <p>Dear {$applicant['name']},</p>
                <p>Congratulations! You are shortlisted for an interview.</p>
                <p><strong>Interview Date and Time:</strong> {$formattedDate}</p>
                <p>Please be available on time. We look forward to speaking with you.</p>
                <p>Best regards,<br>JobBoard Pro Team</p>
            ";

            $mail->send();
        } catch (Exception $e) {
            error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }
}

// Fetch jobseekers with profile picture
$jobseekers = $conn->query("SELECT id, name, email, username, profile_picture FROM users WHERE role = 'jobseeker' ORDER BY name ASC");

// Fetch applications
$applications = $conn->query("
    SELECT applications.id, applications.status, applications.resume, applications.cover_letter,
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
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="layout">
    <div class="main-content">
        <h2>Welcome, Admin <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</h2>

        <!-- Jobseeker Accounts Section -->
        <div class="card">
            <h3>List of Jobseekers</h3>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Profile Picture</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($jobseekers->num_rows > 0): ?>
                        <?php $count = 1; ?>
                        <?php while ($seeker = $jobseekers->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $count++; ?></td>
                                <td>
                                    <?php if ($seeker['profile_picture']): ?>
                                    <center> <img src="../uploads/<?php echo $seeker['profile_picture']; ?>" alt="Profile Picture" style="width: 60px; height: 60px;">
                                    <?php else: ?>
                                        <img src="../uploads/default-profile.png" alt="Profile Picture" style="width: 60px; height: 60px;"> </center>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($seeker['name']); ?></td>
                                <td><?php echo htmlspecialchars($seeker['email']); ?></td>
                                <td><?php echo htmlspecialchars($seeker['username']); ?></td>
                                <td>
                                    <a href="send_email.php?email=<?php echo urlencode($seeker['email']); ?>&name=<?php echo urlencode($seeker['name']); ?>" class="button">Send Email</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No jobseekers found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Applications Section -->
        <div class="card">
            <h3>Manage Applications</h3>
            <table>
                <thead>
                    <tr>
                        <th>Applicant</th>
                        <th>Email</th>
                        <th>Company</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th>Resume</th>
                        <th>Cover Letter</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($app = $applications->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($app['name']); ?></td>
                            <td><?php echo htmlspecialchars($app['email']); ?></td>
                            <td><?php echo htmlspecialchars($app['company_name']); ?></td>
                            <td><?php echo htmlspecialchars($app['position_title']); ?></td>
                            <td><span class="badge badge-<?php echo htmlspecialchars($app['status']); ?>"><?php echo ucfirst($app['status']); ?></span></td>
                            <td>
                                <?php if ($app['resume']): ?>
                                    <a href="../uploads/<?php echo $app['resume']; ?>" target="_blank">View</a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($app['cover_letter']): ?>
                                    <a href="../uploads/<?php echo $app['cover_letter']; ?>" target="_blank">View</a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td id="row-<?php echo $app['id']; ?>">
                                <form method="post" style="display:inline-block; text-align:center;">
                                    <input type="hidden" name="application_id" value="<?php echo $app['id']; ?>">
                                    <select name="status" onchange="toggleSchedule(this, <?php echo $app['id']; ?>)" required>
                                        <option value="">Select</option>
                                        <option value="pending">Pending</option>
                                        <option value="interview">Interview</option>
                                        <option value="hired">Hired</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                    <div id="schedule-<?php echo $app['id']; ?>" style="display:none; margin-top:5px;">
                                        <label style="font-size: 12px;">Date & Time:</label>
                                        <input type="datetime-local" name="interview_schedule" style="font-size: 12px;">
                                    </div>
                                    <button type="submit" style="padding: 4px 8px; font-size: 12px; margin-top: 5px;">Update</button>
                                    <?php if ($app['status'] == 'hired' || $app['status'] == 'rejected'): ?>
                                        <button type="button" onclick="hideRow(<?php echo $app['id']; ?>)" style="padding: 4px 8px; font-size: 12px; background-color: red; color: white; border: none; margin-top: 5px;">Delete</button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
<script>
function toggleSchedule(select, id) {
    const scheduleBox = document.getElementById('schedule-' + id);
    if (select.value === 'interview') {
        scheduleBox.style.display = 'block';
    } else {
        scheduleBox.style.display = 'none';
    }
}

function hideRow(appId) {
    const row = document.getElementById('row-' + appId).parentNode;
    row.style.display = 'none';
}
</script>
</body>
</html>