### Members
1. Anthoniete Tumanda
2. Marc Giane A. Baranda
3. Francis Demetri Daa

Your system is designed to manage inventory efficiently by tracking borrowed and returned supplies. It ensures role-based access control, allowing only authorized users to perform certain actions while maintaining security and usability.

User Roles & Permissions
Admin (Full Access)
Can add, edit, and delete supplies, Can mark items as returned, Can export data to CSV for record-keeping,  Has full control over inventory updates,  Can search for specific supplies,  Can view detailed logs of borrowed and returned items

 Standard User (Limited Access)
 Can borrow items from inventory, Can view supplies and their details, Cannot add, edit, or delete items, Cannot mark items as returned

 How the System Works 
 1. Authentication & Role-Based Access
 Users must log in to access the system.  Admins have special permissions, while standard users have restricted access.
 2. Inventory Management
 Admins add new supplies with details like name, quantity, and description. If the same item is added, the system updates its quantity instead of duplicating. 🔍 Users search for items using a dynamic search bar. Admins delete obsolete items when needed.
 3. Borrowing & Returning Items
 Users borrow supplies, and their request is recorded. Admins mark items as returned when they are brought back. AJAX-powered updates ensure real-time tracking of stock levels.
 4. User Experience & Security
 Confirmation modals prevent accidental deletions or updates. Search filters allow quick access to supplies. Role-based restrictions ensure security by preventing unauthorized actions. ✅ Clear Search button resets filtered searches and reloads full inventory.
 5. Data Export & Records
 Admins export inventory data to CSV, making external tracking easier. 📊 Future expansions could include analytics and usage reports.

Security Features & Protections
- User Authentication & Role-Based Access
Login System: Users must authenticate before accessing the system. Session Management: Securely tracks logged-in users, preventing unauthorized access.  Role-Based Restrictions:
- Admins have full control (adding, editing, deleting, marking as returned).
- Standard Users have limited actions (borrowing items but no modification privileges).
- Data Validation & Protection
Prepared Statements for SQL Queries: Prevents SQL injection attacks by safely handling user inputs.  Strict Data Validation: Ensures only valid inputs are processed (e.g., quantity must be positive). Prevent Unauthorized Direct Access:
Sensitive pages like process_add_item.php block direct access from browsers.
Users can’t manipulate URLs to perform unauthorized actions.
- Secure Navigation & Controls
Session Expiry & Logout: Automatically logs out users after inactivity to prevent unauthorized use.  Encrypted Password Storage: (If implemented)—prevents direct access to user credentials.  Restricted URL Access: Blocks unauthorized users from manually entering restricted URLs.
- Protection Against Accidental or Malicious Actions
Confirmation Modals prevent accidental deletions or modifications.  Role-Based Restrictions block unauthorized item modifications.  Error Handling & Logging to track suspicious activity attempts.
- Future Enhancements for Security
Two-Factor Authentication (2FA) for extra login security.  Detailed Access Logs to track changes made by users.  Advanced Encryption for sensitive user data (beyond session security).

How to Use the Inventory System Repository on GitHub
🔹 1. Cloning the Repository
To get started, open Terminal (Mac/Linux) or Command Prompt (Windows) and run:

bash
git clone https://github.com/YOUR-USERNAME/inventory_system.git
Replace YOUR-USERNAME/inventory_system with your actual GitHub repo URL.

🔹 2. Setting Up the Environment
Navigate into the cloned repository:

bash
cd inventory_system
Then, ensure your system has: ✅ PHP 8.x or higher installed ✅ MySQL Database running ✅ Apache Server (or use XAMPP) for local testing

🔹 3. Configuring the Database
1️⃣ Open phpMyAdmin or use MySQL CLI 2️⃣ Create a new database (e.g., inventory_system) 3️⃣ Import the provided SQL dump file (if included in your repo):

sql
SOURCE database.sql;
4️⃣ Open db_connect.php and update the database credentials:

php
$servername = "localhost";
$username = "your_db_user";
$password = "your_db_password";
$dbname = "inventory_system";
🔹 4. Running the Project Locally
✅ If using XAMPP, place the project folder inside htdocs, then navigate to:

http://localhost/inventory_system/supplies.php
✅ If using Apache (without XAMPP), place the project inside /var/www/html/ and restart Apache.

🔹 5. Managing Git Changes
If you modify files and want to push updates:

bash
git add .
git commit -m "Updated inventory tracking"
git push origin main
This keeps your repo up to date with all changes! 🚀

🔹 6. Accessing the Admin Panel & Authentication
Default login page: login.php

Admin users must be registered with the correct role to access inventory controls.

Modify process_login.php to adjust authentication rules if needed.
