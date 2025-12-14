# SatayTech

A web-based medical inventory management system designed for independent pharmacies and clinics. Streamlines operations by digitizing manual records, enabling real-time stock tracking, expiry monitoring, and report generation.

## **📋 Prerequisites**

Ensure you have the following installed on your machine:

- **PHP 8.0+** (with php-mysql extension enabled)
- **MySQL 5.7+** (or MariaDB)
- **Git** (optional, for cloning)

## **⚙️ Configuration**

### **Database Credentials**

Before running the app, you must ensure the code knows your database username and password.

1. Open **src/install.php**.
2. Open **src/config/db.php**.
3. Update the $username and $password variables in **BOTH** files to match your setup.

#### **For XAMPP Users**

- **Username:** root
- **Password:** (leave empty)
- _You usually don't need to change anything unless you set a custom password._

#### **For Linux/Mac (Native MySQL) Users**

You likely need to create a user, as root is locked down. Run this in your terminal:

```bash
sudo mysql -u root
```

Then runs this SQL:

```sql
CREATE USER 'admin'@'localhost' IDENTIFIED BY 'password123';
GRANT ALL PRIVILEGES ON *.* TO 'admin'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Finally, update your PHP files to use `$username = 'admin'` and `$password = 'password123'`.

## **🚀 Installation & Setup**

### **Step 1: Start the Database Server**

#### **Option A: Using XAMPP (Windows/Linux)**

1. Open **XAMPP Control Panel**.
2. Click **Start** next to **MySQL**.
3. Click **Start** next to **Apache** (if using XAMPP to serve files).

#### **Option B: Using Native MySQL (Linux CLI)**

1. Open your terminal.
2. Start the service:
   ```
   sudo systemctl start mysql
   ```

### **Step 2: Serve the Source Code**

You can serve the project using XAMPP's Apache or PHP's built-in server.

#### **Option A: Using XAMPP (htdocs)**

1. Copy the entire **SatayTech** folder.
2. Paste it into your XAMPP installation folder: `C:\\xampp\\htdocs\\` (or `/opt/lampp/htdocs/`).
3. The path should look like: `.../htdocs/SatayTech/src/`.

#### **Option B: Using PHP CLI**

1. Open your terminal/command prompt.
2. Navigate to the source directory:
   ```bash
   cd SatayTech/src/
   ```
3. Start the server:
   ```bash
   php -S localhost:3000
   ```

### **Step 3: Run the Database Installer**

Now you need to run the install.php script to create the database and tables. The URL depends on how you started the server in Step 2\.

- If you used XAMPP:  
  Open browser and visit:  
  👉 **http://localhost/SatayTech/src/install.php**
- If you used PHP CLI:  
  Open browser and visit:  
  👉 **http://localhost:3000/install.php**

**Note:** You will see a success message indicating the satay-tech-db has been created.

### **Step 4: Access the Application**

Once installed, you can access the Public Storefront and Admin Panel.

#### **If using XAMPP:**

- **Public Store:** http://localhost/SatayTech/src/public/
- **Admin Panel:** http://localhost/SatayTech/src/public/admin/

#### **If using PHP CLI:**

- **Public Store:** http://localhost:3000/public/
- **Admin Panel:** http://localhost:3000/public/admin/

## **🔑 Default Login Credentials**

The installer seeds the database with these default accounts:  
**Administrator (Inventory System):**

- **Email:** admin@sys.com
- **Password:** Pass123\!

**Member (Customer System):**

- **Email:** jeremy@gmail.com
- **Password:** User123\!

## **📂 Project Structure**

```
/SatayTech
└── /src            # Root of the application source
 ├── config/db.php  # Database connection file
 ├── includes/      # Backend logic (Auth, CRUD, Helpers)
 ├── public/        # Web root (CSS, JS, View Files)
 │ ├── admin/       # Admin-specific pages
 │ └── assets/      # Images and Styles
 ├── sql/schema.sql # Database structure definition
 └── install.php    # Database setup script
```
