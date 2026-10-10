package com.kpr.portal.dao;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

/**
 * Gives a JDBC connection to the same college_db database used by the PHP admin panel.
 * mysql-connector-j jar must be present inside WEB-INF/lib.
 */
public class DBConnection {

    private static final String URL =
            "jdbc:mysql://localhost:3306/college_db?useSSL=false&serverTimezone=Asia/Kolkata";
    private static final String USER = "root";
    private static final String PASSWORD = "";

    static {
        try {
            // load the driver class once when this class is first used
            Class.forName("com.mysql.cj.jdbc.Driver");
        } catch (ClassNotFoundException e) {
            throw new RuntimeException("MySQL JDBC driver not found. Copy mysql-connector-j.jar to WEB-INF/lib", e);
        }
    }

    private DBConnection() {
        // only static method, so no object needed
    }

    public static Connection getConnection() throws SQLException {
        return DriverManager.getConnection(URL, USER, PASSWORD);
    }
}
