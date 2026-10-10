<?php
// delete_file.php - removes a single uploaded file (row + physical file)
require 'db.php';
require 'functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$fileId    = filter_input(INPUT_POST, 'file_id', FILTER_VALIDATE_INT);
$studentId = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);

// student_id is also checked so that a file of another student cannot be deleted by changing the id
$stmt = $conn->prepare('SELECT file_path, original_name FROM student_files WHERE file_id = ? AND student_id = ?');
$stmt->bind_param('ii', $fileId, $studentId);
$stmt->execute();
$file = $stmt->get_result()->fetch_assoc();

if ($file) {
    $del = $conn->prepare('DELETE FROM student_files WHERE file_id = ?');
    $del->bind_param('i', $fileId);
    $del->execute();
    removeFiles([$file['file_path']]);
    setFlash('success', $file['original_name'] . ' was deleted.');
} else {
    setFlash('error', 'File not found.');
}

header('Location: view.php?id=' . (int)$studentId);
exit;
