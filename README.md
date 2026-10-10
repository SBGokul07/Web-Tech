# Web Technology Assignment II (U21CS501)

Student admission system in three parts, all sharing one MySQL database (`college_db`).

| Folder | Question | Technology |
|---|---|---|
| `q1_registration/` | Q1 - Student registration form with validation | HTML, CSS, JavaScript |
| `q2_admin_php/` | Q2 - Admin panel: CRUD + file upload + metadata | PHP, MySQL |
| `q3_servlet_portal/` | Q3 - Student profile portal with login | Java Servlet, JSP, JDBC |
| `database/` | DB schema script | MySQL |

## How to run

1. Start Apache and MySQL in XAMPP. Import `database/college_db.sql` in phpMyAdmin.
2. **Q1:** open `q1_registration/register.html` in a browser.
3. **Q2:** copy `q2_admin_php` to `C:\xampp\htdocs\student_admin` and open `http://localhost/student_admin/`.
   Set `upload_max_filesize = 10M` and `post_max_size = 12M` in `php.ini` if uploads fail.
4. **Q3:** create a Dynamic Web Project `StudentPortal` in Eclipse (Tomcat 10.1), copy `src/main/java` and
   `src/main/webapp`, put `mysql-connector-j-*.jar` in `WEB-INF/lib`, set `uploadDirectory` in `web.xml`
   to the PHP uploads folder, then run on server and open `http://localhost:8080/StudentPortal/`.
   Login: `admin` / `Admin@123`.

For Tomcat 9, change `jakarta.servlet` imports to `javax.servlet`.
