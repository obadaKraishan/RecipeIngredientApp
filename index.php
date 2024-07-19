<?php
// Include the database connection file
require 'includes/db.php';
// Start the session
session_start();

// Fetch categories for the sidebar
$categories = $pdo->query("SELECT DISTINCT category FROM recipes")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<div class="row">
    <div class="col-md-3">
        <h3>Categories</h3>
        <ul class="list-group" id="category-list">
            <?php foreach ($categories as $category): ?>
                <li class="list-group-item">
                    <a href="#" class="category-filter" data-category="<?php echo htmlspecialchars($category['category']); ?>">
                        <?php echo htmlspecialchars($category['category']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <!-- Clear filters button -->
        <button id="clear-filters" class="btn btn-secondary mt-2">Clear</button>
    </div>
    <div class="col-md-9">
        <h1>All Recipes</h1>
        <!-- Search bar for recipes -->
        <div class="form-group">
            <input type="text" id="search-bar" class="form-control" placeholder="Search for recipes...">
        </div>
        <div class="row" id="recipe-list">
            <!-- Recipes will be loaded here via AJAX -->
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
$(document).ready(function() {
    // Load all recipes initially
    loadRecipes();

    function loadRecipes(category = '', search = '', page = 1) {
        $.ajax({
            url: 'recipes/get_recipes.php',
            method: 'GET',
            data: { category: category, search: search, page: page },
            success: function(response) {
                $('#recipe-list').html(response);
            }
        });
    }

    // Handle search bar input
    $('#search-bar').on('input', function() {
        const search = $(this).val();
        const category = $('.category-filter.active').data('category') || '';
        loadRecipes(category, search);
    });

    // Handle category filter click
    $('.category-filter').on('click', function(e) {
        e.preventDefault();
        $('.category-filter').removeClass('active');
        $(this).addClass('active');
        const category = $(this).data('category');
        const search = $('#search-bar').val();
        loadRecipes(category, search);
    });

    // Handle clear filters button click
    $('#clear-filters').on('click', function() {
        $('.category-filter').removeClass('active');
        $('#search-bar').val('');
        loadRecipes();
    });

    // Handle pagination click
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        const category = $('.category-filter.active').data('category') || '';
        const search = $('#search-bar').val();
        loadRecipes(category, search, page);
    });
});
</script>
