<?php
include 'connect.php';
session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_pic'])) {
    $targetDir = "pfp/"; // directory where photos will be stored
    $fileName = time() . '_' . basename($_FILES['profile_pic']['name']);
    $targetFile = $targetDir . $fileName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // validate the type
    $allowedTypes = ['jpeg', 'jpg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowedTypes)) {
        $_SESSION['upload_error'] = 'File format not supported';
        header('location: index.php');
        exit;
    }

    // check if file already exists
    if (file_exists($targetFile)) {
        $_SESSION['upload_error'] = 'Sorry the file already exists';
        header('location: index.php');
        exit;
    }

    // upload the file
    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFile)) {
        // update the database with the file name only
        $id = $_SESSION['user_id'];;
        $sql = "UPDATE users SET profile_pic = '$fileName' WHERE id = $id";
        $result = $conn -> query($sql);

        if($result) {
            $_SESSION['upload_success'] = ['Photo uploaded successfully'];
            header("location: index.php");
        } else {
            $_SESSION['upload_error'] = 'Sorry there was an error uploading your photo' . $conn -> error;
        }
    } else {
        $_SESSION['upload_error'] = 'Error uploading your photo';
    }
}
?>