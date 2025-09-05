<?php
include 'database.php';

$matric_no    = $_POST['matric_no'];
$student_name = $_POST['student_name'];
$password     = $_POST['password'];
$phone        = $_POST['phone'];
$faculty      = $_POST['faculty'];
$department   = $_POST['department'];
$level        = $_POST['level'];
$gender       = $_POST['gender'];

// ✅ Check if matric_no already exists
$stmt = $conn->prepare("SELECT matric_no FROM students WHERE matric_no=?");
$stmt->bind_param("s", $matric_no);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Already registered
    $stmt->close();
    header("Location: register.html?error=exists");
    exit;
}
$stmt->close();

// ✅ Hash password before saving (recommended)
// $hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO students (matric_no, student_name, password, phone, faculty, department, level, gender) VALUES (?,?,?,?,?,?,?,?)");
$stmt->bind_param("ssssssss", $matric_no, $student_name, $password, $phone, $faculty, $department, $level, $gender);

if ($stmt->execute()) {
    // Success → redirect to login page
    header("Location: login.html?status=success");
    exit;
} else {
    // Failed → redirect with error
    header("Location: register.html?error=failed");
    exit;
}

$stmt->close();
$conn->close();
?>
