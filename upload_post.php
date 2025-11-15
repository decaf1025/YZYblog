<?php
session_start();
include 'connect.php';

if (isset($_POST['upload'])) {
    $user_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $image = '';

    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target = 'uploads/' . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $image = $image_name;
        }
    }

    $stmt = $conn -> prepare("INSERT INTO posts (user_id, title, content, image) values (?, ?, ?, ?)");
    $stmt -> bind_param('isss', $user_id, $title, $content, $image);

    if ($stmt -> execute()) {
        header('location: index.php');
    } else {
        echo "Failed to upload: $stmt -> error";
    }

    $stmt -> close();

}
?>