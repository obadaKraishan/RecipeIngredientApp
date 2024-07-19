<?php
require 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<?php include 'includes/header.php'; ?>

<h2>Welcome, <?php echo htmlspecialchars($user['username']); ?></h2>
<p>Email: <?php echo htmlspecialchars($user['email']); ?></p>

<ul class="nav nav-tabs" id="recipeTabs" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" id="view-recipes-tab" data-toggle="tab" href="#view-recipes" role="tab" aria-controls="view-recipes" aria-selected="true">View Recipes</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="add-recipe-tab" data-toggle="tab" href="#add-recipe" role="tab" aria-controls="add-recipe" aria-selected="false">Add Recipe</a>
  </li>
</ul>

<div class="tab-content" id="recipeTabsContent">
  <div class="tab-pane fade show active" id="view-recipes" role="tabpanel" aria-labelledby="view-recipes-tab">
    <h3>Your Recipes</h3>
    <div id="user-recipes" class="mt-3">
      <!-- User recipes will be dynamically loaded here -->
    </div>
  </div>
  <div class="tab-pane fade" id="add-recipe" role="tabpanel" aria-labelledby="add-recipe-tab">
    <h3>Add Recipe</h3>
    <form action="recipes/add_recipe.php" method="post" enctype="multipart/form-data">
      <div class="form-group">
        <label for="title">Recipe Title</label>
        <input type="text" name="title" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="description">Description</label>
        <textarea name="description" class="form-control" required></textarea>
      </div>
      <div class="form-group">
        <label for="instructions">Instructions</label>
        <textarea name="instructions" class="form-control" required></textarea>
      </div>
      <div class="form-group">
        <label for="image">Image</label>
        <input type="file" name="image" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary">Add Recipe</button>
    </form>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
$(document).ready(function() {
    loadUserRecipes();

    function loadUserRecipes() {
        $.ajax({
            url: 'recipes/get_user_recipes.php',
            method: 'GET',
            success: function(response) {
                $('#user-recipes').html(response);
            }
        });
    }
});
</script>
