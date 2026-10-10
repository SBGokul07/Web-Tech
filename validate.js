// validate.js - client side validation for the student registration form
// Every field is checked while typing (live) and once again when Register is clicked.

const form = document.getElementById("regForm");

// limits for uploaded files
const MAX_PHOTO_SIZE = 2 * 1024 * 1024;   // 2 MB
const MAX_PDF_SIZE = 5 * 1024 * 1024;     // 5 MB

// ---------- helper functions to show / clear messages ----------
function setError(element, message) {
    const field = element.closest(".field");
    field.classList.add("invalid");
    field.classList.remove("valid");
    field.querySelector(".error").textContent = message;
    return false;
}

function setValid(element) {
    const field = element.closest(".field");
    field.classList.remove("invalid");
    field.classList.add("valid");
    field.querySelector(".error").textContent = "";
    return true;
}

// ---------- individual field checks ----------
function checkName() {
    const name = form.fullName.value.trim();
    if (name === "") return setError(form.fullName, "Name is required");
    if (!/^[A-Za-z. ]{3,50}$/.test(name))
        return setError(form.fullName, "Only letters, dot and space allowed (3 to 50 characters)");
    return setValid(form.fullName);
}

function checkRollNo() {
    // converting to upper case so that 23cs101 and 23CS101 are treated the same
    const roll = form.rollNo.value.trim().toUpperCase();
    form.rollNo.value = roll;
    if (roll === "") return setError(form.rollNo, "Roll number is required");
    // format: 2 digit year + 2 to 4 letter dept code + 3 digit number, eg. 23CS101
    if (!/^[0-9]{2}[A-Z]{2,4}[0-9]{3}$/.test(roll))
        return setError(form.rollNo, "Invalid format. Example: 23CS101");
    return setValid(form.rollNo);
}

function checkEmail() {
    const email = form.email.value.trim();
    if (email === "") return setError(form.email, "Email is required");
    const pattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!pattern.test(email)) return setError(form.email, "Enter a valid email (eg. name@gmail.com)");
    return setValid(form.email);
}

function checkMobile() {
    const mobile = form.mobile.value.trim();
    if (mobile === "") return setError(form.mobile, "Mobile number is required");
    // Indian mobile numbers are 10 digits and start with 6, 7, 8 or 9
    if (!/^[6-9][0-9]{9}$/.test(mobile))
        return setError(form.mobile, "Enter a valid 10 digit mobile number");
    return setValid(form.mobile);
}

function checkCourse() {
    if (form.course.value === "") return setError(form.course, "Please select a course");
    return setValid(form.course);
}

function checkGender() {
    const picked = form.querySelector('input[name="gender"]:checked');
    const firstRadio = form.querySelector('input[name="gender"]');
    if (!picked) return setError(firstRadio, "Please select gender");
    return setValid(firstRadio);
}

// ---------- password strength ----------
// returns which rules are satisfied, used by both the meter and the final check
function passwordRules(pwd) {
    return {
        len: pwd.length >= 8,
        upper: /[A-Z]/.test(pwd),
        lower: /[a-z]/.test(pwd),
        digit: /[0-9]/.test(pwd),
        special: /[^A-Za-z0-9]/.test(pwd)
    };
}

function updateStrengthMeter() {
    const pwd = form.password.value;
    const rules = passwordRules(pwd);
    const score = Object.values(rules).filter(Boolean).length;   // 0 to 5

    // tick / cross in the rules list
    document.querySelectorAll("#pwdRules li").forEach(li => {
        li.classList.toggle("ok", rules[li.dataset.rule]);
    });

    const bar = document.getElementById("strengthBar");
    const text = document.getElementById("strengthText");
    let label = "", colour = "";

    if (pwd.length === 0)  { label = "";       colour = "transparent"; }
    else if (score <= 2)   { label = "Weak";   colour = "#d93025"; }
    else if (score <= 4)   { label = "Medium"; colour = "#f29900"; }
    else                   { label = "Strong"; colour = "#188038"; }

    bar.style.width = (score * 20) + "%";
    bar.style.background = colour;
    text.textContent = label ? "Strength: " + label : "";
    text.style.color = colour;
}

