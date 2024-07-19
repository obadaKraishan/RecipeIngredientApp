<?php
// Include the database connection file
require '../includes/db.php';
// Start the session
session_start();

// Get the recipe ID from the query string
$id = $_GET['id'];

// Fetch the recipe details along with the username of the creator
$stmt = $pdo->prepare('SELECT recipes.*, users.username FROM recipes JOIN users ON recipes.created_by = users.id WHERE recipes.id = ?');
$stmt->execute([$id]);
$recipe = $stmt->fetch(PDO::FETCH_ASSOC);

// If the recipe doesn't exist, redirect to the home page
if (!$recipe) {
    header('Location: ../home.php');
    exit();
}

// Fetch the ingredients for the recipe
$stmt = $pdo->prepare('SELECT * FROM ingredients WHERE recipe_id = ?');
$stmt->execute([$id]);
$ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if the user is logged in
$user_logged_in = isset($_SESSION['user_id']);
// Check if the user can review this recipe (not the creator)
$can_review = $user_logged_in && $_SESSION['user_id'] != $recipe['created_by'];
?>

<?php include '../includes/header.php'; ?>

<!-- Display the recipe details -->
<h1><?php echo htmlspecialchars($recipe['title']); ?></h1>
<img src="/RecipeIngredient/img/<?php echo htmlspecialchars($recipe['image']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
<p class="mt-4"><?php echo nl2br(htmlspecialchars($recipe['description'])); ?></p>
<h3>Instructions</h3>
<p><?php echo nl2br(htmlspecialchars($recipe['instructions'])); ?></p>
<h3>Ingredients</h3>
<ul>
    <?php foreach ($ingredients as $ingredient): ?>
        <li><?php echo htmlspecialchars($ingredient['quantity']) . ' - ' . htmlspecialchars($ingredient['name']); ?></li>
    <?php endforeach; ?>
</ul>

<!-- Display the reviews section -->
<h3>Reviews</h3>
<div id="reviews">
    <!-- Reviews will be loaded here via AJAX -->
</div>

<!-- Display the review form if the user is logged in and can review -->
<?php if ($user_logged_in): ?>
    <?php if ($can_review): ?>
    <form id="review-form">
        <div class="form-group">
            <label for="rating">Rating</label>
            <select id="rating" class="form-control">
                <option>1</option>
                <option>2</option>
                <option>3</option>
                <option>4</option>
                <option>5</option>
            </select>
        </div>
        <div class="form-group">
            <label for="comment">Comment</label>
            <textarea id="comment" class="form-control" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit Review</button>
    </form>
    <?php else: ?>
    <p>You cannot review your own recipe.</p>
    <?php endif; ?>
<?php else: ?>
    <p>You must be <a href="/RecipeIngredient/auth/login.php">logged in</a> or <a href="/RecipeIngredient/auth/register.php">register</a> to be able to review this recipe.</p>
<?php endif; ?>

<!-- Bootstrap Modal for feedback -->
<div class="modal fade" id="feedbackModal" tabindex="-1" role="dialog" aria-labelledby="feedbackModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="feedbackModalLabel">Review Submitted</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Your review has been submitted successfully.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
$(document).ready(function() {
    // Load the reviews for the recipe
    loadReviews();

    function loadReviews() {
        $.ajax({
            url: '/RecipeIngredient/reviews/get_reviews.php?recipe_id=<?php echo $recipe['id']; ?>',
            method: 'GET',
            success: function(response) {
                $('#reviews').html(response);
            }
        });
    }

    // Handle the review form submission
    $('#review-form').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: '/RecipeIngredient/reviews/add_review.php',
            method: 'POST',
            data: {
                recipe_id: <?php echo $recipe['id']; ?>,
                rating: $('#rating').val(),
                comment: $('#comment').val()
            },
            success: function(response) {
                $('#comment').val('');
                loadReviews();
                $('#feedbackModal').modal('show'); // Show the feedback modal
            }
        });
    });
});
</script>
