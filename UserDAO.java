package com.kpr.portal.dao;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;

/** Checks the login details against the portal_users table. */
public class UserDAO {

    /**
     * Returns the full name of the user if username and password are correct, otherwise null.
     * Password is never stored in plain text, MySQL's SHA2() hashes the typed password
     * and it is compared with the stored hash.
     */
    public String validateUser(String username, String password) throws SQLException {
        String sql = "SELECT full_name FROM portal_users WHERE username = ? AND password_hash = SHA2(?, 256)";

        try (Connection con = DBConnection.getConnection();
             PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setString(1, username);
            ps.setString(2, password);
            try (ResultSet rs = ps.executeQuery()) {
                return rs.next() ? rs.getString("full_name") : null;
            }
        }
    }
}
