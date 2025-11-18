<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

if (isset($_POST['submit_comment'])) {
    $user_id = $_SESSION['user_id'];
    $post_id = intval($_POST['post_id']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : NULL;

    $sql = "INSERT INTO comments (post_id, user_id, comment, parent_id) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iisi", $post_id, $user_id, $comment, $parent_id);
    mysqli_stmt_execute($stmt);
    
    header("Location: each_blog.php?id=" . $post_id);
    exit;
}
?>