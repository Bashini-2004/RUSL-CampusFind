# 🎓 RUSL-CampusFind
> A modern web-based Lost and Found Management System designed for the students, faculty, and administration of **Rajarata University of Sri Lanka (RUSL)**.

---

## 📌 Project Overview
CampusFind provides a centralized, secure digital hub to report lost belongings, list recovered items, search campus-wide reports with rich filters, and securely connect item owners with finders.

### Key Features
- **Item Reporting:** Report both **Lost** and **Found** items with details (name, category, description, campus location, date/time, and photo upload).
- **Search & Filter:** Instantly search through reported items by keywords, category, campus venue/location, and report type (Lost / Found / All).
- **User Authentication:** Student and staff registration, secure login, profile management, and password updates with bcrypt hashing.
- **My Reported Items Dashboard:** Logged-in users can manage, track, resolve (mark as claimed/recovered), or delete their own listings.
- **Admin Control Panel:** Dedicated dashboard for university administrators to monitor system statistics, manage/moderate all item listings, toggle item status, and review contact inquiries.
- **Contact Inquiries:** Campus community members can send inquiries directly to university security/support staff.
- **Responsive & Accessible Design:** Fully responsive layout built with Bootstrap 5, Bootstrap Icons, and customized campus branding.

---

## 🛠️ Technology Stack
- **Backend:** PHP 7.4+ / PHP 8.x (Native PDO, Prepared Statements)
- **Database:** MySQL / MariaDB
- **Frontend:** HTML5, CSS3, JavaScript (ES6), Bootstrap 5.3, Bootstrap Icons
- **Web Server:** Apache (via XAMPP / WAMP / LAMP)

---

## 🚀 Setup & Installation (XAMPP on Windows)

### 1. Place Project in `htdocs`
Ensure this repository folder is placed inside your XAMPP web root directory:
```
C:\xampp\htdocs\RUSL-CampusFind\
```

### 2. Start Apache & MySQL
1. Launch the **XAMPP Control Panel**.
2. Click **Start** for both **Apache** and **MySQL**.

### 3. Import Database
1. Open your browser and go to [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
2. Click the **Import** tab at the top.
3. Click **Choose File** and select `schema.sql` located inside:
   ```
   C:\xampp\htdocs\RUSL-CampusFind\schema.sql
   ```
4. Click **Import** (or **Go**) at the bottom.
   > The script automatically creates the `campusfind_db` database, tables (`users`, `categories`, `items`, `contact_messages`), and sample test data.

### 4. Access the Application
Open your web browser and visit:
```
http://localhost/RUSL-CampusFind/
```

---

## 🔑 Default Accounts & Credentials

### University Administrator Account:
- **Email:** `admin@campusfind.rusl.ac.lk`
- **Password:** `Admin@123`
- **Dashboard URL:** `http://localhost/RUSL-CampusFind/admin.php`

### Sample Student / Staff Accounts:
You can register new student/staff accounts directly at `register.php`, or log in using any registered user credentials.

---

## 📂 Project Structure

```
RUSL-CampusFind/
│
├── config/
│   └── db.php                 # PDO database connection & helper functions
│
├── css/
│   └── style.css              # Custom styling, campus palette, and card styles
│
├── image/                     # Static UI assets and sample item photos
│   ├── logo.png
│   ├── phone.jpeg
│   ├── keys.jpg
│   └── ...
│
├── includes/
│   ├── header.php             # Global navigation bar and session state
│   └── footer.php             # Global footer and JavaScript scripts
│
├── uploads/                   # Storage directory for user-uploaded item photos
│
├── about.php                  # About RUSL-CampusFind & project background
├── admin.php                  # Administrator dashboard & moderation portal
├── contact.php                # Contact inquiry form for students & staff
├── contact_process.php        # Backend handler for contact messages
├── index.php                  # Homepage with hero section & recent items
├── login.php                  # User & administrator login form
├── login_process.php          # Authentication processing & session initiation
├── logout.php                 # Session termination & sign-out
├── myitems.php                # User's personal reported items dashboard
├── profile.php                # User profile information page
├── register.php               # New user registration form
├── register_process.php       # Account creation processor
├── report.php                 # Item reporting form (Lost / Found)
├── report_process.php         # Report processor & secure file upload handler
├── schema.sql                 # Complete database schema and seed data
├── script.js                  # Client-side form validation & live image preview
└── search.php                 # Search and filter lost & found items
```

---

## 🛡️ Security Features
- **SQL Injection Prevention:** 100% prepared PDO statements with parameterized inputs.
- **Password Protection:** Passwords securely hashed with `PASSWORD_DEFAULT` (bcrypt).
- **File Upload Security:** Strict validation for image extensions (`.jpg`, `.jpeg`, `.png`, `.webp`), MIME-type verification, and maximum 5MB size limit.
- **XSS Mitigation:** Output sanitization using `htmlspecialchars()` across all user-rendered content.
- **Access Control:** Role-based access control protecting administrative routes and private user dashboards.

---

## 📝 License
This project is open-source under the MIT / Apache License. Developed for **Faculty of Technology, Rajarata University of Sri Lanka**.
