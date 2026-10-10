package com.kpr.portal.servlet;

import com.kpr.portal.dao.StudentDAO;
import com.kpr.portal.model.StudentFile;

import jakarta.servlet.ServletException;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.URLEncoder;
import java.nio.charset.StandardCharsets;
import java.sql.SQLException;

/**
 * Streams an uploaded image / document to the browser.
 * Usage in HTML:  <img src="file?id=12">   or   <a href="file?id=15&download=1">
 * Only the file id comes from the browser, the real path is taken from the database,
 * so nobody can read other files on the server by changing the URL.
 */
public class FileServlet extends HttpServlet {

    private final StudentDAO studentDAO = new StudentDAO();
    private File uploadFolder;

    @Override
    public void init() throws ServletException {
        // folder path comes from web.xml (context-param "uploadDirectory")
        String dir = getServletContext().getInitParameter("uploadDirectory");
        if (dir == null) {
            throw new ServletException("uploadDirectory is not set in web.xml");
        }
        uploadFolder = new File(dir);
    }

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        int fileId;
        try {
            fileId = Integer.parseInt(request.getParameter("id"));
        } catch (NumberFormatException e) {
            response.sendError(HttpServletResponse.SC_BAD_REQUEST, "Invalid file id");
            return;
        }

        StudentFile meta;
        try {
            meta = studentDAO.getFileById(fileId);
        } catch (SQLException e) {
            log("DB error while reading file " + fileId, e);
            response.sendError(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
            return;
        }

        if (meta == null) {
            response.sendError(HttpServletResponse.SC_NOT_FOUND, "File record not found");
            return;
        }

        File file = new File(uploadFolder, meta.getFilePath());

        // extra safety: the final path must still be inside the uploads folder
        if (!file.getCanonicalPath().startsWith(uploadFolder.getCanonicalPath() + File.separator)) {
            response.sendError(HttpServletResponse.SC_FORBIDDEN);
            return;
        }
        if (!file.isFile()) {
            response.sendError(HttpServletResponse.SC_NOT_FOUND, "File missing on server");
            return;
        }

        // tell the browser what is coming
        response.setContentType(meta.getFileType());
        response.setContentLengthLong(file.length());

        boolean download = "1".equals(request.getParameter("download"));
        String encodedName = URLEncoder.encode(meta.getOriginalName(), StandardCharsets.UTF_8).replace("+", "%20");
        response.setHeader("Content-Disposition",
                (download ? "attachment" : "inline") + "; filename*=UTF-8''" + encodedName);

        // copy the file to the response in small chunks (does not load the whole file in memory)
        try (InputStream in = new FileInputStream(file);
             OutputStream out = response.getOutputStream()) {
            byte[] buffer = new byte[8192];
            int bytesRead;
            while ((bytesRead = in.read(buffer)) != -1) {
                out.write(buffer, 0, bytesRead);
            }
        }
    }
}
