<?php
// db.php - database connection and common settings.
// Every page includes this file first.

// change these according to your XAMPP / WAMP setup
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'college_db');

// uploaded files are kept inside this folder (profile/ and documents/ sub folders)
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', 'uploads/');

// size limits in bytes
define('MAX_IMAGE_SIZE', 2 * 1024 * 1024);   // 2 MB for profile photo
define('MAX_DOC_SIZE', 5 * 1024 * 1024);     // 5 MB for ID proof / certificates

// list of courses shown in the drop down (also used for server side validation)
define('COURSES', ['B.E. CSE', 'B.Tech IT', 'B.Tech AI&DS', 'B.E. ECE', 'B.E. EEE', 'B.E. MECH']);

// make mysqli throw exceptions instead of silently returning false
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Indian time for date() and for MySQL timestamps
date_default_timezone_set('Asia/Kolkata');

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4');
    $conn->query("SET time_zone = '+05:30'");
} catch (mysqli_sql_exception $e) {
    // the real error goes to the log file, the user only sees a simple message
    error_log('DB connection failed: ' . $e->getMessage());
    die('<h3 style="color:#c00;font-family:Arial">Unable to connect to the database. Check the settings in db.php</h3>');
}

// session is used only to carry success / error messages between pages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
