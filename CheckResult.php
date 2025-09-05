<?php
session_start();
if (!isset($_SESSION['matric_no'])) {
    header('Location: login.html');
    exit;
}

$matric_no = $_SESSION['matric_no'];

// connect to DB
include 'database.php';

// fetch personal details
$details_stmt = $conn->prepare(
    'SELECT matric_no, student_name, faculty, department, level
     FROM students
     WHERE matric_no = ?'
);
$details_stmt->bind_param('s', $matric_no);
$details_stmt->execute();
$details = $details_stmt->get_result()->fetch_assoc();

$student_name = $details['student_name'] ?? '';
$faculty      = $details['faculty'] ?? '';
$department   = $details['department'] ?? '';
$level        = $details['level'] ?? '';

// Handle semester selection (from dropdown)
$selected_semester = $_GET['semester'] ?? '';

// fetch distinct semesters for this student
$semester_result = $conn->prepare(
    'SELECT DISTINCT semester 
     FROM results 
     WHERE matric_no = ? 
     ORDER BY semester'
);
$semester_result->bind_param('s', $matric_no);
$semester_result->execute();
$semester_rows = $semester_result->get_result()->fetch_all(MYSQLI_ASSOC);

// fetch results depending on semester filter
if (!empty($selected_semester)) {
    $stmt = $db->prepare(
        'SELECT semester, level, course_code, course_title, test_score, exam_score, total_score
         FROM results
         WHERE matric_no = ? AND semester = ?
         ORDER BY course_code'
    );
    $stmt->bind_param('ss', $matric_no, $selected_semester);
} else {
    $stmt = $conn->prepare(
        'SELECT semester, level, course_code, course_title, test_score, exam_score, total_score
         FROM results
         WHERE matric_no = ?
         ORDER BY semester, course_code'
    );
    $stmt->bind_param('s', $matric_no);
}
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// course code ↔ title map (autofill + enforce)
$course_map = [
    'CSC311' => 'Mobile Application Development',
    'CSC312' => 'Object-Oriented Programming in C++',
    'CSC313' => 'System Analysis and Design',
    'CSC314' => 'Computer Architecture and Organization I',
    'CSC315' => 'Operating System II',
    'CSC316' => 'Algorithms and Complexity Analysis',
    'CSC317' => 'Cryptography II',
    'CSC321' => 'Survey of Programming Languages',
    'CSC322' => 'Computational Science & Numerical Methods',
    'CSC323' => 'Web Database Design and Management',
    'CSC324' => 'Computer Architecture and Organization II',
    'CSC325' => 'Compiler Construction I',
    'CSC326' => 'Industrial Training II',
    'CSC327' => 'Data Management',
];

// summary calculations
$total_units = 0;
$total_score = 0;
foreach ($rows as &$r) {
    // Auto–fill title from map if code exists
    if (isset($course_map[$r['course_code']])) {
        $r['course_title'] = $course_map[$r['course_code']];
    }

    // Force unit = 3 for all courses
    $r['unit'] = 3;

    $total_units += $r['unit'];
    $total_score += $r['total_score'];
}
unset($r);

$num_courses = count($rows);
$average_score = $num_courses > 0 ? round($total_score / $num_courses, 2) : 'N/A';
?>
<!doctype html>
<html>
<head>
  <title>My Results</title>
  <link rel="stylesheet" type="text/css" href="login.css">
  <style>
   body {
  font-family: 'Inter', Arial, sans-serif;
  background: #f9fafb;
  color: #1f2937;
  margin: 0;
  padding: 20px;
}

/* Navbar */
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #1f2937; /* dark gray */
  color: #fff;
  padding: 12px 24px;
  border-radius: 0.5rem;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}
.navbar a {
  color: #f9fafb;
  text-decoration: none;
  margin-left: 20px;
  font-weight: 500;
  transition: color 0.2s;
}
.navbar a:hover {
  color: #93c5fd; /* light blue */
}

/* Container */
.container {
  max-width: 1000px;
  margin: 30px auto;
  background: #fff;
  padding: 24px;
  border-radius: 0.75rem;
  box-shadow: 0 4px 10px rgba(0,0,0,0.08);
}
h2, h3 {
  font-weight: 600;
  color: #111827;
  margin-bottom: 12px;
}

