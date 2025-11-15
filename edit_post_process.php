<?php
session_start();
include 'connect.php';

if (isset($_POST['update'])) {
    // Get post ID and escape safely
    $post_id = intval($_POST['post_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $image = '';

    // Check if a new image was uploaded
    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target = 'uploads/' . $image_name;

        $allowedTypes = ['jpeg', 'jpg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowedTypes)) {
            $_SESSION['upload_error'] = 'File format not supported';
            header('location: index.php');
            exit;
        }

        // Upload new image
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $image = $image_name;

            // Optional: get old image to delete
            $oldImageQuery = $conn->prepare("SELECT image FROM posts WHERE id = ?");
            $oldImageQuery->bind_param("i", $post_id);
            $oldImageQuery->execute();
            $oldResult = $oldImageQuery->get_result();
            if ($oldRow = $oldResult->fetch_assoc()) {
                $oldImagePath = 'uploads/' . $oldRow['image'];
                if (file_exists($oldImagePath) && !empty($oldRow['image'])) {
                    unlink($oldImagePath); // remove old image file
                }
            }
            $oldImageQuery->close();

            // Update title, content and new image
            $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ?, image = ? WHERE id = ?");
            $stmt->bind_param("sssi", $title, $content, $image, $post_id);
        } else {
            echo "Error uploading new image.";
            exit;
        }

    } else {
        // No new image — update only title and content
        $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
        $stmt->bind_param("ssi", $title, $content, $post_id);
    }

    // Execute update
    if ($stmt->execute()) {
        header("Location: index.php?edit=success");
        exit();
    } else {
        echo "Error updating post: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>