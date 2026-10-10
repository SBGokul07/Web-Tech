<?php
// add.php - "Create" part of CRUD. Adds a student along with photo / documents.
require 'db.php';
require 'functions.php';

$errors = [];
$old = ['name' => '', 'roll_no' => '', 'email' => '', 'phone' => '', 'course' => ''];

if (postTooLarge()) {
    $errors[] = 'The files you selected are too large for the server. Please choose smaller files.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // read and clean the form values (kept in $old so the form is refilled on error)
    $old = [
        'name'    => trim($_POST['name'] ?? ''),
        'roll_no' => strtoupper(trim($_POST['roll_no'] ?? '')),
        'email'   => strtolower(trim($_POST['email'] ?? '')),
        'phone'   => trim($_POST['phone'] ?? ''),
        'course'  => $_POST['course'] ?? '',
    ];

    $errors = validateStudent($old);
    if (empty($_FILES['profile']['name'])) {
        $errors[] = 'Profile photo is required.';
    }

    if (empty($errors)) {
        $savedFiles = [];              // remember saved files so we can delete them if DB insert fails
        $conn->begin_transaction();    // student row + file rows are saved together or not at all
        try {
            $stmt = $conn->prepare('INSERT INTO students (name, roll_no, email, phone, course) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('sssss', $old['name'], $old['roll_no'], $old['email'], $old['phone'], $old['course']);
            $stmt->execute();
            $studentId = $conn->insert_id;

            // form field name => category stored in the database
            $uploadFields = ['profile' => 'PROFILE', 'id_proof' => 'ID_PROOF', 'certificate' => 'CERTIFICATE'];

            foreach ($uploadFields as $field => $category) {
                if (!empty($_FILES[$field]['name'])) {   // ID proof and certificate are optional
                    $info = saveUploadedFile($_FILES[$field], $category, $old['roll_no']);
                    $savedFiles[] = $info['file_path'];
                    insertFileRecord($conn, $studentId, $category, $info);
                }
            }

            $conn->commit();
            setFlash('success', 'Student ' . $old['name'] . ' (' . $old['roll_no'] . ') was added successfully.');
            header('Location: index.php');
            exit;

        } catch (mysqli_sql_exception $ex) {
            $conn->rollback();
            removeFiles($savedFiles);
            // 1062 = duplicate entry (roll_no and email are UNIQUE in the table)
            $errors[] = ($ex->getCode() == 1062)
                ? 'A student with this roll number or email already exists.'
                : 'Database error, the record was not saved.';
            error_log($ex->getMessage());
        } catch (Exception $ex) {
            // file validation errors thrown by saveUploadedFile()
            $conn->rollback();
            removeFiles($savedFiles);
            $errors[] = $ex->getMessage();
        }
    }
}

$pageTitle = 'Add Student';
include 'header.php';
?>

<div class="card">
    <h2>Add New Student</h2>

    <?php if ($errors): ?>
        <div class="alert error">
            <strong>Please correct the following:</strong>
            <ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <!-- enctype multipart/form-data is a must, otherwise files are not sent -->
    <form method="post" enctype="multipart/form-data" class="form-grid">
        <label>Full Name *
            <input type="text" name="name" value="<?= e($old['name']) ?>" required>
        </label>
        <label>Roll Number *
            <input type="text" name="roll_no" value="<?= e($old['roll_no']) ?>" placeholder="23CS101" required>
        </label>
        <label>Email *
            <input type="email" name="email" value="<?= e($old['email']) ?>" required>
        </label>
        <label>Phone
            <input type="text" name="phone" value="<?= e($old['phone']) ?>" maxlength="10">
        </label>
        <label>Course *
            <select name="course" required>
                <option value="">-- Select --</option>
                <?php foreach (COURSES as $c): ?>
                    <option value="<?= e($c) ?>" <?= $old['course'] === $c ? 'selected' : '' ?>><?= e($c) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <span></span>

        <label>Profile Photo * <small>(JPG/PNG, max 2 MB)</small>
            <input type="file" name="profile" accept="image/jpeg,image/png">
        </label>
        <label>ID Proof <small>(PDF/JPG/PNG, max 5 MB)</small>
            <input type="file" name="id_proof" accept=".pdf,image/jpeg,image/png">
        </label>
        <label>Certificate <small>(PDF/JPG/PNG, max 5 MB)</small>
            <input type="file" name="certificate" accept=".pdf,image/jpeg,image/png">
        </label>

        <div class="form-actions">
            <a href="index.php" class="btn btn-light">Cancel</a>
            <button type="submit" class="btn">Save Student</button>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>
