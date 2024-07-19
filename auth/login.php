<?php
// Include the database connection file
require '../includes/db.php';
// Start the session
session_start();

// Initialize the message variable to store any error messages
$message = '';

// Handle the form submission for user login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute the SQL statement to fetch the user by email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Verify the password and log in the user if the credentials are correct
    if ($user && password_verify($password, $user['password'])) {
        // Set session variables for the logged-in user
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        // Redirect to the profile page after successful login
        header('Location: ../profile.php');
        exit();
    } else {
        // Set an error message if the login credentials are invalid
        $message = "Invalid email or password.";
    }
}
?>

<?php include '../includes/header.php'; ?>

<!-- Display the login form -->
<h2>Login</h2>
<!-- Display an error message if there is one -->
<?php if ($message): ?>
    <div class="alert alert-danger"><?php echo $message; ?></div>
<?php endif; ?>
<form action="login.php" method="post">
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Login</button>
</form>

<?php include '../includes/footer.php'; ?>
