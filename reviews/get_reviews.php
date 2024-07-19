<?php
require '../includes/db.php';

$recipe_id = $_GET['recipe_id'];

$stmt = $pdo->prepare('SELECT reviews.*, users.username FROM reviews JOIN users ON reviews.user_id = users.id WHERE recipe_id = ? ORDER BY created_at DESC');
$stmt->execute([$recipe_id]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($reviews as $review) {
    echo "
    <div class='review'>
        <h5>{$review['username']} <span class='text-muted'>rated {$review['rating']}/5</span></h5>
        <p>{$review['comment']}</p>
        <small class='text-muted'>{$review['created_at']}</small>
    </div>
    <hr>";
}
?>
