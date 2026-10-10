<?php
// view.php - shows one student with the profile photo and all uploaded documents
require 'db.php';
require 'functions.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$student = $id ? getStudent($conn, $id) : null;

if (!$student) {
    setFlash('error', 'Student not found.');
    header('Location: index.php');
    exit;
}

$files = getStudentFiles($conn, $id);

// separate the profile photo from the other documents
$photo = null;
$documents = [];
foreach ($files as $f) {
    if ($f['file_category'] === 'PROFILE' && $photo === null) {
        $photo = $f;
    } else {
        $documents[] = $f;
    }
}

$pageTitle = $student['name'];
include 'header.php';
?>

<?php showFlash(); ?>

<div class="card profile">
    <div class="profile-photo">
        <?php if ($photo): ?>
            <img src="<?= UPLOAD_URL . e($photo['file_path']) ?>" alt="Profile photo of <?= e($student['name']) ?>">
        <?php else: ?>
            <div class="no-photo big">No Photo</div>
        <?php endif; ?>
    </div>

    <div class="profile-info">
        <h2><?= e($student['name']) ?></h2>
        <table class="details">
            <tr><th>Roll Number</th><td><?= e($student['roll_no']) ?></td></tr>
            <tr><th>Course</th><td><?= e($student['course']) ?></td></tr>
            <tr><th>Email</th><td><?= e($student['email']) ?></td></tr>
            <tr><th>Phone</th><td><?= e($student['phone'] ?: '-') ?></td></tr>
            <tr><th>Added On</th><td><?= date('d M Y, h:i A', strtotime($student['created_at'])) ?></td></tr>
        </table>
        <div class="form-actions left">
            <a href="edit.php?id=<?= $id ?>" class="btn btn-warn">Edit Details</a>
            <a href="index.php" class="btn btn-light">Back to List</a>
        </div>
    </div>
</div>

<div class="card">
    <h3>Uploaded Documents</h3>
    <?php if (empty($documents)): ?>
        <p class="empty">No documents uploaded for this student.</p>
    <?php else: ?>
        <table class="list">
            <thead>
                <tr><th>Type</th><th>File Name</th><th>Format</th><th>Size</th><th>Uploaded On</th><th>Preview / Open</th></tr>
            </thead>
            <tbody>
            <?php foreach ($documents as $d): ?>
                <tr>
                    <td><?= categoryLabel($d['file_category']) ?></td>
                    <td><?= e($d['original_name']) ?></td>
                    <td><?= e($d['file_type']) ?></td>
                    <td><?= formatSize($d['file_size']) ?></td>
                    <td><?= date('d-m-Y h:i A', strtotime($d['upload_date'])) ?></td>
                    <td>
                        <?php if (str_starts_with($d['file_type'], 'image/')): ?>
                            <!-- images are shown directly -->
                            <a href="<?= UPLOAD_URL . e($d['file_path']) ?>" target="_blank">
                                <img src="<?= UPLOAD_URL . e($d['file_path']) ?>" class="doc-thumb" alt="document">
                            </a>
                        <?php else: ?>
                            <!-- PDFs open in a new tab -->
                            <a href="<?= UPLOAD_URL . e($d['file_path']) ?>" target="_blank" class="btn btn-sm">Open PDF</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
