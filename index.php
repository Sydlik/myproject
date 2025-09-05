<?php
session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
    <h2>Role: <?php echo htmlspecialchars($_SESSION['role']); ?></h2>

    <nav>
      <?php if ($_SESSION['role'] === 'student'): ?>
        <a href="checkResult.php">Check Results</a>
      <?php elseif ($_SESSION['role'] === 'lecturer'): ?>
        <a href="upload_scores_process.php">Upload Scores</a>
      <?php endif; ?>
      <a href="logout.php">Logout</a>
    </nav>

    <div class="card-container">
      <?php if ($_SESSION['role'] === 'student'): ?>
        <div class="card">
          <img src="images/Check result.jpg" alt="Check Results">
          <div class="card-contents">
            <h3>Check Results</h3>
            <p class="text-body">View your semester results here</p>
            <a href="checkResult.php" class="btn">Check Results</a>
          </div>
        </div>
      <?php elseif ($_SESSION['role'] === 'lecturer'): ?>
        <div class="card">
          <img src="images/upload.png" alt="Upload Scores">
          <div class="card-contents">
            <h3>Upload Scores</h3>
            <p class="text-body">Upload test and exam scores by course</p>
            <a href="upload_scores.php" class="btn">Upload Scores</a>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
