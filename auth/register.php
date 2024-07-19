<?php
// Include the database connection file
require '../includes/db.php';
// Start the session
session_start();

// Initialize the message variable to store any error messages
$message = '';

// Handle the form submission for user registration
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    // Hash the password using bcrypt
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if username or email already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    $existingUser = $stmt->fetch();

    if ($existingUser) {
        // Set an error message if the username or email already exists
        $message = "Username or email already exists.";
    } else {
        // Insert the new user into the database
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $email, $password])) {
            // Set session variables for the logged-in user
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['username'] = $username;
            // Redirect to the profile page after successful registration
            header('Location: ../profile.php');
            exit();
        } else {
            // Set an error message if there is an issue creating the account
            $message = "Error creating account. Please try again.";
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

<!-- Display the registration form -->
<h2>Register</h2>
<!-- Display an error message if there is one -->
<?php if ($message): ?>
    <div class="alert alert-danger"><?php echo $message; ?></div>
<?php endif; ?>
<form action="register.php" method="post">
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" name="username" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Register</button>
</form>

<?php include '../includes/footer.php'; ?>
