    <?php
    session_start();
    include('config/db.php');

    // Optional: Enable error reporting (development only)
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    // Handle login form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        // Prepare and execute SQL query to fetch user
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Verify password and log in
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true); // Prevent session fixation
            $_SESSION['user'] = $user;

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin/profile.php");
            } else {
                header("Location: jobseeker/profile.php");
            }
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    }
    ?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>JobBoard Pro Login</title>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
        <div class="center-container">
            <h1>JobBoard Pro</h1>
            <h2>Login</h2>

            <form method="post" action="">
                <label>Username:</label><br>
                <input type="text" name="username" required><br><br>

                <label>Password:</label><br>
                <input type="password" name="password" required><br><br>

                <button type="submit">Login</button>
            </form>

            <?php if (!empty($error)): ?>
                <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <p>Don't have an account? <a href="register.php">Register</a></p>
            <p><a href="forgot_password.php">Forgot your password?</a></p>
        </div>
    </body>
    </html>

