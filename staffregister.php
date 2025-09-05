<?php
// registerprocess.php

$servername = "localhost";
$dbuser     = "root";
$dbpass     = "";
$dbname     = "Studentdb";

$conn = new mysqli($servername, $dbuser, $dbpass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.html");
    exit;
}

$staff_name = trim($_POST['staff_name'] ?? '');
$phone        = trim($_POST['phone'] ?? '');
$email      = trim($_POST['email'] ?? '');
$gender       = trim($_POST['gender'] ?? '');
$staff_id    = trim($_POST['staff_id'] ?? '');
$password     = $_POST['password'] ?? '';

$missing = [];
if ($staff_name === '')    $missing[] = 'Staff Name';
if ($phone === '')        $missing[] = 'Phone';
if ($email === '') $missing[] = 'Email';
if ($gender === '')       $missing[] = 'Gender';
if ($staff_id === '')      $missing[] = 'Staff Id';
if ($password === '')     $missing[] = 'Password';

if (!empty($missing)) {
    $error_msg = "Missing required fields: " . implode(', ', $missing);
    header("Location: message.php?status=error&msg=" . urlencode($error_msg));
    $conn->close();
    exit;
}

// $hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO staff (staff_name, phone, email, gender, staff_id, password)
                        VALUES (?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    $error_msg = "Prepare failed: " . $conn->error;
    header("Location: message.php?status=error&msg=" . urlencode($error_msg));
    $conn->close();
    exit;
}

$stmt->bind_param("ssssss", $staff_name, $phone, $email, $gender, $staff_id, $password);

if ($stmt->execute()) {
    header("Location: login.html"
);
} else {
    header("Location: message.php?status=error&msg=" . urlencode("Error saving record: " . $stmt->error));
}

$stmt->close();
$conn->close();
?>
