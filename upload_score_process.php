<?php
include 'database.php';

// Process form only if submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ✅ Match form names correctly
    $student_name = trim($_POST['student_name'] ?? '');
    $matric_no    = trim($_POST['matricNo'] ?? '');  // fixed name
    $course_code  = trim($_POST['course'] ?? '');
    $course_title = trim($_POST['course_title'] ?? '');
    $unit         = intval($_POST['course_unit'] ?? 0); // fixed name
    $semester     = trim($_POST['semester'] ?? '');
    $test_score   = intval($_POST['test_score'] ?? 0);
    $exam_score   = intval($_POST['exam_score'] ?? 0);
    $total_score  = $test_score + $exam_score;

    // Validate inputs
    if ($unit < 1 || $unit > 3) {
        die("❌ Invalid course unit. Must be between 1 and 3.");
    }
    if ($test_score < 0 || $test_score > 30 || $exam_score < 0 || $exam_score > 70) {
        die("❌ Invalid score values. Test must be 0-30 and Exam 0-70.");
    }

    // ✅ Insert into results table
    $sql = "INSERT INTO results (student_name, matric_no, course_code, course_title, unit, semester, test_score, exam_score, total_score) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssissii", $student_name, $matric_no, $course_code, $course_title, $unit, $semester, $test_score, $exam_score, $total_score);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>✅ Score uploaded successfully for " . htmlspecialchars($student_name) . " (" . htmlspecialchars($matric_no) . ")</p>";
    } else {
        echo "<p style='color:red;'>❌ Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html> 
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Upload Scores</title>
  <link rel="stylesheet" href="upload.css">
</head>
<body>
  <!-- Navbar -->
  <div class="navbar">
    <div class="nav-left">Student Assessment Record System</div>
    <div class="nav-right"><a href="index.html" style='color: white; text-decoration: none; font-weight: 600; '>Back to Home</a></div>
  </div>

  <!-- Page Info -->
  <div class="page-info">
    <p>Upload student score (one at a time)</p>
  </div>

  <!-- Upload Scores Form -->
  <div class="container">
    <h2>Upload Scores</h2>

    <form action="" method="POST">
      
      <!-- Semester -->
      <label for="semester">Select Semester:</label><br>
      <select name="semester" id="semester" required onchange="updateCourses()">
        <option value="">-- Select Semester --</option>
        <option value="1st Semester">1st Semester</option>
        <option value="2nd Semester">2nd Semester</option>
      </select>
      <br><br>

      <!-- Select Course -->
      <label for="course">Select Course Code:</label><br>
      <select name="course" id="course" required onchange="updateCourseTitle()">
        <option value="">-- Select Semester First --</option>
      </select>
      <br><br>

      <!-- Course Title -->
      <label for="course_title">Course Title:</label><br>
      <input type="text" name="course_title" id="course_title" readonly required>
      <br><br>

      <!-- Course Unit -->
      <label for="course_unit">Course Unit:</label><br>
      <input type="text" name="course_unit" id="course_unit" value="3" readonly required>
      <br><br>

      <!-- Student Name -->
      <label for="student_name">Student Name:</label><br>
      <input type="text" name="student_name" id="student_name" placeholder="Enter name" required>
      <br><br>

      <!-- Matric Number -->
      <label for="matricNo">Matric Number:</label><br>
      <input type="text" name="matricNo" id="matricNo" maxlength="8" placeholder="Enter Matric No" required>
      <br><br>

      <!-- Test Score -->
      <label for="test_score">Test Score (0 - 30):</label><br>
      <input type="number" name="test_score" id="test_score" min="0" max="30" required>
      <br><br>

      <!-- Exam Score -->
      <label for="exam_score">Exam Score (0 - 70):</label><br>
      <input type="number" name="exam_score" id="exam_score" min="0" max="70" required>
      <br><br>

      <!-- Submit Button -->
      <input type="submit" value="Submit Score">
    </form>
  </div>

  <script>
    // Mapping of courses
    const coursesMap = {
      "1st Semester": {
        "CSC311": "Mobile Application Development",
        "CSC312": "Object-Oriented Programming in C++",
        "CSC313": "System Analysis and Design",
        "CSC314": "Computer Architecture and Organization I",
        "CSC315": "Operating System II",
        "CSC316": "Algorithms and Complexity Analysis",
        "CSC317": "Cryptography II"
      },
      "2nd Semester": {
        "CSC321": "Survey of Programming Languages",
        "CSC322": "Computational Science & Numerical Methods",
        "CSC323": "Web Database Design and Management",
        "CSC324": "Computer Architecture and Organization II",
        "CSC325": "Compiler Construction I",
        "CSC326": "Industrial Training II",
        "CSC327": "Data Management"
      }
    };

    // Update courses when semester is selected
    function updateCourses() {
      let semester = document.getElementById("semester").value;
      let courseSelect = document.getElementById("course");
      courseSelect.innerHTML = "";

      if (coursesMap[semester]) {
        Object.keys(coursesMap[semester]).forEach(code => {
          let option = document.createElement("option");
          option.value = code;
          option.text = code;
          courseSelect.appendChild(option);
        });
      } else {
        let option = document.createElement("option");
        option.value = "";
        option.text = "-- Select Semester First --";
        courseSelect.appendChild(option);
      }

      // reset title when semester changes
      document.getElementById("course_title").value = "";
    }

    // Auto-fill course title & unit
    function updateCourseTitle() {
      let semester = document.getElementById("semester").value;
      let courseCode = document.getElementById("course").value;
      let titleField = document.getElementById("course_title");
      let unitField = document.getElementById("course_unit");

      if (coursesMap[semester] && coursesMap[semester][courseCode]) {
        titleField.value = coursesMap[semester][courseCode];
        unitField.value = "3"; // all courses are 3 units
      } else {
        titleField.value = "";
        unitField.value = "";
      }
    }
  </script>

</body>
</html>
