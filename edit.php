<?php
// edit.php - "Update" part of CRUD.
// Details can be changed, the profile photo can be replaced and new documents can be added.
require 'db.php';
require 'functions.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$student = $id ? getStudent($conn, $id) : null;
if (!$student) {
    setFlash('error', 'Student not found.');
    header('Location: index.php');
    exit;
}

$errors = [];
$old = $student;   // form is filled with the current values

if (postTooLarge()) {
    $errors[] = 'The files you selected are too large for the server. Please choose smaller files.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = [
        'name'    => trim($_POST['name'] ?? ''),
        'roll_no' => strtoupper(trim($_POST['roll_no'] ?? '')),
        'email'   => strtolower(trim($_POST['email'] ?? '')),
        'phone'   => trim($_POST['phone'] ?? ''),
        'course'  => $_POST['course'] ?? '',
    ];
    $errors = validateStudent($old);

    $docCategory = $_POST['doc_category'] ?? '';
    if (!empty($_FILES['new_doc']['name']) && !in_array($docCategory, ['ID_PROOF', 'CERTIFICATE'], true)) {
        $errors[] = 'Select whether the new document is an ID proof or a certificate.';
    }

    if (empty($errors)) {
        $savedFiles = [];
        $oldPhotoToDelete = null;
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare('UPDATE students SET name = ?, roll_no = ?, email = ?, phone = ?, course = ? WHERE id = ?');
            $stmt->bind_param('sssssi', $old['name'], $old['roll_no'], $old['email'], $old['phone'], $old['course'], $id);
            $stmt->execute();

            // new profile photo -> save new one first, then remove the old record
            if (!empty($_FILES['profile']['name'])) {
                $info = saveUploadedFile($_FILES['profile'], 'PROFILE', $old['roll_no']);
                $savedFiles[] = $info['file_path'];

                $q = $conn->prepare("SELECT file_id, file_path FROM student_files WHERE student_id = ? AND file_category = 'PROFILE'");
                $q->bind_param('i', $id);
                $q->execute();
                $oldPhotoToDelete = $q->get_result()->fetch_assoc();

                if ($oldPhotoToDelete) {
                    $del = $conn->prepare('DELETE FROM student_files WHERE file_id = ?');
                    $del->bind_param('i', $oldPhotoToDelete['file_id']);
                    $del->execute();
                }
                insertFileRecord($conn, $id, 'PROFILE', $info);
            }

            // optional extra document
            if (!empty($_FILES['new_doc']['name'])) {
                $info = saveUploadedFile($_FILES['new_doc'], $docCategory, $old['roll_no']);
                $savedFiles[] = $info['file_path'];
                insertFileRecord($conn, $id, $docCategory, $info);
            }

            $conn->commit();

            // old photo file is deleted from disk only after the DB changes are committed
            if ($oldPhotoToDelete) {
                removeFiles([$oldPhotoToDelete['file_path']]);
            }

            setFlash('success', 'Details of ' . $old['name'] . ' updated successfully.');
            header('Location: view.php?id=' . $id);
            exit;

        } catch (mysqli_sql_exception $ex) {
            $conn->rollback();
            removeFiles($savedFiles);
            $errors[] = ($ex->getCode() == 1062)
                ? 'Another student already has this roll number or email.'
                : 'Database error, changes were not saved.';
            error_log($ex->getMessage());
        } catch (Exception $ex) {
            $conn->rollback();
            removeFiles($savedFiles);
            $errors[] = $ex->getMessage();
        }
    }
}

$files = getStudentFiles($conn, $id);

$pageTitle = 'Edit Student';
include 'header.php';
?>

<div class="card">
    <h2>Edit Student - <?= e($student['roll_no']) ?></h2>

    <?php if ($errors): ?>
        <div class="alert error">
            <strong>Please correct the following:</strong>
            <ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="form-grid">
        <label>Full Name *
            <input type="text" name="name" value="<?= e($old['name']) ?>" required>
        </label>
        <label>Roll Number *
            <input type="text" name="roll_no" value="<?= e($old['roll_no']) ?>" required>
        </label>
        <label>Email *
            <input type="email" name="email" value="<?= e($old['email']) ?>" required>
        </label>
        <label>Phone
            <input type="text" name="phone" value="<?= e($old['phone']) ?>" maxlength="10">
        </label>
        <label>Course *
            <select name="course" required>
                <?php foreach (COURSES as $c): ?>
                    <option value="<?= e($c) ?>" <?= $old['course'] === $c ? 'selected' : '' ?>><?= e($c) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Replace Profile Photo <small>(leave empty to keep current)</small>
            <input type="file" name="profile" accept="image/jpeg,image/png">
        </label>
        <label>Add Document
            <input type="file" name="new_doc" accept=".pdf,image/jpeg,image/png">
        </label>
        <label>Document Type
            <select name="doc_category">
                <option value="">-- Select --</option>
                <option value="ID_PROOF">ID Proof</option>
                <option value="CERTIFICATE">Certificate</option>
            </select>
        </label>

        <div class="form-actions">
            <a href="view.php?id=<?= $id ?>" class="btn btn-light">Cancel</a>
            <button type="submit" class="btn">Update Student</button>
        </div>
    </form>
</div>

<div class="card">
    <h3>Existing Files</h3>
    <?php if (empty($files)): ?>
        <p class="empty">No files uploaded yet.</p>
    <?php else: ?>
        <table class="list">
            <thead><tr><th>Type</th><th>File Name</th><th>Size</th><th>Uploaded On</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($files as $f): ?>
                <tr>
                    <td><?= categoryLabel($f['file_category']) ?></td>
                    <td><a href="<?= UPLOAD_URL . e($f['file_path']) ?>" target="_blank"><?= e($f['original_name']) ?></a></td>
                    <td><?= formatSize($f['file_size']) ?></td>
                    <td><?= date('d-m-Y h:i A', strtotime($f['upload_date'])) ?></td>
                    <td>
                        <form action="delete_file.php" method="post" class="inline"
                              onsubmit="return confirm('Delete this file permanently?');">
                            <input type="hidden" name="file_id" value="<?= $f['file_id'] ?>">
                            <input type="hidden" name="student_id" value="<?= $id ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
