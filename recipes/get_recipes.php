<?php
require '../includes/db.php';

$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = 6; // Number of recipes per page
$offset = ($page - 1) * $limit;

// Prepare the base query
$query = "SELECT recipes.*, users.username FROM recipes JOIN users ON recipes.created_by = users.id WHERE 1=1";

// Add category filter if provided
if ($category) {
    $query .= " AND category = :category";
}

// Add search filter if provided
if ($search) {
    $query .= " AND (title LIKE :search OR description LIKE :search)";
}

// Add pagination
$query .= " LIMIT :limit OFFSET :offset";

// Prepare the statement
$stmt = $pdo->prepare($query);

// Bind parameters
if ($category) {
    $stmt->bindParam(':category', $category);
}
if ($search) {
    $search = "%$search%";
    $stmt->bindParam(':search', $search);
}
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

// Execute the statement
$stmt->execute();
$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch total number of recipes for pagination
$countQuery = "SELECT COUNT(*) FROM recipes WHERE 1=1";
if ($category) {
    $countQuery .= " AND category = :category";
}
if ($search) {
    $countQuery .= " AND (title LIKE :search OR description LIKE :search)";
}
$countStmt = $pdo->prepare($countQuery);
if ($category) {
    $countStmt->bindParam(':category', $category);
}
if ($search) {
    $countStmt->bindParam(':search', $search);
}
$countStmt->execute();
$totalRecipes = $countStmt->fetchColumn();
$totalPages = ceil($totalRecipes / $limit);

foreach ($recipes as $recipe) {
    echo "
    <div class='col-md-4'>
        <div class='card mb-4'>
            <img src='/RecipeIngredient/img/{$recipe['image']}' class='card-img-top' alt='{$recipe['title']}'>
            <div class='card-body'>
                <h5 class='card-title'>{$recipe['title']}</h5>
                <p class='card-text'>{$recipe['username']}</p>
                <p class='card-text'>".substr($recipe['description'], 0, 100)."...</p>
                <a href='/RecipeIngredient/recipes/recipe_details.php?id={$recipe['id']}' class='btn btn-primary'>View Recipe</a>
            </div>
        </div>
    </div>";
}

// Output pagination controls
if ($totalPages > 1) {
    echo '<nav style="width: 100%;"><ul class="pagination">';
    for ($i = 1; $i <= $totalPages; $i++) {
        echo '<li class="page-item'.($i == $page ? ' active' : '').'"><a class="page-link" href="#" data-page="'.$i.'">'.$i.'</a></li>';
    }
    echo '</ul></nav>';
}
?>
