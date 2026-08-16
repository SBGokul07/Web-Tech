// ============================================
// Q4 - STUDENT GRADE CALCULATOR
// ============================================

// Get the form
const form = document.querySelector("form");

// Get result elements
const totalMarksElement = document.getElementById("totalMarks");
const averageElement = document.getElementById("average");
const gradeElement = document.getElementById("grade");
const statusElement = document.getElementById("status");
const messageElement = document.getElementById("message");


// ============================================
// FORM SUBMISSION
// ============================================

form.addEventListener("submit", function (event) {

    // Prevent page refresh
    event.preventDefault();

    // Get all number inputs
    const inputs = form.querySelectorAll('input[type="number"]');

    const marks = [];

    inputs.forEach(function (input) {
        marks.push(Number(input.value));
    });


    // ============================================
    // VALIDATION
    // ============================================

    // Check whether all fields are filled
    if (marks.some(mark => isNaN(mark))) {
        alert("Please enter marks for all subjects.");
        return;
    }

    // Check marks range
    if (marks.some(mark => mark < 0 || mark > 100)) {
        alert("Marks must be between 0 and 100.");
        return;
    }


    // ============================================
    // CALCULATIONS
    // ============================================

    // Calculate total
    const total = marks.reduce(function (sum, mark) {
        return sum + mark;
    }, 0);

    // Calculate average
    const average = total / marks.length;


    // ============================================
    // GRADE CALCULATION
    // ============================================

    let grade;

    if (average >= 90) {
        grade = "A+";
    }
    else if (average >= 80) {
        grade = "A";
    }
    else if (average >= 70) {
        grade = "B";
    }
    else if (average >= 60) {
        grade = "C";
    }
    else if (average >= 50) {
        grade = "D";
    }
    else {
        grade = "F";
    }


    // ============================================
    // PASS / FAIL
    // ============================================

    // Student MUST score at least 40 in EVERY subject
    const hasFailedSubject = marks.some(function (mark) {
        return mark < 40;
    });

    let status;

    if (hasFailedSubject) {
        status = "FAIL";
    }
    else {
        status = "PASS";
    }


    // ============================================
    // DISPLAY RESULTS
    // ============================================

    totalMarksElement.textContent = total;
    averageElement.textContent = average.toFixed(2);
    gradeElement.textContent = grade;
    statusElement.textContent = status;


    // ============================================
    // DISPLAY MESSAGE
    // ============================================

    if (status === "PASS") {

        messageElement.textContent =
            "Congratulations! The student has passed all subjects successfully.";

        // Optional CSS classes
        statusElement.classList.remove("fail");
        statusElement.classList.add("pass");

        messageElement.classList.remove("fail-message");
        messageElement.classList.add("success-message");

    }
    else {

        messageElement.textContent =
            "The student has failed because one or more subjects are below 40 marks.";

        statusElement.classList.remove("pass");
        statusElement.classList.add("fail");

        messageElement.classList.remove("success-message");
        messageElement.classList.add("fail-message");
    }

});