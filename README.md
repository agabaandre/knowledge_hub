
# Installation Guide for Africa CDC Knowledge Hub

This guide covers the setup process for the Africa CDC Knowledge Hub on both Windows and Linux environments.

---

## 1. Prerequisites

Ensure you have the following installed:

- Operating System: Windows, Linux, or macOS
- Web Server: Apache or Nginx
- Database: MySQL or MariaDB
- PHP Version: 8.x or later
- Node.js Version: 16.x or later
- Composer: PHP dependency manager
- Git: Version control
- Redis (Optional but recommended for caching)

---

## 2. Windows Installation

### 2.1 Install WAMP/XAMPP

1. To set up PHP, MySQL, and Apache, download and install WAMP or XAMPP:
   - [Download XAMPP](https://www.apachefriends.org/download.html)
   - [Download WAMP](http://www.wampserver.com/en/)

2. Follow the installation instructions for either WAMP or XAMPP.

### 2.2 Install Composer

1. Download and install Composer from [here](https://getcomposer.org/download/) to manage PHP dependencies.

### 2.3 Install Node.js

1. Download and install Node.js from [here](https://nodejs.org/) for JavaScript dependency management.

---

## 3. Linux Installation

### 3.1 Install LAMP Stack (Linux)

To install Apache, MySQL, and PHP on a Linux (Ubuntu) system, follow these steps:

```bash
sudo apt update
sudo apt install apache2
sudo apt install mysql-server
sudo apt install php libapache2-mod-php php-mysql
```

### 3.2 Install Composer

Install Composer on Linux:

```bash
sudo apt install composer
```

### 3.3 Install Node.js

Install Node.js and npm:

```bash
sudo apt install nodejs
sudo apt install npm
```

---

## 4. Clone the Knowledge Hub Repository

Clone the Knowledge Hub repository from GitHub into your web server directory:

```bash
git clone https://github.com/Africa-cdc-Khub/knowledge_hub.git
```

For Windows:
- Extract the project into your XAMPP or WAMP htdocs folder:
  ```bash
  C:/xampp/htdocs/knowledge_hub
  ```

For Linux:
- Place the folder in the Apache web root directory:
  ```bash
  /var/www/html/knowledge_hub
  ```

---

## 5. Create Environment Configuration (.env)

1. Inside the project directory, create an .env file by copying the example file:
   ```bash
   cp .env.example .env
   ```

2. Open the .env file and update the following variables with your database details:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=khub
DB_USERNAME=root
DB_PASSWORD=
```

3. Configure other necessary environment settings:

```env
STATES_ENABLED=TRUE
ADMIN_UNITS_ENABLED=FALSE
APP_URL=http://localhost/knowledge_hub
```

---

## 6. Create and Import Database

### Windows (phpMyAdmin):

1. Open phpMyAdmin and create a new database named `khub`.
2. Import the starter SQL file via the phpMyAdmin interface.

### Linux (MySQL CLI):

1. Create the database and import the starter SQL file using the following command:

```bash
mysql -u root -p khub < path_to_starter_db.sql
```

---

## 7. Install Dependencies

### 7.1 PHP Dependencies

Install PHP dependencies using Composer:

```bash
composer install --ignore-platform-reqs
```

### 7.2 JavaScript Dependencies

Install JavaScript dependencies using npm:

```bash
npm install
```

---

## 8. Run Database Migrations and Seeders

Run the following commands to set up the database schema and populate it with initial data:

### 8.1 Migrate the database:

```bash
php artisan migrate
```

### 8.2 Seed the database:

```bash
php artisan db:seed
```

### 8.3 Create symbolic links for file storage:

```bash
php artisan storage:link
```

---

## 9. Set Folder Permissions (Linux)

For Linux, ensure the correct folder permissions:

1. Set ownership for the project directory:
   ```bash
   sudo chown -R $USER:$USER /var/www/html/knowledge_hub
   ```

2. Set the correct permissions for the project:
   ```bash
   sudo chmod -R 755 /var/www/html/knowledge_hub
   sudo chmod -R 777 storage public
   ```

---

## 10. Start the Application

### For Windows (XAMPP/WAMP):

1. Open the XAMPP Control Panel and start Apache and MySQL.

2. Access the application in your browser:

   ```bash
   http://localhost/knowledge_hub
   ```

### For Linux:

1. Start Apache:

   ```bash
   sudo service apache2 start
   ```

2. Access the application in your browser:

   ```bash
   http://{server_ip}/knowledge_hub
   ```

---

## Troubleshooting

### 1. 404 Not Found Errors:
Ensure that .htaccess files are enabled for Apache and that your virtual host configuration allows for URL rewrites.

### 2. Database Connection Issues:
Verify that the database credentials in the .env file are correct and that the MySQL/MariaDB server is running.

### 3. Permission Issues (Linux):
If you encounter permission errors, ensure the storage and public directories are writable by the web server:

```bash
sudo chmod -R 777 storage public
```

---

## Conclusion

You have successfully installed the Africa CDC Knowledge Hub. Refer to the Developer Guide for instructions on maintaining and extending the platform.
