<%@ page contentType="text/html; charset=UTF-8" pageEncoding="UTF-8" %>
<%@ page import="java.util.List, java.text.SimpleDateFormat" %>
<%@ page import="com.kpr.portal.model.Student, com.kpr.portal.model.StudentFile, com.kpr.portal.util.HtmlUtil" %>
<%
    Student s = (Student) request.getAttribute("student");
    StudentFile photo = s.getProfilePhoto();
    List<StudentFile> docs = s.getDocuments();
    SimpleDateFormat fmt = new SimpleDateFormat("dd-MM-yyyy hh:mm a");
    String ctx = request.getContextPath();
%>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><%= HtmlUtil.esc(s.getName()) %> | Student Profile Portal</title>
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
    <a href="<%= ctx %>/students" class="back">&larr; Back to all students</a>

    <section class="profile card">
        <div>
            <% if (photo != null) { %>
                <img src="<%= ctx %>/file?id=<%= photo.getFileId() %>" class="photo big" alt="Profile photo">
            <% } else { %>
                <div class="avatar big"><%= s.getInitials() %></div>
            <% } %>
        </div>
        <div class="info">
            <h2><%= HtmlUtil.esc(s.getName()) %></h2>
            <table class="details">
                <tr><th>Roll Number</th><td><%= HtmlUtil.esc(s.getRollNo()) %></td></tr>
                <tr><th>Course</th><td><%= HtmlUtil.esc(s.getCourse()) %></td></tr>
                <tr><th>Email</th><td><%= HtmlUtil.esc(s.getEmail()) %></td></tr>
                <tr><th>Phone</th>
                    <td><%= (s.getPhone() == null || s.getPhone().isEmpty()) ? "Not given" : HtmlUtil.esc(s.getPhone()) %></td></tr>
                <tr><th>Registered On</th><td><%= fmt.format(s.getCreatedAt()) %></td></tr>
            </table>
        </div>
    </section>

    <section class="card">
        <h3>Documents (<%= docs.size() %>)</h3>

        <% if (docs.isEmpty()) { %>
            <p class="empty">This student has not uploaded any documents yet.</p>
        <% } else { %>
            <div class="doc-grid">
            <% for (StudentFile d : docs) { %>
                <div class="doc">
                    <% if (d.isImage()) { %>
                        <!-- image documents are shown as thumbnails -->
                        <img src="<%= ctx %>/file?id=<%= d.getFileId() %>" alt="<%= HtmlUtil.esc(d.getOriginalName()) %>">
                    <% } else if (d.isPdf()) { %>
                        <!-- PDFs get a simple icon, they open in a new tab when clicked -->
                        <a href="<%= ctx %>/file?id=<%= d.getFileId() %>" target="_blank" class="file-icon">PDF</a>
                    <% } else { %>
                        <div class="file-icon">FILE</div>
                    <% } %>
                    <p class="doc-title"><%= d.getCategoryLabel() %></p>
                    <p class="doc-meta"><%= HtmlUtil.esc(d.getOriginalName()) %><br>
                        <%= d.getReadableSize() %> &middot; <%= fmt.format(d.getUploadDate()) %></p>
                    <a href="<%= ctx %>/file?id=<%= d.getFileId() %>" target="_blank" class="btn btn-sm">Open</a>
                    <a href="<%= ctx %>/file?id=<%= d.getFileId() %>&amp;download=1" class="btn btn-sm btn-light">Download</a>
                </div>
            <% } %>
            </div>
        <% } %>
    </section>
</main>
</body>
</html>