/* Table */
table {
  border-collapse: collapse;
  width: 100%;
  margin-bottom: 20px;
  font-size: 0.95rem;
}
table th {
  background: #f3f4f6;
  color: #374151;
  font-weight: 600;
  padding: 10px;
  text-align: left;
}
table td {
  border-top: 1px solid #e5e7eb;
  padding: 10px;
}
table tr:nth-child(even) {
  background: #f9fafb;
}

/* Summary box */
.summary {
  margin-top: 20px;
  padding: 15px;
  background: #f3f4f6;
  border-radius: 0.5rem;
  font-weight: 500;
  color: #1f2937;
}

/* Error */
.error {
  color: #b91c1c;
  font-weight: 600;
  padding: 10px 15px;
  background: #fee2e2;
  border-radius: 0.5rem;
  border: 1px solid #fecaca;
}

/* Filter form */
.filter-form {
  margin-bottom: 20px;
}
.filter-form select,
.filter-form button {
  padding: 8px 14px;
  margin-right: 10px;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 0.9rem;
  transition: all 0.2s;
}
.filter-form select:focus,
.filter-form button:focus {
  outline: none;
  border-color: #60a5fa;
  box-shadow: 0 0 0 2px #bfdbfe;
}
.filter-form button {
  background: #2563eb;
  color: white;
  border: none;
  cursor: pointer;
  font-weight: 500;
}
.filter-form button:hover {
  background: #1d4ed8;
}

  </style>
</head>
<body>
<div class="navbar">
  <div class="nav-left">Student Assessment Record System</div>
  <div class="nav-right"><a href="logout.php">Logout</a></div>
</div>

<div class="container">
  <h2>Results for Matric No: <?php echo htmlspecialchars($matric_no); ?></h2>

  <!-- Personal Details -->
  <div class="personal-details">
    <h3>Personal Details</h3>
    <table>
      <tr><td>Matriculation Number</td><td><?php echo htmlspecialchars($details['matric_no']?? ''); ?></td></tr>
      <tr><td>Student Name</td><td><?php echo htmlspecialchars($student_name); ?></td></tr>
      <tr><td>Faculty</td><td><?php echo htmlspecialchars($faculty); ?></td></tr>
      <tr><td>Department</td><td><?php echo htmlspecialchars($department); ?></td></tr>
      <tr><td>Level</td><td><?php echo htmlspecialchars($level); ?></td></tr>
    </table>
  </div>

  <!-- Semester Filter Form -->
  <div class="filter-form">
    <form method="get" action="">
      <label for="semester">Select Semester:</label>
      <select name="semester" id="semester">
          <option value="">-- All Semesters --</option>
          <?php foreach ($semester_rows as $sem): ?>
              <option value="<?php echo htmlspecialchars($sem['semester']); ?>"
                  <?php if ($selected_semester === $sem['semester']) echo 'selected'; ?>>
                  <?php echo htmlspecialchars($sem['semester']); ?>
              </option>
          <?php endforeach; ?>
      </select>
      <button type="submit">View Results</button>
    </form>
  </div>

  <!-- Results -->
  <?php if ($num_courses === 0): ?>
    <p class="error">No results found.</p>
  <?php else: ?>
    <h3>Course Results <?php echo $selected_semester ? " - " . htmlspecialchars($selected_semester) . " Semester" : ""; ?></h3>
    <table class="results-table">
      <thead>
        <tr>
          <th>Semester</th>
          <th>Level</th>
          <th>Course Code</th>
          <th>Course Title</th>
          <th>Unit</th>
          <th>Test Score</th>
          <th>Exam Score</th>
          <th>Total Score</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?php echo htmlspecialchars($r['semester']); ?></td>
            <td><?php echo htmlspecialchars($r['level']); ?></td>
            <td><?php echo htmlspecialchars($r['course_code']); ?></td>
            <td><?php echo htmlspecialchars($r['course_title']); ?></td>
            <td><?php echo htmlspecialchars($r['unit']); ?></td>
            <td><?php echo htmlspecialchars($r['test_score']); ?></td>
            <td><?php echo htmlspecialchars($r['exam_score']); ?></td>
            <td><?php echo htmlspecialchars($r['total_score']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <!-- Summary -->
    <div class="summary">
      <p>Total Units: <?php echo $total_units; ?></p>
      <p>Number of Courses: <?php echo $num_courses; ?></p>
      <p>Average Total Score: <?php echo $average_score; ?></p>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
