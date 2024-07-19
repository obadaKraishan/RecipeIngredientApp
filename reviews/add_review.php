<?php
require '../includes/db.php';

$recipe_id = $_POST['recipe_id'];
$user_id = $_SESSION['user_id'];
$rating = $_POST['rating'];
$comment = $_POST['comment'];

$stmt = $pdo->prepare("INSERT INTO reviews (recipe_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
$stmt->execute([$recipe_id, $user_id, $rating, $comment]);
?>
