<%-- index.jsp - opening the project URL directly takes the user to the login servlet --%>
<% response.sendRedirect(request.getContextPath() + "/login"); %>
