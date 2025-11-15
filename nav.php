<?php
include 'connect.php';
session_start();

if(isset($_SESSION['user_id'])) {
  $id = $_SESSION['user_id'];
  $sql = "SELECT * FROM users WHERE id = $id";
  $result = $conn -> query($sql);
  $row = $result -> fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>YZYblog</title>
  <link href="./style.css" rel="stylesheet">
  <link rel="stylesheet" href="./bootstrap-5.3.7/dist/css/bootstrap.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light text-dark border-bottom sticky-top" id="mainNavbar">
      <div class="container">
        <!-- Brand -->
        <a class="navbar-brand fw-bold text-primary fs-3" href="#">
          <i class="bi bi-chat-dots-fill me-1"></i> YZYblog
        </a>

        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
          <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar links -->
        <div class="collapse navbar-collapse p-3" id="navbarMenu">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link active fw-semibold" aria-current="page" href="index.php">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-semibold" href="contact.php">Contact</a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-semibold" href="about.php">About</a>
            </li>
          </ul>

          <!-- Search bar -->
          <button id="themeToggle" class="btn btn-outline-primary btn-sm mb-2 me-3"><i id="themeIcon" class="bi bi-toggle-off"></i></button>
          <form class="d-flex me-3" role="search">
            <input class="form-control form-control-sm me-2" type="search" placeholder="Search posts..." aria-label="Search">
            <button class="btn btn-outline-primary btn-sm" type="submit"><i class="bi bi-search"></i></button>
          </form>

          <!-- Right icons -->
          <div class="d-flex align-items-center">
            <?php if (isset($_SESSION['user_id'])): ?>
            <button class="btn text-dark p-0 me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarNotif">
              <i class="bi bi-bell-fill fs-5"></i>
            </button>
            <button class="btn text-dark p-0 me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMsg">
              <i class="bi bi-envelope-fill fs-5"></i>
            </button>
            <button class="btn text-dark p-0 me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarProfile">
              <?php if (empty($row['profile_pic'])): ?>
              <i class="bi bi-person-circle fs-5"></i>
              <?php else: ?>
              <img src="pfp/<?= $row['profile_pic'] ?>" class="rounded-circle me-2" width="30" height="30" alt="Profile">
              <?php endif ?>
            </button>

            <?php else: ?>
            <a href="signup.html" class="btn btn-primary me-2">Sign Up</a>
            <a href="login.html" class="btn btn-success">Login</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </nav>


  <script src="./bootstrap-5.3.7/dist/js/bootstrap.bundle.js"></script>
    <script src="script.js"></script>
</body>
</html>