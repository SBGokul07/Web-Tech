package com.kpr.portal.model;

import java.sql.Timestamp;
import java.util.ArrayList;
import java.util.List;

/** Student details along with the list of files uploaded for that student. */
public class Student {

    private int id;
    private String name;
    private String rollNo;
    private String email;
    private String phone;
    private String course;
    private Timestamp createdAt;
    private List<StudentFile> files = new ArrayList<>();

    /** returns the profile photo or null if the student has no photo */
    public StudentFile getProfilePhoto() {
        for (StudentFile f : files) {
            if ("PROFILE".equals(f.getCategory())) {
                return f;
            }
        }
        return null;
    }

    /** all files except the profile photo (ID proof, certificates) */
    public List<StudentFile> getDocuments() {
        List<StudentFile> docs = new ArrayList<>();
        for (StudentFile f : files) {
            if (!"PROFILE".equals(f.getCategory())) {
                docs.add(f);
            }
        }
        return docs;
    }

    /** first letters of the name, shown when there is no photo. "Arun Kumar S" -> "AK" */
    public String getInitials() {
        StringBuilder sb = new StringBuilder();
        for (String part : name.trim().split("\\s+")) {
            if (!part.isEmpty() && sb.length() < 2) {
                sb.append(Character.toUpperCase(part.charAt(0)));
            }
        }
        return sb.toString();
    }

    public void addFile(StudentFile f) {
        files.add(f);
    }

    // ----- getters and setters -----

    public int getId() { return id; }
    public void setId(int id) { this.id = id; }

    public String getName() { return name; }
    public void setName(String name) { this.name = name; }

    public String getRollNo() { return rollNo; }
    public void setRollNo(String rollNo) { this.rollNo = rollNo; }

    public String getEmail() { return email; }
    public void setEmail(String email) { this.email = email; }

    public String getPhone() { return phone; }
    public void setPhone(String phone) { this.phone = phone; }

    public String getCourse() { return course; }
    public void setCourse(String course) { this.course = course; }

    public Timestamp getCreatedAt() { return createdAt; }
    public void setCreatedAt(Timestamp createdAt) { this.createdAt = createdAt; }

    public List<StudentFile> getFiles() { return files; }
}
