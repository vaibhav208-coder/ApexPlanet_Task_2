# 🗄️ ApexPlanet Web Development Workspace — Task 2
# Full-Stack CRUD Application (PHP & MySQL)

This is a secure Full-Stack CRUD (Create, Read, Update, Delete) application built as part of Task 2 for my Web Development Internship at ApexPlanet. 

The project includes a complete user authentication system and allows logged-in users to manage text posts on a personalized dashboard.

## 🚀 Features
* **User Authentication:** Registration, Login, and Logout functionality.
* **Password Security:** Passwords are encrypted using PHP's native `password_hash()`.
* **Forgot Password:** Users can reset their password securely if forgotten.
* **CRUD Operations:** Authenticated users can create, read, edit, and delete posts.
* **Security First:** Uses PDO (PHP Data Objects) with prepared statements to prevent SQL Injection attacks.
* **UI/UX:** Clean, responsive dark-mode interface styled entirely with Tailwind CSS.

## 💻 Technologies Used
* **Frontend:** HTML5, Tailwind CSS
* **Backend:** PHP 8.x
* **Database:** MySQL
* **Environment:** Apache (WampServer)

## ⚙️ How to Run Locally

1. **Clone the repository:**
   Place this folder inside your local server directory (e.g., `C:\wamp64\www\ApexPlanet_Task_2`).

2. **Set up the Database:**
   * Open phpMyAdmin (`http://localhost/phpmyadmin`).
   * Create a database named `blog`.
   * Run the following SQL commands to create the required tables:

```sql
   CREATE TABLE users (
       id INT AUTO_INCREMENT PRIMARY KEY,
       username VARCHAR(50) NOT NULL UNIQUE,
       password VARCHAR(255) NOT NULL,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );

   CREATE TABLE posts (
       id INT AUTO_INCREMENT PRIMARY KEY,
       title VARCHAR(255) NOT NULL,
       content TEXT NOT NULL,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );

## 📂 Modular File Architecture
```bash
ApexPlanet_Task_2/
└── README.md             # System manual and architecture specifications
├── config.php            # Central PDO database connection abstraction
├── register.php          # User registration panel with uniqueness validations
├── login.php             # Session token generator and secure entry gate
├── forgot-password.php   # Password override module and account recovery tool
├── dashboard.php         # Core layout rendering the dynamic MySQL post feed (Read)
├── create.php            # Secure data injection interface (Create)
├── edit.php              # Pre-populated record modification terminal (Update)
├── delete.php            # Silent background record-dropping process (Delete)
├── logout.php            # Total token clearance and session destruction logic