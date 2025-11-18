<?php
include 'nav.php';
include 'connect.php';

// 1) Get current post id from GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Post ID missing.");
}
$current_post_id = intval($_GET['id']);

// 2) Fetch the current post (including author info)
$sql = "SELECT posts.*, users.username, users.bio, users.profile_pic, posts.user_id AS author_id FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = ?";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "i", $current_post_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) === 0) {
    die("Post not found.");
}

$post = mysqli_fetch_assoc($result);
$author_id = (int)$post['author_id']; // author id for next query

// 3) Fetch other posts by same author (exclude current post)
// Prepare statement and check for failure
$sql2 = "SELECT id, title, image, created_at FROM posts WHERE user_id = ? ORDER BY created_at DESC";
$stmt2 = mysqli_prepare($conn, $sql2);
if (!$stmt2) {
    // prepare failed — show debug info (remove in production)
    die("Prepare for more-posts query failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt2, "i", $author_id);
$exec_ok = mysqli_stmt_execute($stmt2);
if (!$exec_ok) {
    die("Execute failed: " . mysqli_stmt_error($stmt2));
}
$all_posts = mysqli_stmt_get_result($stmt2);

$sql_comments = "SELECT comments.*, users.username, users.profile_pic FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = ? ORDER BY comments.created_at DESC";
$comments_stmt = mysqli_prepare($conn, $sql_comments);
mysqli_stmt_bind_param($comments_stmt, "i", $current_post_id);
mysqli_stmt_execute($comments_stmt);
$comments_result= mysqli_stmt_get_result($comments_stmt);

$count_sql = "SELECT COUNT(*) AS total FROM comments WHERE post_id = ?";
$count_stmt = mysqli_prepare($conn, $count_sql);
mysqli_stmt_bind_param($count_stmt, "i", $current_post_id);
mysqli_stmt_execute($count_stmt);
$count_result = mysqli_stmt_get_result($count_stmt);
$comment_count = mysqli_fetch_assoc($count_result)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?></title>
</head>
<body class="lightMode">
  <div class="site-cover site-cover-sm same-height overlay single-page" style="background-image: url('images/hero_5.jpg');">
    <div class="container">
      <div class="row same-height justify-content-center">
        <div class="col-md-6">
          <div class="post-entry text-center">
            <h1 class="mt-4"><?= htmlspecialchars($post['title']) ?></h1>
            <div class="post-meta align-items-center text-center">
              <figure class="author-figure mb-0 d-inline-block">
                <?php if (!empty($post['profile_pic'])): ?>
                  <img src="pfp/<?= htmlspecialchars($post['profile_pic']) ?>" class="rounded-circle" width="50" height="50" alt="profile">
                <?php else: ?>
                  <i class="bi bi-person-circle" style="font-size: 1rem;" alt="profile"></i>
                <?php endif; ?>
              </figure>
              <span class="d-inline-block mt-1">By <?= htmlspecialchars($post['username']) ?></span>
              <span>&nbsp;-&nbsp; <?= htmlspecialchars($post['created_at']) ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <section class="section">
    <div class="container">
      <div class="row blog-entries element-animate">
        <div class="col-md-12 col-lg-8 main-content">
          <div class="post-content-body"> 
            <div class="row my-4">
              <div class="col-md-12 text-center mb-4">
                <?php if (!empty($post['image'])): ?>
                  <img src="uploads/<?= htmlspecialchars($post['image']) ?>" class="img-fluid w-100 rounded" alt="Post image">
                <?php endif; ?>
              </div>
            </div>
            <p><?= htmlspecialchars($post['content']) ?></p>

            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
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
         
          <div class="comment-wrap shadow-sm rounded p-3 my-3">
            <?php if (mysqli_num_rows($comments_result) == 0): ?>
              <p>No comments on this post yet!</p>
            <?php else: ?>
            <?php while ($c = mysqli_fetch_assoc($comments_result)): ?>
              <h3 class="mb-5 heading">Comments: (<?= $comment_count ?>)</h3>
                <ul class="comment-list">
                  <li class="comment">
                    <div class="vcard">
                      <?php if (!empty($c['profile_pic'])): ?>
                        <img src="pfp/<?= htmlspecialchars($c['profile_pic']) ?>" class="rounded-circle" width="50" height="50" alt="profile">
                      <?php else: ?>
                        <i class="bi bi-person-circle" style="font-size: 1rem;" alt="profile"></i>
                      <?php endif; ?>
                    </div>
                    <div class="comment-body">
                      <h3><?= htmlspecialchars($c['username'])?></h3>
                      <div class="meta"><?= htmlspecialchars($c['created_at'])?></div>
                      <p><?= nl2br(htmlspecialchars($c['comment']))?></p>
                      <div class="d-flex justify-content-end gap-2 me-auto mt-2">
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $c['user_id']): ?>
                            <form method="POST" action="delete_comment.php" onsubmit="return confirm('Delete this comment?');">
                                <input type="hidden" name="comment_id" value="<?= $c['id'] ?>">
                                <input type="hidden" name="post_id" value="<?= $current_post_id ?>">
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        <?php endif; ?>
                      </div>
                  </li>
                </ul>
            <?php endwhile; ?>
          <?php endif; ?>
            <!-- END comment-list -->

            <div class="comment-form-wrap py-5 mb-5">
              <h3 class="mb-5">Leave a comment</h3>

              <?php if (isset($_SESSION['user_id'])): ?>
              <form action="add_comment.php" method="POST" class="p-5 bg-light">
                <input type="hidden" name="post_id" value="<?= $current_post_id ?>">
                <input type="hidden" name="parent_id" value="">
                <div class="input-group">
                  <input type="text" name="comment" class="form-control" placeholder="Write a comment..." required>
                  <button type="submit" name="submit_comment" class="btn btn-outline-primary ms-2">Post Comment</button>
                </div>
              </form>
              <?php else: ?>
                <p><a href="login.html" class="btn btn-outline-primary">Login</a> to comment on this post</p>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- END main-content -->

        <div class="col-md-12 col-lg-4 sidebar">
          <div class="sidebar-box">
            <div class="bio text-center">
              <?php if (!empty($post['profile_pic'])): ?>
                <img src="pfp/<?= htmlspecialchars($post['profile_pic']) ?>" class="rounded-circle border border-info" width="250" height="250" alt="profile">
              <?php else: ?>
                <i class="bi bi-person-circle" style="font-size: 1rem;" alt="profile"></i>
              <?php endif; ?>
              <div class="bio-body">
                <h2><?= htmlspecialchars($post['username']) ?></h2>
                <p class="mb-4"><?= htmlspecialchars($post['bio']) ?></p>
                <p class="social">
                  <a href="#" class="p-2"><i class="bi bi-facebook"></i></a>
                  <a href="#" class="p-2"><i class="bi bi-twitter"></i></a>
                  <a href="#" class="p-2"><i class="bi bi-instagram"></i></a>
                  <a href="#" class="p-2"><i class="bi bi-youtube"></i></a>
                </p>
              </div>
            </div>
          </div>
          <!-- END sidebar-box -->  
          <div class="sidebar-box mb-5">
            <h3 class="heading">All Posts</h3>
            <div class="post-entry-sidebar">
              <?php if ($all_posts && mysqli_num_rows($all_posts) > 0): ?>
                <?php while ($p = mysqli_fetch_assoc($all_posts)): ?>
                  <ul>
                    <li>
                      <a href="each_blog.php?id=<?= htmlspecialchars($p['id']) ?>">
                        <?php if (!empty($p['image'])): ?>
                          <img src="uploads/<?= htmlspecialchars($p['image']) ?>" class="me-4 rounded" width="50" height="50" alt="Post image">
                        <?php endif; ?>
                        <div class="text">
                          <h4><?= htmlspecialchars($p['title']) ?></h4>
                          <div class="post-meta">
                            <span class="mr-2"><?= htmlspecialchars($p['created_at']) ?></span>
                          </div>
                        </div>
                      </a>
                    </li>
                  </ul>
                <?php endwhile; ?>
              <?php else: ?>
                <p>No other posts by this author.</p>
              <?php endif; ?>
            </div>
          </div>
          <!-- END sidebar-box -->
        </div>
        <!-- END sidebar -->

      </div>
    </div>
  </section>

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
                      value="<?= htmlspecialchars($post['title']) ?>" 
                      required>
              </div>

              <!-- Content -->
              <div class="mb-3">
                <label class="form-label">Content</label>
                <textarea name="content" 
                          class="form-control" 
                          rows="4" 
                          required><?= htmlspecialchars($post['content']) ?></textarea>
              </div>

              <!-- Preview current image -->
              <?php if (!empty($post['image'])): ?>
                <div class="mb-3">
                  <label class="form-label">Current Image</label><br>
                  <img src="uploads/<?= htmlspecialchars($post['image']) ?>" 
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

  <footer id="footer" class="footer bg-dark text-center text-white fixed-bottom py-1">
    <p>© Copyright <strong class="text-primary px-1">YZYblog.</strong> All Rights Reserved.</p>
  </footer>
</body>
</html>