<?php
// index.php - lists all students (the "View" part of CRUD) with search
require 'db.php';
require 'functions.php';

$search = trim($_GET['q'] ?? '');

// LEFT JOIN with the profile photo so that we can show a thumbnail,
// and a sub query to count how many documents each student has
$sql = "SELECT s.id, s.name, s.roll_no, s.email, s.course,
               p.file_path AS photo,
               (SELECT COUNT(*) FROM student_files d
                 WHERE d.student_id = s.id AND d.file_category <> 'PROFILE') AS doc_count
        FROM students s
        LEFT JOIN student_files p ON p.student_id = s.id AND p.file_category = 'PROFILE'";

if ($search !== '') {
    $sql .= " WHERE s.name LIKE ? OR s.roll_no LIKE ? OR s.course LIKE ?";
}
$sql .= " ORDER BY s.roll_no";

$stmt = $conn->prepare($sql);
if ($search !== '') {
    $like = '%' . $search . '%';
    $stmt->bind_param('sss', $like, $like, $like);
}
$stmt->execute();
$students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'All Students';
include 'header.php';
?>

<div class="page-head">
    <h2>Student Records <small>(<?= count($students) ?> found)</small></h2>
    <form method="get" class="search">
        <input type="text" name="q" value="<?= e($search) ?>" placeholder="Search name, roll no or course">
        <button type="submit" class="btn">Search</button>
        <?php if ($search !== ''): ?><a href="index.php" class="btn btn-light">Clear</a><?php endif; ?>
    </form>
</div>

<?php showFlash(); ?>

<?php if (empty($students)): ?>
    <p class="empty">No student records found. <a href="add.php">Add the first student</a>.</p>
<?php else: ?>
<div class="table-box">
<table class="list">
    <thead>
        <tr>
            <th>Photo</th><th>Roll No</th><th>Name</th><th>Course</th>
            <th>Email</th><th>Documents</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($students as $s): ?>
        <tr>
            <td>
                <?php if ($s['photo']): ?>
                    <img src="<?= UPLOAD_URL . e($s['photo']) ?>" class="thumb" alt="photo">
                <?php else: ?>
                    <span class="no-photo">No photo</span>
                <?php endif; ?>
            </td>
            <td><?= e($s['roll_no']) ?></td>
            <td><?= e($s['name']) ?></td>
            <td><?= e($s['course']) ?></td>
            <td><?= e($s['email']) ?></td>
            <td><?= (int)$s['doc_count'] ?></td>
            <td class="actions">
                <a href="view.php?id=<?= $s['id'] ?>" class="btn btn-sm">View</a>
                <a href="edit.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-warn">Edit</a>
                <!-- delete is done with POST, a plain link could be triggered by mistake -->
                <form action="delete.php" method="post" class="inline"
                      onsubmit="return confirm('Delete <?= e($s['name']) ?> and all uploaded files?');">
                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>

<?php include 'footer.php'; ?>
