<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_GET['delete_id'];
    $comment_id = intval($_POST['comment_id']);
    $post_id = intval($_POST['post_id']);
    $user_id = $_SESSION['user_id'];

    // Only delete if the logged in user OWNS this comment
    $sql = "DELETE FROM comments WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $comment_id, $user_id);
    mysqli_stmt_execute($stmt);

    // Go back to the blog post
    header("Location: each_blog.php?id=<?= $id ?>" . $post_id);
    exit;
}
?>