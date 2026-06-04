# 🗄️ ApexPlanet Web Development Workspace — Task 2

A secure, enterprise-grade **Full-Stack CRUD Application** featuring robust multi-user authentication, password encryption pipelines, and relational database integrations. This project fulfills the milestone deliverables for **Task 2** of the Web Development Internship at ApexPlanet Software Pvt Ltd.

---

## 🚀 Application Overview & Features

This system transitions from an isolated runtime configuration into a fully dynamic web platform. It establishes a secure session lifecycle, allowing authenticated users to manage data records seamlessly on a dark glassmorphic dashboard.

### 🔒 Core Capabilities
* **Secure Authentication Engine:** User registration and login interfaces equipped with state validation rules and route protection guard clauses.
* **Cryptographic Password Hashing:** Leverages native PHP `password_hash()` algorithms utilizing random cryptographic salts to prevent raw-text exposure in the database.
* **Self-Service Account Recovery:** An integrated **Forgot Password** gate that authenticates username ownership before safely overriding target security keys.
* **Complete CRUD Pipeline:** Full implementation of data cycles: **Create** (record validation forms), **Read** (tabular data grids), **Update** (pre-populated modification states), and **Delete** (safe row entity drops with verification alerts).
* **Modern Workspace UI:** Uniformly styled with responsive glassmorphism modules powered by a CDN Tailwind CSS layer and high-fidelity FontAwesome iconography.

---

## 🛠️ Technology Stack & Architecture Layering

| Layer | Component | Functional Domain |
| :--- | :--- | :--- |
| **Local Hosting Stack** | WampServer Engine | Provisions local Apache servers and MySQL instances |
| **Backend Processing** | PHP 8.3+ | Handles state variables, prepared statements, and data routing |
| **Database Architecture**| MySQL / InnoDB | Manages data persistence across relational tables |
| **Security Pipeline** | PDO (PHP Data Objects) | Protects application from SQL Injection vulnerabilities |
| **UI Framework** | HTML5 / Tailwind CSS | Renders responsive layouts and dark design system aesthetics |

---

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