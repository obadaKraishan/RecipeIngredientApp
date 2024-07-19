<?php
// Include the database connection file
require '../includes/db.php';
// Start the session
session_start();

// Redirect to the login page if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Get the recipe ID from the query string
$id = $_GET['id'];

// Prepare and execute the SQL statement to delete the recipe
// Only delete the recipe if the current user is the creator
$stmt = $pdo->prepare("DELETE FROM recipes WHERE id = ? AND created_by = ?");
$stmt->execute([$id, $_SESSION['user_id']]);

// Redirect to the profile page after deletion
header('Location: ../profile.php');
exit();
?>
