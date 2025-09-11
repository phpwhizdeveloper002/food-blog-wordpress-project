## ⚙️ Installation Steps

### 1. Install Local Server

* Download and install **XAMPP**, **WAMP**, or **MAMP**.
* Start **Apache** and **MySQL** services.

### 2. Clone or Copy Project

* Place the WordPress project folder inside the local server directory:

  * For **XAMPP** → `htdocs/`
  * For **WAMP** → `www/`
  * For **MAMP** → `htdocs/`

Example (XAMPP):

```bash
C:\xampp\htdocs\wordpress
```

### 3. Import Database

* Open **phpMyAdmin**: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
* Create a new database (e.g., `wordpress`)
* Import the provided `.sql` file into this database.

### 4. Configure WordPress

* Open the project folder → find `wp-config.php`.
* Update database credentials:

```php
define( 'DB_NAME', 'wordpress' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', '' ); // keep empty for XAMPP/WAMP, use 'root' for MAMP
define( 'DB_HOST', 'localhost' );
```

### 5. Run the Project

* Visit in your browser:

  ```
  http://localhost/wordpress
  ```

---

## 🚀 Common Issues

* **White Screen / Error Establishing DB Connection**
  → Check `wp-config.php` database details.

* **404 on pages**
  → Go to **Settings → Permalinks** in WordPress admin and click **Save Changes**.

* **Missing styles/scripts**
  → Make sure the project folder name matches the URL path.

---

## 📂 Folder Structure

```
wordpress/
│── wp-admin/
│── wp-content/
│── wp-includes/
│── wp-config.php
│── index.php
│── ...
```

## 🖼️ Screenshot
<img width="1899" height="918" alt="image (24)" src="https://github.com/user-attachments/assets/8a4c5cfe-d819-4c28-93ee-bc729ccab018" />
<img width="1895" height="914" alt="image (23)" src="https://github.com/user-attachments/assets/5a4e042f-8425-44ee-9e1c-5c1fee990707" />
<img width="1893" height="919" alt="image (22)" src="https://github.com/user-attachments/assets/1189136d-cf17-4b14-a200-6a7a20e7ff73" />
<img width="1893" height="919" alt="image (21)" src="https://github.com/user-attachments/assets/96fadb04-b52e-4d83-bdde-ce4e84edacda" />


## 🎯 Done!

Your WordPress project should now be running locally 🎉


