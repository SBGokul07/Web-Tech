// ============================================
// STUDENT GRADE CALCULATOR
// ============================================

// Function to calculate the grade
function getGrade(average) {
    if (average >= 90) {
        return "A+";
    } else if (average >= 80) {
        return "A";
    } else if (average >= 70) {
        return "B";
    } else if (average >= 60) {
        return "C";
    } else if (average >= 50) {
        return "D";
    } else {
        return "F";
    }
}


// Main function
function calculateResult() {

    // Get marks from the five subjects
    const mark1 = Number(document.getElementById("subject1").value);
    const mark2 = Number(document.getElementById("subject2").value);
    const mark3 = Number(document.getElementById("subject3").value);
    const mark4 = Number(document.getElementById("subject4").value);
    const mark5 = Number(document.getElementById("subject5").value);


    // Check whether all marks are valid
    const marks = [mark1, mark2, mark3, mark4, mark5];

    for (let i = 0; i < marks.length; i++) {

        if (
            document.getElementById(`subject${i + 1}`).value === "" ||
            isNaN(marks[i]) ||
            marks[i] < 0 ||
            marks[i] > 100
        ) {
            alert(
                `Please enter valid marks between 0 and 100 for Subject ${i + 1}.`
            );
            return;
        }
    }


    // Calculate total
    const total = mark1 + mark2 + mark3 + mark4 + mark5;


    // Calculate average
    const average = total / 5;


    // Calculate grade
    const grade = getGrade(average);


    // Check pass/fail
    // Student must score at least 40 in EVERY subject
    const passed = marks.every(mark => mark >= 40);


    const status = passed ? "PASS" : "FAIL";


    // Display total
    document.getElementById("totalMarks").textContent = total;


    // Display average
    document.getElementById("average").textContent =
        average.toFixed(2);


    // Display grade
    document.getElementById("grade").textContent = grade;


    // Display pass/fail status
    document.getElementById("status").textContent = status;


    // Display message
    const message = document.getElementById("resultMessage");

    if (passed) {

        message.textContent =
            "Congratulations! The student has passed all subjects successfully.";

        message.className = "success-message";

    } else {

        message.textContent =
            "The student has failed because one or more subjects have marks below 40.";

        message.className = "failure-message";
    }
}