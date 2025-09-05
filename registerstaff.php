<?php
// Database connection
include 'database.php';

// Get staff form values
$staff_id   = $_POST['staff_id'] ?? '';
$staff_name = $_POST['staff_name'] ?? '';
$email      = $_POST['email'] ?? '';
$phone      = $_POST['phone'] ?? '';
$gender     = $_POST['gender'] ?? '';
$password   = $_POST['password'] ?? '';

// ✅ Check if staff_id already exists
$stmt = $conn->prepare("SELECT staff_id FROM staff WHERE staff_id=?");
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    die("❌ This Staff ID is already registered!");
}
$stmt->close();

// ✅ Check if email already exists
$stmt = $conn->prepare("SELECT email FROM staff WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    die("❌ This Email is already registered!");
}
$stmt->close();

// ✅ Hash password before saving
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// ✅ Check if password already exists (compare hash in DB)
$stmt = $conn->prepare("SELECT password FROM staff");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    if (password_verify($password, $row['password'])) {
        die("❌ This Password is already used by another staff!");
    }
}
$stmt->close();

// ✅ Insert staff record
$stmt = $conn->prepare("INSERT INTO staff (staff_id, staff_name, email, phone, gender, password) VALUES (?,?,?,?,?,?)");
$stmt->bind_param("ssssss", $staff_id, $staff_name, $email, $phone, $gender, $hashed_password);

if ($stmt->execute()) {
    echo "✅ Staff registered successfully!";
} else {
    echo "❌ Registration failed: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
