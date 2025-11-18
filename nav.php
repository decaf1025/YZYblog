<?php
include 'connect.php';
session_start();

if(isset($_SESSION['user_id'])) {
  $id = $_SESSION['user_id'];
  $sql = "SELECT * FROM users WHERE id = $id";
  $result = $conn -> query($sql);
  $row = $result -> fetch_assoc();
}

if(isset($_SESSION['user_id'])) {
  $id = $_SESSION['user_id'];
  $sql = "SELECT * FROM users WHERE id = $id";
  $user_result = $conn -> query($sql);
  $user = $user_result -> fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>YZYblog</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" />
  <link href="./style.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
</head>
<body class="lightMode">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-light sticky-top" id="mainNavbar">
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
        <div class="d-flex align-items-center">
          <button id="themeToggle" class="btn btn-outline-primary btn-sm me-3"><i id="themeIcon" class="bi bi-toggle-off"></i></button>
          <form class="d-flex me-3" role="search">
            <input class="form-control form-control-sm me-2" type="search" placeholder="Search posts..." aria-label="Search">
            <button class="btn btn-outline-primary btn-sm" type="submit"><i class="bi bi-search"></i></button>
          </form>

          <!-- Right icons -->
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

    <!-- Profile -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="sidebarProfile">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title">Profile</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body text-center pb-3">
      <form action="pfp.php" method="post" enctype="multipart/form-data">
        <div class="container">
          <div class="profile-container d-inline-block position-relative">
            <?php if (empty($user['profile_pic'])): ?>
              <div id="profileIcon" class="rounded-circle d-flex justify-content-center align-items-center" style="cursor:pointer;">
                <i class="bi bi-person-circle" style="font-size: 7rem;"></i>
              </div>
            <?php else: ?>
              <img src="pfp/<?= htmlspecialchars($user['profile_pic']) ?>" id="profileImage" class="rounded-circle" width="150" height="150" style="object-fit:cover; cursor:pointer;" alt="Profile picture">
            <?php endif; ?>

            <!-- Hidden file input -->
            <input type="file" id="fileInput" name="profile_pic" accept="image/*" style="display:none;">
          </div>

          <p class="text-muted">Click to change pfp</p>
          <button type="submit" name="photo" class="btn btn-info">Upload</button>
        </div>
      </form>

      <script>
      document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('fileInput');
        const icon = document.getElementById('profileIcon');
        const img = document.getElementById('profileImage');
        const clickable = icon || img;

        if (!clickable) return;

        // ✅ When icon or image is clicked → open file picker
        clickable.addEventListener('click', function() {
          fileInput.click();
        });

        // ✅ When a file is chosen → preview it immediately
        fileInput.addEventListener('change', function() {
          const file = this.files[0];
          if (!file) return;

          const reader = new FileReader();
          reader.onload = function(e) {
            if (icon) {
              // Replace icon with new preview image
              const newImg = document.createElement('img');
              newImg.src = e.target.result;
              newImg.className = 'rounded-circle border border-3 border-info shadow mb-3';
              newImg.style.width = '150px';
              newImg.style.height = '150px';
              newImg.style.objectFit = 'cover';
              newImg.style.cursor = 'pointer';
              newImg.id = 'profileImage';
              icon.replaceWith(newImg);

              // Make the new image clickable too
              newImg.addEventListener('click', () => fileInput.click());
            } else {
              // Replace existing image preview
              img.src = e.target.result;
            }
          };
          reader.readAsDataURL(file);
        });
      });
      </script>

      <div class="mt-3">
        <h4 class="fw-semibold"><?= ($user['username']) ?></h4>
        <?php if (empty($user['bio'])): ?>
          <form action="bio.php" method="post" enctype="multipart/form-data">
            <label class="form-label">Update Bio</label>
            <textarea name="bio" class="form-control" rows="1" placeholder="" required></textarea>
            <button type="submit" class="btn btn-info mt-2">Update</button>
          </form>
        <?php else: ?>
          <p class="text-muted"> <?= $user['bio'] ?> </p>
        <?php endif ?>
      </div>

      <!-- Post Upload Form -->
      <div id="postSection" class="mt-3">
        <form action="upload_post.php" method="POST" enctype="multipart/form-data">
          <div class="mb-3">
            <label class="form-label text-primary">New post 📝:</label>
            <input type="text" name="title" class="form-control mb-2" placeholder="Enter blog title" required>
            <textarea name="content" class="form-control" rows="3" placeholder="What's on your mind?" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Upload Image</label>
            <input type="file" name="image" class="form-control">
          </div>
          <button type="submit" class="btn btn-success w-100" name="upload">Upload</button>
        </form>
      </div>

      <button class="btn btn-danger position-fixed bottom-0 end-0 m-4">
        <a href="logout.php" class="text-decoration-none text-white">Log Out</a>
      </button>
    </div>
  </div>

<!-- Notifications -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="sidebarNotif">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="notifTitle">Notifications</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
      <!-- Content Area -->
      <div id="sidebarContent">
        <div id="notifSection" class="">
          <ul class="list-group">
            <li class="list-group-item">Anna liked your post</li>
            <li class="list-group-item">David commented “Nice!”</li>
            <li class="list-group-item">Your post reached 100 likes!</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

<!-- Messages -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="sidebarMsg">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="msgTitle">Messages</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
      <!-- Content Area -->
      <div id="sidebarContent">
        <div id="msgSection" class="">
          <ul class="list-group">
            <li class="list-group-item">Mike: “Hey, how’s it going?”</li>
            <li class="list-group-item">Sara: “Let’s collab soon!”</li>
            <li class="list-group-item">Alex: “Nice new post!”</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>