<?php
include 'connect.php';

$id = $_GET['delete_id'];
$sql = "DELETE FROM posts WHERE id = $id";
$result = $conn -> query($sql);

if ($result){
    header('location: index.php');
}
else {
    die(mysqli_error($conn));
}

?>