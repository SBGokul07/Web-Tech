package com.kpr.portal.model;

import java.sql.Timestamp;

/** One row of the student_files table (metadata of an uploaded image / document). */
public class StudentFile {

    private int fileId;
    private int studentId;
    private String category;      // PROFILE, ID_PROOF or CERTIFICATE
    private String originalName;
    private String filePath;      // relative path inside the uploads folder
    private String fileType;      // MIME type
    private long fileSize;
    private Timestamp uploadDate;

    // ----- helper methods used in the JSP -----

    public boolean isImage() {
        return fileType != null && fileType.startsWith("image/");
    }

    public boolean isPdf() {
        return "application/pdf".equals(fileType);
    }

    public String getCategoryLabel() {
        switch (category) {
            case "PROFILE":  return "Profile Photo";
            case "ID_PROOF": return "ID Proof";
            default:         return "Certificate";
        }
    }

    public String getReadableSize() {
        if (fileSize >= 1024 * 1024) {
            return String.format("%.2f MB", fileSize / (1024.0 * 1024));
        }
        return String.format("%.1f KB", fileSize / 1024.0);
    }

    // ----- getters and setters -----

    public int getFileId() { return fileId; }
    public void setFileId(int fileId) { this.fileId = fileId; }

    public int getStudentId() { return studentId; }
    public void setStudentId(int studentId) { this.studentId = studentId; }

    public String getCategory() { return category; }
    public void setCategory(String category) { this.category = category; }

    public String getOriginalName() { return originalName; }
    public void setOriginalName(String originalName) { this.originalName = originalName; }

    public String getFilePath() { return filePath; }
    public void setFilePath(String filePath) { this.filePath = filePath; }

    public String getFileType() { return fileType; }
    public void setFileType(String fileType) { this.fileType = fileType; }

    public long getFileSize() { return fileSize; }
    public void setFileSize(long fileSize) { this.fileSize = fileSize; }

    public Timestamp getUploadDate() { return uploadDate; }
    public void setUploadDate(Timestamp uploadDate) { this.uploadDate = uploadDate; }
}
