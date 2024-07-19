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

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $instructions = $_POST['instructions'];
    $image = $_FILES['image']['name'];
    $target = "../img/" . basename($image);

    // Create the img directory if it doesn't exist
    if (!is_dir('../img')) {
        mkdir('../img', 0775, true);
    }

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        // Insert the recipe into the database
        $stmt = $pdo->prepare("INSERT INTO recipes (title, description, instructions, image, created_by) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$title, $description, $instructions, $image, $_SESSION['user_id']])) {
            // Redirect to the profile page if the recipe is successfully inserted
            header('Location: ../profile.php');
            exit();
        } else {
            // Display an error message if the recipe insertion fails
            echo "Failed to insert recipe into database.";
        }
    } else {
        // Display an error message if the file upload fails
        echo "Failed to upload image. Error code: " . $_FILES['image']['error'];
        var_dump($_FILES);
    }
}
?>
