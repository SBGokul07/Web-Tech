<%@ page contentType="text/html; charset=UTF-8" pageEncoding="UTF-8" %>
<%@ page import="com.kpr.portal.util.HtmlUtil" %>
<%
    // message from the servlet (wrong password etc.)
    String error = (String) request.getAttribute("error");
    // message passed in the URL by the filter / logout servlet
    String msg = request.getParameter("msg");
%>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Student Profile Portal</title>
    <link rel="stylesheet" href="<%= request.getContextPath() %>/css/portal.css">
</head>
<body class="login-page">
    <div class="login-box">
        <h1>Student Profile Portal</h1>
        <p class="sub">KPR Institute of Engineering and Technology</p>

        <% if (error != null) { %>
            <div class="alert error"><%= HtmlUtil.esc(error) %></div>
        <% } else if ("required".equals(msg)) { %>
            <div class="alert info">Please login to continue.</div>
        <% } else if ("loggedout".equals(msg)) { %>
            <div class="alert success">You have been logged out successfully.</div>
        <% } %>

        <form action="<%= request.getContextPath() %>/login" method="post">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required autofocus
                   value="<%= HtmlUtil.esc(request.getParameter("username")) %>">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="btn full">Login</button>
        </form>
    </div>
</body>
</html>
