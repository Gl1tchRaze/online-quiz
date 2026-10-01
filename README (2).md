# Online Quiz System

A web-based online quiz application built with **PHP**. It provides separate areas for **administrators** and **students**: admins manage quizzes, while students register, log in, and take them.

---

## Features

- **User Registration and Login**: students can create an account and sign in
- **Role-Based Access**: separate `admin` and `student` sections
- **Admin Panel**: manage quiz content and monitor students
- **Student Panel**: attempt quizzes and view results
- **Session-Based Authentication**: login and logout handling
- **Centralized Configuration**: database settings kept in a dedicated `config` folder

---

## Project Structure

```
online-quiz/
├── admin/          # Admin dashboard and management pages
├── student/        # Student dashboard and quiz pages
├── config/         # Configuration files (e.g., database connection)
├── login.php       # User login page
├── logout.php      # Ends the user session
└── register.php    # New user registration page
```

| Path | Purpose |
|---|---|
| `admin/` | Pages available to administrators only |
| `student/` | Pages available to logged-in students |
| `config/` | Database connection and other settings |
| `login.php` | Authenticates users and redirects by role |
| `register.php` | Creates new user accounts |
| `logout.php` | Destroys the session and logs the user out |

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP |
| Database | MySQL |
| Frontend | HTML, CSS |
| Local Server | XAMPP / WAMP |

---

## Getting Started

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) (or any PHP + MySQL server stack)
- A web browser

### Installation

1. **Clone the repository** into your server's web folder:

   ```bash
   cd C:/xampp/htdocs
   git clone https://github.com/Gl1tchRaze/online-quiz.git
   ```

2. **Start Apache and MySQL** from the XAMPP Control Panel.

3. **Create the database** in phpMyAdmin (`http://localhost/phpmyadmin`) and import the project's SQL file if available.

4. **Configure the connection** by editing the database settings inside the `config/` folder (host, username, password, database name).

5. **Open the app** in your browser:

   ```
   http://localhost/online-quiz/login.php
   ```

---

## Usage

| Role | Steps |
|---|---|
| Student | Register at `register.php` → log in → open the student panel → take a quiz |
| Admin | Log in with an admin account → open the admin panel → manage quizzes |

---

## Security Notes

- Never commit real database passwords to a public repository
- Use prepared statements and password hashing (`password_hash`) for safe authentication
- Change any default admin credentials before deploying

---

## Future Improvements

- Timer for each quiz
- Result history and leaderboard
- Question randomization
- Export results to CSV or PDF
- Responsive, mobile-friendly design

---

## Author

**Gl1tchRaze**

---

## License

This project is for educational purposes.
