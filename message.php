<?php
$status = $_GET['status'] ?? '';
$msg = $_GET['msg'] ?? '';
$bgcolor = ($status === 'success') ? '#d4edda' : '#f8d7da';
$textcolor = ($status === 'success') ? '#155724' : '#721c24';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Registration Message</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background: #f0f0f0;
    }
    .message-box {
      padding: 30px 50px;
      border-radius: 10px;
      background-color: <?= $bgcolor ?>;
      color: <?= $textcolor ?>;
      border: 1px solid <?= $textcolor ?>;
      text-align: center;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    a {
      display: inline-block;
      margin-top: 20px;
      text-decoration: none;
      color: white;
      background-color: #007bff;
      padding: 10px 20px;
      border-radius: 5px;
    }
    a:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>
  <div class="message-box">
    <h2><?= htmlspecialchars($msg) ?></h2>
    <a href="register.html">Back to Register</a>
  </div>
</body>
</html>
