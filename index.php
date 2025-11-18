<?php
include 'nav.php';
include 'connect.php';

$user = null;
if(isset($_SESSION['user_id'])) {
  $id = $_SESSION['user_id'];
  $sql = "SELECT * FROM users WHERE id = $id";
  $user_result = $conn -> query($sql);
  $user = $user_result -> fetch_assoc();
}

$sql = "SELECT posts.id AS id, posts.title, posts.content, posts.image, posts.created_at, users.username, users.profile_pic, users.id AS user_id  FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC";
$result = $conn -> query($sql);

$sql2 = "SELECT id, title, image, created_at FROM posts ORDER BY created_at DESC LIMIT 3";
$popular_posts = $conn -> query($sql2);

$count_sql = "SELECT post_id, COUNT(*) AS total FROM comments GROUP BY post_id";
$count_result = mysqli_query($conn, $count_sql);
$comment_count = [];
if ($count_result) {
  while ($row = mysqli_fetch_assoc($count_result)) {
    $comment_count[(int)$row['post_id']] = (int)$row['total'];
  }
}
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="lightMode">
  <div class="container my-4">
    <div class="hero text-center pt-3">
      <h3>Welcome to YZYblog</h3>
      <p>Share stories, connect with people, and explore trending topics.</p>
    </div>

    <!-- UPLOAD CARD -->
    <div class="row posts-entry mt-4">
      <div class="col-lg-8">
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <?php $id = htmlspecialchars($row['id']); ?>
            <div class="card mb-3 shadow-sm">
              <div class="row g-0">
                <div class="col-md-4">
                  <?php if (!empty($row['image'])): ?>
                    <img src="uploads/<?= htmlspecialchars($row['image']) ?>" class="img-fluid rounded-start w-100 h-100" alt="Post image">
                  <?php endif; ?>
                </div>

                <div class="col-md-8">
                  <div class="card-body">
                    <div class="d-flex text-muted small mb-2">
                      <span class="me-3"><?= htmlspecialchars($row['created_at']) ?></span>
                      <a href="#">
                        <?php if (!empty($row['profile_pic'])): ?>
                          <img src="pfp/<?= htmlspecialchars($row['profile_pic']) ?>" class="rounded-circle" width="30" height="30" alt="profile">
                        <?php else: ?>
                          <i class="bi bi-person-circle" style="font-size: 1rem;" alt="profile"></i>
                        <?php endif; ?>
                        <span>@<?= htmlspecialchars($row['username']) ?></span>
                      </a>
                    </div>

                    <a href="each_blog.php?id=<?= $id ?>" class="text-decoration-none text-dark"><h5 class="card-title text-uppercase fw-bold"><?= htmlspecialchars($row['title']) ?></h5></a>
                    <p class="card-text"><?= htmlspecialchars($row['content']) ?></p>
                    <span><a href="each_blog.php?id=<?= $id ?>" class="btn btn-sm btn-outline-primary me-3">Read More</a></span>
                    <span><i class="bi bi-chat me-2"></i><?= $comment_count[$row['id']] ?? 0 ?> Comment(s)</span>

                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['user_id']): ?>
                      <div class="d-flex justify-content-end gap-2 me-auto mt-2">
                        <button class="btn btn-sm btn-outline-warning" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editModal<?= $id ?>">Edit</button>

                        <a href="delete_post.php?delete_id=<?= $id ?>" 
                          class="btn btn-sm btn-outline-danger"
                          onclick="return confirm('Are you sure you want to delete this post?');">
                          Delete
                        </a>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>

            <!-- Edit Modal -->
            <div class="modal fade" id="editModal<?= $id ?>" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <form action="edit_post_process.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                      <h5 class="modal-title">Edit Post</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                      <input type="hidden" name="post_id" value="<?= $id ?>">

                      <!-- Title -->
                      <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" 
                              name="title" 
                              class="form-control" 
                              value="<?= htmlspecialchars($row['title']) ?>" 
                              required>
                      </div>

                      <!-- Content -->
                      <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea name="content" 
                                  class="form-control" 
                                  rows="4" 
                                  required><?= htmlspecialchars($row['content']) ?></textarea>
                      </div>

                      <!-- Preview current image -->
                      <?php if (!empty($row['image'])): ?>
                        <div class="mb-3">
                          <label class="form-label">Current Image</label><br>
                          <img src="uploads/<?= htmlspecialchars($row['image']) ?>" 
                              alt="Current post image" 
                              class="img-fluid rounded mb-2">
                        </div>
                      <?php endif; ?>

                      <!-- Change image -->
                      <div class="mb-3">
                        <label class="form-label">Change Image (optional)</label>
                        <input type="file" name="image" class="form-control">
                      </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" name="update" class="btn btn-success">Save Changes</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p class="text-muted">No posts yet. Be the first to share something!</p>
        <?php endif; ?>
      </div>

          <div class="col-lg-4 sidebar mb-4">
            <div class="sidebar-box">
              <h3 class="heading">Popular Posts</h3>
              <div class="post-entry-sidebar">
                <?php if ($popular_posts && mysqli_num_rows($popular_posts) > 0): ?>
                  <?php while ($row = mysqli_fetch_assoc($popular_posts)): ?>
                    <ul>
                      <li>
                        <a href="each_blog.php?id=<?= htmlspecialchars($row['id']) ?>">
                          <?php if (!empty($row['image'])): ?>
                            <img src="uploads/<?= htmlspecialchars($row['image']) ?>" class="me-4 rounded" width="50" height="50" alt="Post image">
                          <?php endif; ?>
                          <div class="text">
                            <h4><?= htmlspecialchars($row['title']) ?></h4>
                            <div class="post-meta">
                              <span class="mr-2"><?= htmlspecialchars($row['created_at']) ?></span>
                            </div>
                          </div>
                        </a>
                      </li>
                    </ul>
                  <?php endwhile; ?>
                <?php else: ?>
                  <p>No posts made yet.</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
    </div>
  </div>

<footer id="footer" class="footer bg-dark text-center text-white fixed-bottom py-1">
  <p>© Copyright <strong class="text-primary px-1">YZYblog.</strong> All Rights Reserved.</p>
</footer>

  <script src="script.js"></script>
</body>
</html>