function checkPassword() {
    const pwd = form.password.value;
    if (pwd === "") return setError(form.password, "Password is required");
    const rules = passwordRules(pwd);
    // all five rules must pass, otherwise password is not strong enough
    if (!Object.values(rules).every(Boolean))
        return setError(form.password, "Password is not strong enough, see the rules above");
    return setValid(form.password);
}

function checkConfirmPassword() {
    const cpwd = form.confirmPassword.value;
    if (cpwd === "") return setError(form.confirmPassword, "Please confirm your password");
    if (cpwd !== form.password.value) return setError(form.confirmPassword, "Passwords do not match");
    return setValid(form.confirmPassword);
}

// ---------- file checks ----------
function checkPhoto() {
    const file = form.photo.files[0];
    const preview = document.getElementById("photoPreview");
    preview.style.display = "none";

    if (!file) return setError(form.photo, "Please upload your photo");
    if (!["image/jpeg", "image/png"].includes(file.type))
        return setError(form.photo, "Only JPG or PNG images are allowed");
    if (file.size > MAX_PHOTO_SIZE)
        return setError(form.photo, "Photo size should be less than 2 MB");

    // show a small preview of the selected photo
    preview.src = URL.createObjectURL(file);
    preview.style.display = "block";
    return setValid(form.photo);
}

function checkMarksheet() {
    const file = form.marksheet.files[0];
    if (!file) {                     // optional field, so empty is fine
        form.marksheet.closest(".field").classList.remove("invalid", "valid");
        form.marksheet.closest(".field").querySelector(".error").textContent = "";
        return true;
    }
    if (file.type !== "application/pdf" && !file.name.toLowerCase().endsWith(".pdf"))
        return setError(form.marksheet, "Mark sheet must be a PDF file");
    if (file.size > MAX_PDF_SIZE)
        return setError(form.marksheet, "PDF size should be less than 5 MB");
    return setValid(form.marksheet);
}

function checkAgree() {
    if (!form.agree.checked) return setError(form.agree, "You must accept the declaration");
    return setValid(form.agree);
}

// ---------- live validation while the user types ----------
form.fullName.addEventListener("input", checkName);
form.rollNo.addEventListener("blur", checkRollNo);
form.email.addEventListener("input", checkEmail);
form.mobile.addEventListener("input", checkMobile);
form.course.addEventListener("change", checkCourse);
form.querySelectorAll('input[name="gender"]').forEach(r => r.addEventListener("change", checkGender));
form.password.addEventListener("input", () => {
    updateStrengthMeter();
    checkPassword();
    if (form.confirmPassword.value !== "") checkConfirmPassword();
});
form.confirmPassword.addEventListener("input", checkConfirmPassword);
form.photo.addEventListener("change", checkPhoto);
form.marksheet.addEventListener("change", checkMarksheet);
form.agree.addEventListener("change", checkAgree);

// ---------- final check on submit ----------
form.addEventListener("submit", function (event) {
    event.preventDefault();

    // run every check (not using && because we want all errors to show at once)
    const results = [
        checkName(), checkRollNo(), checkEmail(), checkMobile(), checkCourse(),
        checkGender(), checkPassword(), checkConfirmPassword(), checkPhoto(),
        checkMarksheet(), checkAgree()
    ];

    if (results.includes(false)) {
        // take the user to the first wrong field
        const firstBad = form.querySelector(".field.invalid input, .field.invalid select");
        if (firstBad) firstBad.focus();
        return;
    }

    // everything is fine. In the full project the data is sent to the server here (form.submit()).
    form.hidden = true;
    document.getElementById("successMsg").textContent =
        "Thank you " + form.fullName.value.trim() + " (" + form.rollNo.value +
        "). Your registration for " + form.course.value + " has been received.";
    document.getElementById("successBox").hidden = false;
});

// clearing the form should also clear the coloured borders and messages
form.addEventListener("reset", function () {
    setTimeout(() => {
        form.querySelectorAll(".field").forEach(f => {
            f.classList.remove("invalid", "valid");
            const err = f.querySelector(".error");
            if (err) err.textContent = "";
        });
        updateStrengthMeter();
        document.getElementById("photoPreview").style.display = "none";
    }, 0);
});

document.getElementById("newEntry").addEventListener("click", function () {
    form.reset();
    form.hidden = false;
    document.getElementById("successBox").hidden = true;
});
