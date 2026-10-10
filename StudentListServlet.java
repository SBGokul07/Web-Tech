package com.kpr.portal.servlet;

import com.kpr.portal.dao.StudentDAO;
import com.kpr.portal.model.Student;

import jakarta.servlet.ServletException;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import java.io.IOException;
import java.sql.SQLException;
import java.util.List;

/** Fetches the list of students from MySQL and passes it to students.jsp */
public class StudentListServlet extends HttpServlet {

    private final StudentDAO studentDAO = new StudentDAO();

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        String search = request.getParameter("q");

        try {
            List<Student> students = studentDAO.getAllStudents(search);
            request.setAttribute("students", students);
            request.setAttribute("q", search == null ? "" : search);
        } catch (SQLException e) {
            log("Could not load students", e);
            request.setAttribute("error", "Unable to load student records. Please check the database connection.");
        }

        request.getRequestDispatcher("/WEB-INF/views/students.jsp").forward(request, response);
    }
}
