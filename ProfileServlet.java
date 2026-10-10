package com.kpr.portal.servlet;

import com.kpr.portal.dao.StudentDAO;
import com.kpr.portal.model.Student;

import jakarta.servlet.ServletException;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import java.io.IOException;
import java.sql.SQLException;

/** Shows the full profile of one student:  /profile?id=3 */
public class ProfileServlet extends HttpServlet {

    private final StudentDAO studentDAO = new StudentDAO();

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        int id;
        try {
            id = Integer.parseInt(request.getParameter("id"));
        } catch (NumberFormatException e) {
            // id missing or not a number
            response.sendRedirect(request.getContextPath() + "/students");
            return;
        }

        try {
            Student student = studentDAO.getStudentById(id);
            if (student == null) {
                response.sendError(HttpServletResponse.SC_NOT_FOUND, "Student not found");
                return;
            }
            request.setAttribute("student", student);
            request.getRequestDispatcher("/WEB-INF/views/profile.jsp").forward(request, response);

        } catch (SQLException e) {
            log("Could not load profile " + id, e);
            response.sendError(HttpServletResponse.SC_INTERNAL_SERVER_ERROR, "Database error");
        }
    }
}
