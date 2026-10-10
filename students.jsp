<%@ page contentType="text/html; charset=UTF-8" pageEncoding="UTF-8" %>
<%@ page import="java.util.List, com.kpr.portal.model.Student, com.kpr.portal.model.StudentFile" %>
<%@ page import="com.kpr.portal.util.HtmlUtil" %>
<%
    // data sent by StudentListServlet
    @SuppressWarnings("unchecked")
    List<Student> students = (List<Student>) request.getAttribute("students");
    String q = (String) request.getAttribute("q");
    String error = (String) request.getAttribute("error");
    String ctx = request.getContextPath();
%>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students | Student Profile Portal</title>
    <link rel="stylesheet" href="<%= ctx %>/css/portal.css">
</head>
<body>
<header class="topbar">
    <div class="brand">Student Profile Portal</div>
    <div class="user">
        Welcome, <strong><%= HtmlUtil.esc((String) session.getAttribute("fullName")) %></strong>
        <a href="<%= ctx %>/logout" class="btn btn-light btn-sm">Logout</a>
    </div>
</header>

<main class="wrap">
    <div class="page-head">
        <h2>Student Profiles</h2>
        <form action="<%= ctx %>/students" method="get" class="search">
            <input type="text" name="q" value="<%= HtmlUtil.esc(q) %>" placeholder="Search by name, roll no or course">
            <button type="submit" class="btn">Search</button>
        </form>
    </div>

    <% if (error != null) { %>
        <div class="alert error"><%= HtmlUtil.esc(error) %></div>

    <% } else if (students == null || students.isEmpty()) { %>
        <p class="empty">No students found<%= (q != null && !q.isEmpty()) ? " for \"" + HtmlUtil.esc(q) + "\"" : "" %>.</p>

    <% } else { %>
        <p class="count">Showing <%= students.size() %> student(s)</p>

        <div class="grid">
        <%  // loop through every student and build one card for each
            for (Student s : students) {
                StudentFile photo = s.getProfilePhoto();
                List<StudentFile> docs = s.getDocuments();
        %>
            <div class="card">
                <% if (photo != null) { %>
                    <!-- the image is not a direct path, it is served by FileServlet -->
                    <img src="<%= ctx %>/file?id=<%= photo.getFileId() %>" class="photo"
                         alt="Photo of <%= HtmlUtil.esc(s.getName()) %>">
                <% } else { %>
                    <div class="avatar"><%= s.getInitials() %></div>
                <% } %>

                <h3><%= HtmlUtil.esc(s.getName()) %></h3>
                <p class="roll"><%= HtmlUtil.esc(s.getRollNo()) %></p>
                <p class="course"><%= HtmlUtil.esc(s.getCourse()) %></p>

                <% if (docs.isEmpty()) { %>
                    <span class="badge warn">No documents</span>
                <% } else { %>
                    <span class="badge ok"><%= docs.size() %> document<%= docs.size() > 1 ? "s" : "" %></span>
                <% } %>

                <a href="<%= ctx %>/profile?id=<%= s.getId() %>" class="btn full">View Profile</a>
            </div>
        <% } %>
        </div>
    <% } %>
</main>
</body>
</html>
