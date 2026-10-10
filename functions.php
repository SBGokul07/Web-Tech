<?php
// functions.php - helper functions used by the admin pages

// escape any value before printing it in HTML (prevents XSS)
function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// flash message = message stored in session and shown once on the next page
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $message];
}

function showFlash() {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        echo '<div class="alert ' . e($f['type']) . '">' . e($f['msg']) . '</div>';
        unset($_SESSION['flash']);
    }
}

// converts bytes to KB / MB for display
function formatSize($bytes) {
    if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
    return round($bytes / 1024, 1) . ' KB';
}

// readable label for each file category
function categoryLabel($cat) {
    $labels = ['PROFILE' => 'Profile Photo', 'ID_PROOF' => 'ID Proof', 'CERTIFICATE' => 'Certificate'];
    return $labels[$cat] ?? $cat;
}

// checks the text fields of the student form and returns a list of error messages
function validateStudent($data) {
    $errors = [];
    if (!preg_match('/^[A-Za-z. ]{3,50}$/', $data['name'])) {
        $errors[] = 'Name should contain only letters, dot and space (3 to 50 characters).';
    }
    if (!preg_match('/^[0-9]{2}[A-Z]{2,4}[0-9]{3}$/', $data['roll_no'])) {
        $errors[] = 'Roll number format is wrong. Example: 23CS101';
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if ($data['phone'] !== '' && !preg_match('/^[6-9][0-9]{9}$/', $data['phone'])) {
        $errors[] = 'Phone number must be a valid 10 digit mobile number.';
    }
    if (!in_array($data['course'], COURSES, true)) {
        $errors[] = 'Please select a course from the list.';
    }
    return $errors;
}

// allowed MIME types for each category  (mime type => extension we save with)
function allowedTypes($category) {
    if ($category === 'PROFILE') {
        return ['image/jpeg' => 'jpg', 'image/png' => 'png'];
    }
    return ['application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png'];
}

/*
 * Validates one uploaded file and moves it into the uploads folder.
 * On success returns the details that have to be stored in MySQL.
 * On any problem it throws an Exception with a message we can show to the user.
 */
function saveUploadedFile($file, $category, $rollNo) {
    // 1. PHP's own upload error code
    if ($file['error'] !== UPLOAD_ERR_OK) {
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception($file['name'] . ' is bigger than the size allowed by the server.');
            case UPLOAD_ERR_PARTIAL:
                throw new Exception($file['name'] . ' was only partly uploaded. Please try again.');
            case UPLOAD_ERR_NO_FILE:
                throw new Exception('No file was selected.');
            default:
                throw new Exception('Server error while uploading ' . $file['name'] . '.');
        }
    }

    // 2. size check (different limit for photo and documents)
    $maxSize = ($category === 'PROFILE') ? MAX_IMAGE_SIZE : MAX_DOC_SIZE;
    if ($file['size'] > $maxSize) {
        throw new Exception($file['name'] . ' is too large. Maximum allowed is ' . ($maxSize / 1048576) . ' MB.');
    }
    if ($file['size'] === 0) {
        throw new Exception($file['name'] . ' is an empty file.');
    }

    // 3. type check - finfo reads the actual content of the file,
    //    so renaming virus.exe to photo.jpg will not fool it
    $finfo   = new finfo(FILEINFO_MIME_TYPE);
    $mime    = $finfo->file($file['tmp_name']);
    $allowed = allowedTypes($category);
    if (!isset($allowed[$mime])) {
        $msg = ($category === 'PROFILE') ? 'only JPG or PNG images are allowed' : 'only PDF, JPG or PNG files are allowed';
        throw new Exception($file['name'] . ' was rejected: ' . $msg . '.');
    }

    // 4. create a unique name so an existing file is never overwritten
    $subFolder = ($category === 'PROFILE') ? 'profile/' : 'documents/';
    $targetDir = UPLOAD_DIR . $subFolder;
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    do {
        $newName = strtolower($rollNo . '_' . $category) . '_' . date('Ymd_His') . '_'
                 . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
    } while (file_exists($targetDir . $newName));   // extra safety, practically never loops

    // 5. move from PHP temp folder to our uploads folder
    if (!move_uploaded_file($file['tmp_name'], $targetDir . $newName)) {
        throw new Exception('Could not save ' . $file['name'] . ' on the server.');
    }

    return [
        'original_name' => basename($file['name']),
        'stored_name'   => $newName,
        'file_path'     => $subFolder . $newName,   // relative path stored in DB
        'file_type'     => $mime,
        'file_size'     => $file['size'],
    ];
}

// stores the metadata of one uploaded file, linked to the student
function insertFileRecord($conn, $studentId, $category, $info) {
    $sql = "INSERT INTO student_files
                (student_id, file_category, original_name, stored_name, file_path, file_type, file_size)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isssssi', $studentId, $category, $info['original_name'],
                      $info['stored_name'], $info['file_path'], $info['file_type'], $info['file_size']);
    $stmt->execute();
}

// removes files from disk (used for clean up when something fails)
function removeFiles($paths) {
    foreach ($paths as $p) {
        $full = UPLOAD_DIR . $p;
        if (is_file($full)) {
            unlink($full);
        }
    }
}

// fetches one student row, returns null if not found
function getStudent($conn, $id) {
    $stmt = $conn->prepare('SELECT * FROM students WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// all files of a student, profile photo first and then latest uploads
function getStudentFiles($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM student_files WHERE student_id = ?
                            ORDER BY file_category = 'PROFILE' DESC, upload_date DESC");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// PHP drops $_POST and $_FILES completely if the request is bigger than post_max_size,
// so this check is needed to show a proper message in that case
function postTooLarge() {
    return $_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && empty($_FILES)
           && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0;
}
