<?php
include 'connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_SESSION['user_id'];
  $bio = $_POST['bio'];
  
  $sql = "UPDATE users SET bio = '$bio' WHERE id = $id";
  $result = $conn -> query($sql);

  if ($result) {
    header('location: index.php');
  } else {
    die(mysqli_error($conn));
  }
}
?>