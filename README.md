# ComputerHub - E-Commerce Website for Computer Parts & Accessories

ComputerHub is a responsive, modern e-commerce web application designed for selling computer components, peripherals, and laptops. This project was developed as a Year 1 Semester 1 web development coursework assignment.

---

## 🚀 Features

- **Dynamic Navigation Bar**: Tracks and displays the logged-in user's status, custom welcome message, logout button, and shopping cart badge.
- **Dynamic Shopping Cart**: Users can log in, add products to their cart from various category pages using AJAX, view their cart list, calculate total amounts, remove products, and place orders.
- **Live Search Filter**: Category pages feature a real-time client-side search filter that allows users to instantly find products by name or subcategory without reloading the page.
- **Clean Structure**: The codebase uses HTML5 custom data attributes (`data-product-id`) to link frontend product cards to backend MySQL rows efficiently.
- **Secure Backend**: Implements secure PHP sessions and PHP MySQLi prepared statements to prevent SQL Injection and manage user states.

---

## 🛠️ Tech Stack

- **Frontend**: HTML5, Vanilla CSS3 (Custom Grids, Flexbox, Transitions), Vanilla Javascript (DOM, Fetch API).
- **Backend**: PHP (Session handling, Asynchronous JSON responses).
- **Database**: MySQL (Relational schema with cascade deletes).

---

## 📦 Installation & Setup

Follow these steps to run the project locally on your machine using **WampServer**:

### 1. Copy Project to WWW Directory
Move or clone this project folder into your Wamp64 www folder:
`C:\wamp64\www\UpdatedComputerhub`

### 2. Set Up the Database
1. Open your browser and navigate to **phpMyAdmin**: `http://localhost/phpmyadmin`
2. Create a new database named `computerhub`.
3. Select the `computerhub` database, click on the **Import** tab.
4. Choose the file located at: `NewBackend/database/computerhub_complete.sql`
5. Click **Import** (or **Go**) at the bottom. This will create all the necessary tables (`users`, `products`, `cart_items`, `orders`, `order_items`) and populate them with products and test users.

### 3. Open the Website
Ensure WampServer is running, then open your browser and navigate to:
`http://localhost/UpdatedComputerhub/index.html`

---

## 🔑 Test Credentials

The database script automatically inserts default accounts for testing:

### 👤 Regular User Account
- **Email**: `user@example.com`
- **Password**: `user123`

### 👤 Test User Account
- **Email**: `test@example.com`
- **Password**: `test123`

### ⚙️ Admin Account
- **Email**: `admin@computerhub.com`
- **Password**: `admin123`

---

## 📂 Project Structure

```text
├── NewBackend/
│   ├── actions/          # PHP actions (add/remove from cart, place order, logout)
│   ├── auth/             # Authentication handling (login, signup)
│   ├── database/         # Database connection and SQL setup files
│   └── check_login.php   # Session checker JSON endpoint
├── Detailed Pages/       # Detailed specification pages for individual laptops/parts
├── Images/               # Images and assets used across categories
├── script.js             # Simplified client-side interactive logic (AJAX, Search filter, Navbar)
├── Home.js               # Slideshow animation script for Home Page
├── Cart.php              # Shopping cart dashboard
├── Categories.html       # Overview of main shopping departments
├── index.html        # Main landing page
└── .gitignore            # Git rules file to exclude unnecessary files
```
