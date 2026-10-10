<?php // header.php - common top part of every admin page. Set $pageTitle before including. ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Admin Panel') ?> | Student Admin Panel</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<header class="topbar">
    <div class="brand">Student Records <span>Admin Panel</span></div>
    <nav>
        <a href="index.php">All Students</a>
        <a href="add.php" class="nav-btn">+ Add Student</a>
    </nav>
</header>
<main class="wrap">
