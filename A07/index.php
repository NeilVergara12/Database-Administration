<?php
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['userID'])) {
    $userID = $_POST['userID'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $content = $_POST['content'];
    $privacy = $_POST['privacy'];
    $dateTime = date("Y-m-d H:i:s");

    $checkUserQuery = "SELECT userID FROM userInfo WHERE userID='$userID'";
    $userResult = mysqli_query($conn, $checkUserQuery);

    if (mysqli_num_rows($userResult) == 0) {
        $UserQuery = "INSERT INTO userInfo (userID, firstName, lastName) VALUES ('$userID', '$firstName', '$lastName')";
        mysqli_query($conn, $UserQuery);
    }

    $PostQuery = "INSERT INTO posts (userID, content, dateTime, privacy) VALUES ('$userID', '$content', '$dateTime', '$privacy')";
    mysqli_query($conn, $PostQuery);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btnEdit'])) {
    $postID = $_POST['postID'];
    $newContent = $_POST['content'];

    $updateQuery = "UPDATE posts SET content = '$newContent' WHERE postID = '$postID'";

    $result = mysqli_query($conn, $updateQuery);

    if ($result) {
        header("Location: " . $_SERVER["PHP_SELF"]);
        exit;
    } else {
        echo "Failed to update the post: " . mysqli_error($conn);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btnDelete'])) {
    $deletePostID = $_POST['id'];

    $deleteQuery = "DELETE FROM posts WHERE postID = '$deletePostID'";
    mysqli_query($conn, $deleteQuery);

    header("Location: " . $_SERVER["PHP_SELF"]);
    exit;
}


$query = "
  SELECT posts.postID, userInfo.firstName, userInfo.lastName, posts.content, posts.dateTime, posts.privacy, posts.isDeleted, cities.name AS city, provinces.name AS province
  FROM posts
  LEFT JOIN userInfo ON posts.userID = userInfo.userID
  LEFT JOIN addresses ON userInfo.addressID = addresses.addressID
  LEFT JOIN cities ON addresses.cityID = cities.cityID
  LEFT JOIN provinces ON addresses.provinceID = provinces.provinceID
  ORDER BY posts.dateTime DESC";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Nyelibook.com</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="icon/hatIcon.png" sizes="32x32">
  <style>
    body {
      background-color: #b3b2fd;
      overflow-x: hidden;
    }

    .card {
      width: 100%;
      margin: 0 auto;
    }

    .navbar {
      background-color: #bdbce5;
      color: black;
      margin-bottom: 50px;
      box-shadow: 0 7px 15px rgba(0, 0, 0, 0.2);
    }

    .card-body {
      position: relative;
    }

    .delete-btn, .edit-btn {
      position: absolute;
      top: 10px;
      right: 10px;
    }

    .edit-btn {
      right: 90px;
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <h1>Straw Hat Feed</h1>
    </div>
  </nav>

  <!-- Post submission form -->
  <div class="container my-4">
    <h3>Create a New Post</h3>
    <form id="postForm" method="POST">
      <div class="mb-3">
        <label for="userID" class="form-label">User ID</label>
        <input type="number" class="form-control" id="userID" name="userID" required>
      </div>
      <div class="mb-3">
        <label for="firstName" class="form-label">First Name</label>
        <input type="text" class="form-control" id="firstName" name="firstName" required>
      </div>
      <div class="mb-3">
        <label for="lastName" class="form-label">Last Name</label>
        <input type="text" class="form-control" id="lastName" name="lastName" required>
      </div>
      <div class="mb-3">
        <label for="content" class="form-label">Content</label>
        <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
      </div>
      <div class="mb-3">
        <label for="privacy" class="form-label">Privacy</label>
        <select class="form-control" id="privacy" name="privacy" required>
          <option value="Public">Public</option>
          <option value="Private">Private</option>
          <option value="Friends">Friends</option>
          <option value="Only Me">Only Me</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Create Post</button>
    </form>
  </div>

  <!-- Display posts in cards -->
  <div class="container" id="postsContainer">
    <div class="row">
      <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($post = mysqli_fetch_assoc($result)): ?>
          <div class="col-12">
            <div class="card rounded-4 shadow my-3">
              <div class="card-body">
                <h5 class="card-title">
                  <?php echo htmlspecialchars($post["firstName"] . " " . $post["lastName"]); ?>
                </h5>
                <h6 class="card-subtitle mb-2 text-muted">
                  <?php echo htmlspecialchars($post["city"] . " " . $post["province"]); ?>
                </h6>
                <p class="card-text"><?php echo nl2br(htmlspecialchars($post["content"])); ?></p>
                <p class="text-muted" style="font-size: 0.9em;">Posted on: <?php echo date("F j, Y, g:i a", strtotime($post["dateTime"])); ?></p>
                <p class="text-muted" style="font-size: 0.9em;">Privacy: <?php echo htmlspecialchars($post["privacy"]); ?></p>

                <!-- Edit and Delete Form -->
                <form method="post" class="button">
                  <input type="hidden" value="<?php echo $post['postID']; ?>" name="id">
                  <a href="index.php?editPostID=<?php echo $post['postID']; ?>" class="btn btn-primary mb-2 float-start edit-btn">Edit</a>
                  <button class="btn btn-danger float-end delete-btn" name="btnDelete">Delete</button>
                </form>

                <?php if (isset($_GET['editPostID']) && $_GET['editPostID'] == $post['postID']): ?>
                  <form method="POST">
                    <textarea class="form-control" name="content" rows="3"><?php echo htmlspecialchars($post['content']); ?></textarea>
                    <button type="submit" name="btnEdit" class="btn btn-primary mt-2">Update Post</button>
                    <input type="hidden" name="postID" value="<?php echo $post['postID']; ?>">
                  </form>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="text-center">No posts available.</p>
      <?php endif; ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
