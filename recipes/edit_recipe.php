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

// Handle the form submission for updating the recipe
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $instructions = $_POST['instructions'];
    $image = $_FILES['image']['name'];
    $target = "../img/" . basename($image);

    // Check if a new image is uploaded
    if ($image) {
        // Move the uploaded file to the target directory
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        // Prepare and execute the SQL statement to update the recipe with the new image
        $stmt = $pdo->prepare("UPDATE recipes SET title = ?, description = ?, instructions = ?, image = ? WHERE id = ? AND created_by = ?");
        $stmt->execute([$title, $description, $instructions, $image, $id, $_SESSION['user_id']]);
    } else {
        // Prepare and execute the SQL statement to update the recipe without changing the image
        $stmt = $pdo->prepare("UPDATE recipes SET title = ?, description = ?, instructions = ? WHERE id = ? AND created_by = ?");
        $stmt->execute([$title, $description, $instructions, $id, $_SESSION['user_id']]);
    }

    // Redirect to the profile page after updating the recipe
    header('Location: ../profile.php');
    exit();
}

// Get the recipe ID from the query string
$id = $_GET['id'];
// Fetch the recipe details from the database
$stmt = $pdo->prepare("SELECT * FROM recipes WHERE id = ? AND created_by = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$recipe = $stmt->fetch();

// If the recipe doesn't exist or doesn't belong to the user, redirect to the profile page
if (!$recipe) {
    header('Location: ../profile.php');
    exit();
}
?>

<?php include '../includes/header.php'; ?>

<!-- Display the edit recipe form -->
<h2>Edit Recipe</h2>
<form action="edit_recipe.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $recipe['id']; ?>">
    <div class="form-group">
        <label for="title">Recipe Title</label>
        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($recipe['title']); ?>" required>
    </div>
    <div class="form-group">
        <label for="description">Description</label>
        <textarea name="description" class="form-control" required><?php echo htmlspecialchars($recipe['description']); ?></textarea>
    </div>
    <div class="form-group">
        <label for="instructions">Instructions</label>
        <textarea name="instructions" class="form-control" required><?php echo htmlspecialchars($recipe['instructions']); ?></textarea>
    </div>
    <div class="form-group">
        <label for="image">Image</label>
        <input type="file" name="image" class="form-control">
    </div>
    <button type="submit" class="btn btn-warning">Update Recipe</button>
</form>

<?php include '../includes/footer.php'; ?>
