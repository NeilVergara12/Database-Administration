<?php
include("connect.php");

$query = "
  SELECT posts.postID, userinfo.firstName, userinfo.lastName, posts.content, posts.dateTime, posts.privacy, posts.isDeleted, cities.name AS city, provinces.name AS province
  FROM posts
  LEFT JOIN userinfo ON posts.userID = userinfo.userID
  LEFT JOIN addresses ON userinfo.addressID = addresses.addressID
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

  <style>
    body {
      background-color: #b3b2fd;
      overflow-x: hidden; 
    }

    .card {
      width: 100%; 
      margin: 0 auto; 
    }
  </style>
  
</head>

<body>
  <div class="container-fluid shadow mb-5 p-3">
    <h1>Straw Hat Feed</h1>
  </div>

  <div class="container"> <!-- container to hold cards -->
    <div class="row">
      <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($post = mysqli_fetch_assoc($result)): ?>
          <div class="col-12"> <!-- One card per column -->
            <div class="card rounded-4 shadow my-3" 
              <?php if ($post["isDeleted"] == "yes") echo "style='background-color: pink'"; ?>
            >
              <div class="card-body">
                <h5 class="card-title">
                  <?php echo htmlspecialchars($post["firstName"] . " " . $post["lastName"]); ?>
                </h5>
                
                <h6 class="card-subtitle mb-2 text-muted">
                  <?php
                    echo htmlspecialchars($post["city"] . " " . $post["province"] ?? "Location not specified");
                  ?>
                </h6>
                
                <p class="card-text">
                  <?php echo nl2br(htmlspecialchars($post["content"])); ?>
                </p>
                
                <p class="text-muted" style="font-size: 0.9em;">
                  Posted on: <?php echo date("F j, Y, g:i a", strtotime($post["dateTime"])); ?>
                </p>
                
                <p class="text-muted" style="font-size: 0.9em;">
                  Privacy: <?php echo htmlspecialchars($post["privacy"]); ?>
                </p>
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
