<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userName = $conn -> real_escape_string ($_POST['username']);
    $password = $conn -> real_escape_string ($_POST['password']);

    $sql = "SELECT * FROM users WHERE username = '$userName'";
    $result = $conn -> query($sql);
    $user = $result -> fetch_assoc();

    if($user) {
        if($password == $user['password']) {
            session_start();

            $_SESSION['user_id'] = $user['id'];
            header('location: index.php');
            exit;
        } else {
            echo 'Invalid login';
        }
    }

}
?>