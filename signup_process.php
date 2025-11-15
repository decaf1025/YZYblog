<?php
include 'connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $userName = $_POST['username'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $confirmPassword = $_POST['confirm_password'];
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  $stmt = $conn -> prepare("INSERT INTO users (username, email, password) values (?, ?, ?)");
  $stmt -> bind_param('sss', $userName, $email, $hashedPassword);

  if ($stmt -> execute()) {
    $_SESSION['user_id'] = $conn -> insert_id;
    $_SESSION['username'] = $userName;

    header("Location: index.php");
    exit;
  } else {
    echo "Failed to add user: $stmt -> error";
  }

  $stmt -> close();

}
?>