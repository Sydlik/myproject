// =============================
// Register Page Validation
// =============================
if (document.querySelector("form[action='register_process.php']")) {
  document.querySelector("form").addEventListener("submit", function (e) {
    let studentID = document.getElementById("studentid").value.trim();
    let matric_no = document.getElementById("matric_no").value.trim();
    let email = document.getElementById("email").value.trim();

    if (studentID === "" || matric_no === "" || email === "") {
      alert("⚠ Please fill in all required fields!");
      e.preventDefault();
    }

    // Email format check
    let emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
    if (!email.match(emailPattern)) {
      alert("⚠ Please enter a valid email address!");
      e.preventDefault();
    }
  });
}

// =============================
// Upload Scores Page Validation
// =============================
if (document.querySelector("form[action='upload_process.php']")) {
  document.querySelector("form").addEventListener("submit", function (e) {
    let testScore = document.getElementById("Testscoreid").value.trim();

    if (isNaN(testScore)) {
      alert("⚠ Test score must be a number!");
      e.preventDefault();
    } else if (testScore < 0 || testScore > 30) {
      alert("⚠ Test score must be between 0 and 30!");
      e.preventDefault();
    }
  });
}

// =============================
// Check Results Page (Demo Display)
// =============================
if (document.querySelector("form[action='check_result_process.php']")) {
  document.querySelector("form").addEventListener("submit", function (e) {
    e.preventDefault(); // stop actual submission (for demo only)

    let studentID = document.getElementById("studentid").value.trim();
    if (studentID === "") {
      alert("⚠ Enter your Student ID!");
      return;
    }

    // Fake result (you can replace with PHP output later)
    let resultBox = document.createElement("div");
    resultBox.style.marginTop = "20px";
    resultBox.style.padding = "15px";
    resultBox.style.background = "#e6f7ff";
    resultBox.style.border = "1px solid #004080";
    resultBox.innerHTML = `
      <h3>Result for Student ID: ${studentID}</h3>
      <p>Test Score: <b>85</b></p>
      <p>Status: <b style="color:green">Pass ✅</b></p>
    `;
    document.body.appendChild(resultBox);
  });
}
