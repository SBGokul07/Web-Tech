<?php
// delete.php - "Delete" part of CRUD. Removes the student row and all his files.
require 'db.php';
require 'functions.php';

// only POST is accepted so that a student is never deleted just by opening a link
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$student = $id ? getStudent($conn, $id) : null;

if (!$student) {
    setFlash('error', 'Student not found, nothing was deleted.');
    header('Location: index.php');
    exit;
}

try {
    // collect file paths before the rows disappear
    $paths = array_column(getStudentFiles($conn, $id), 'file_path');

    // ON DELETE CASCADE in the table removes the student_files rows automatically
    $stmt = $conn->prepare('DELETE FROM students WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();

    // now delete the actual files from the uploads folder
    removeFiles($paths);

    setFlash('success', 'Student ' . $student['name'] . ' and ' . count($paths) . ' file(s) deleted.');
} catch (mysqli_sql_exception $ex) {
    error_log($ex->getMessage());
    setFlash('error', 'Could not delete the student. Please try again.');
}

header('Location: index.php');
exit;
