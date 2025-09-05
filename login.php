<?php
session_start();

// Database connection
include 'database.php';

// Get form values safely
$user_type = $_POST['user_type'] ?? '';
$password  = $_POST['password'] ?? '';

if ($user_type === 'student') {
    $matric_no = $_POST['matric_no'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM students WHERE matric_no=? LIMIT 1");
    $stmt->bind_param("s", $matric_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // ✅ Verify hashed password
        if ($password === $user['password']) {
            $_SESSION['matric_no'] = $user['matric_no'];
            $_SESSION['id']        = $user['id'] ?? null;
            $_SESSION['name']      = $user['student_name'] ?? $user['matric_no'];
            $_SESSION['role']      = "student";

            header("Location: CheckResult.php");
            exit;
        } else {
            echo "❌ Invalid Student Password!";
        }
    } else {
        echo "❌ Student not found!";
    }
} elseif ($user_type === 'staff') {
    $staff_id = $_POST['staff_id'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM staff WHERE staff_id=? LIMIT 1");
    $stmt->bind_param("s", $staff_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // ✅ Verify hashed password
        if ( ($password === $user['password'])) {
            $_SESSION['staff_id'] = $user['staff_id'];
            $_SESSION['id']       = $user['id'] ?? null;
            $_SESSION['name']     = $user['staff_name']; // ✅ fixed
            $_SESSION['role']     = "staff";

            header("Location: upload_score_process.php"); // ✅ better than upload_score_process.php
            exit;
        } else {
            echo "❌ Invalid Staff Password!";
        }
    } else {
        echo "❌ Staff not found!";
    }
}

