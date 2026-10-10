package com.kpr.portal.dao;

import com.kpr.portal.model.Student;
import com.kpr.portal.model.StudentFile;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;

/** All database work related to students and their files is kept in this class. */
public class StudentDAO {

    // one query fetches students together with their files using LEFT JOIN,
    // so a student without any file still comes in the result
    private static final String BASE_QUERY =
            "SELECT s.id, s.name, s.roll_no, s.email, s.phone, s.course, s.created_at, "
          + "       f.file_id, f.file_category, f.original_name, f.file_path, f.file_type, "
          + "       f.file_size, f.upload_date "
          + "FROM students s "
          + "LEFT JOIN student_files f ON f.student_id = s.id ";

    /** returns all students (or only matching ones when search text is given) */
    public List<Student> getAllStudents(String search) throws SQLException {
        boolean hasSearch = search != null && !search.trim().isEmpty();

        String sql = BASE_QUERY
                + (hasSearch ? "WHERE s.name LIKE ? OR s.roll_no LIKE ? OR s.course LIKE ? " : "")
                + "ORDER BY s.roll_no, f.upload_date";

        try (Connection con = DBConnection.getConnection();
             PreparedStatement ps = con.prepareStatement(sql)) {

            if (hasSearch) {
                String like = "%" + search.trim() + "%";
                ps.setString(1, like);
                ps.setString(2, like);
                ps.setString(3, like);
            }

            try (ResultSet rs = ps.executeQuery()) {
                return buildStudentList(rs);
            }
        }
    }

    /** returns one student with files, or null when the id does not exist */
    public Student getStudentById(int id) throws SQLException {
        String sql = BASE_QUERY + "WHERE s.id = ? ORDER BY f.upload_date";

        try (Connection con = DBConnection.getConnection();
             PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setInt(1, id);
            try (ResultSet rs = ps.executeQuery()) {
                List<Student> list = buildStudentList(rs);
                return list.isEmpty() ? null : list.get(0);
            }
        }
    }

    /** used by FileServlet to find where a file is stored */
    public StudentFile getFileById(int fileId) throws SQLException {
        String sql = "SELECT * FROM student_files WHERE file_id = ?";

        try (Connection con = DBConnection.getConnection();
             PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setInt(1, fileId);
            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next()) {
                    return mapFile(rs);
                }
            }
        }
        return null;
    }

    /*
     * The JOIN gives one row per file, so the same student comes many times.
     * A LinkedHashMap (keeps insertion order) is used to group the rows by student id.
     */
    private List<Student> buildStudentList(ResultSet rs) throws SQLException {
        Map<Integer, Student> map = new LinkedHashMap<>();

        while (rs.next()) {
            int id = rs.getInt("id");
            Student s = map.get(id);

            if (s == null) {                       // first row of this student
                s = new Student();
                s.setId(id);
                s.setName(rs.getString("name"));
                s.setRollNo(rs.getString("roll_no"));
                s.setEmail(rs.getString("email"));
                s.setPhone(rs.getString("phone"));
                s.setCourse(rs.getString("course"));
                s.setCreatedAt(rs.getTimestamp("created_at"));
                map.put(id, s);
            }

            rs.getInt("file_id");
            if (!rs.wasNull()) {                   // file_id is NULL when the student has no files
                s.addFile(mapFile(rs));
            }
        }
        return new ArrayList<>(map.values());
    }

    private StudentFile mapFile(ResultSet rs) throws SQLException {
        StudentFile f = new StudentFile();
        f.setFileId(rs.getInt("file_id"));
        f.setCategory(rs.getString("file_category"));
        f.setOriginalName(rs.getString("original_name"));
        f.setFilePath(rs.getString("file_path"));
        f.setFileType(rs.getString("file_type"));
        f.setFileSize(rs.getLong("file_size"));
        f.setUploadDate(rs.getTimestamp("upload_date"));
        return f;
    }
}
