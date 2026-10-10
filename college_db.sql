-- college_db.sql
-- Database schema for the Student Admission / Admin / Profile portal
-- Used by both the PHP admin panel (Question 2) and the Servlet portal (Question 3)
-- Run this in phpMyAdmin (SQL tab) or from the MySQL command line:  mysql -u root -p < college_db.sql

CREATE DATABASE IF NOT EXISTS college_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE college_db;

-- drop in reverse order because of the foreign key
DROP TABLE IF EXISTS student_files;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS portal_users;

-- 1. main student details
CREATE TABLE students (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    roll_no     VARCHAR(20)  NOT NULL UNIQUE,
    email       VARCHAR(100) NOT NULL UNIQUE,
    phone       VARCHAR(15),
    course      VARCHAR(50)  NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. metadata of every uploaded file (the file itself stays in the uploads folder)
CREATE TABLE student_files (
    file_id        INT AUTO_INCREMENT PRIMARY KEY,
    student_id     INT NOT NULL,
    file_category  ENUM('PROFILE', 'ID_PROOF', 'CERTIFICATE') NOT NULL,
    original_name  VARCHAR(255) NOT NULL,          -- name of the file on the user's computer
    stored_name    VARCHAR(255) NOT NULL UNIQUE,   -- unique name we saved it with
    file_path      VARCHAR(255) NOT NULL,          -- path relative to the uploads folder
    file_type      VARCHAR(100) NOT NULL,          -- MIME type, eg. image/png, application/pdf
    file_size      INT NOT NULL,                   -- size in bytes
    upload_date    DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_files_student FOREIGN KEY (student_id)
        REFERENCES students(id) ON DELETE CASCADE  -- deleting a student removes his file rows too
);

CREATE INDEX idx_files_student ON student_files(student_id);

-- 3. login table for the Servlet portal (passwords stored as SHA-256 hash, not plain text)
CREATE TABLE portal_users (
    user_id        INT AUTO_INCREMENT PRIMARY KEY,
    username       VARCHAR(50) NOT NULL UNIQUE,
    password_hash  CHAR(64) NOT NULL,
    full_name      VARCHAR(100),
    role           VARCHAR(20) DEFAULT 'STAFF'
);

-- default login accounts for testing
INSERT INTO portal_users (username, password_hash, full_name, role) VALUES
    ('admin',  SHA2('Admin@123', 256), 'Portal Administrator', 'ADMIN'),
    ('staff1', SHA2('Staff@123', 256), 'Class Advisor - III CSE A', 'STAFF');
