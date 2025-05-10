# E-Commerce Platform

A fully functional online shopping website built with PHP OOP and SQL that allows customers to browse products, manage their cart, place orders, and use bonus features such as ratings and wishlist.

## Project Structure

```
- /app
  - /controllers (Controller classes for handling requests)
  - /models (Model classes for database operations)
  - /views (View templates for rendering pages)
  - /core (Core classes: Router, Database, Auth, BaseController)
- /public
  - /css (Stylesheets)
  - /js (JavaScript files)
  - /images (Product images and assets)
  - index.php (Main entry point)
- /config
  - database.php (Database connection configuration)
- .htaccess (URL rewriting rules)
- database.sql (Database schema)
```

## Database Schema

### Main Tables:

1. **users**
   - id (Primary Key)
   - name
   - email (unique)
   - password (hashed)
   - role (ENUM: 'customer', 'admin')
   - created_at

2. **products**
   - id (PK)
   - name
   - description
   - price
   - stock
   - image_url
   - created_at

3. **orders**
   - id (PK)
   - user_id (FK to users)
   - total_price
   - status (ENUM: 'pending', 'shipped', 'delivered', 'cancelled')
   - created_at

4. **order_items**
   - id (PK)
   - order_id (FK to orders)
   - product_id (FK to products)
   - quantity
   - price_each

5. **reviews** (Bonus Feature)
   - id (PK)
   - product_id (FK to products)
   - user_id (FK to users)
   - rating (1-5)
   - comment
   - created_at

6. **wishlist** (Bonus Feature)
   - id (PK)
   - user_id (FK to users)
   - product_id (FK to products)

## Setup Instructions

1. Clone the repository to your local XAMPP htdocs folder
2. Import the database.sql file into your MySQL server
3. Configure database connection in config/database.php
4. Access the application at http://localhost/BATU_E_commerce/

## Features

### User Authentication
- User registration and login
- Role-based access control (admin/customer)

### Product Management (Admin)
- Add, edit, and delete products
- View all products

### Shopping Cart and Checkout
- Add products to cart
- View and manage cart
- Checkout process
- Order confirmation

### Order Management
- Customer order history
- Admin order management
- Order status updates

### Bonus Features
- Product reviews and ratings
- Wishlist functionality

## Security Features

- Prepared statements (PDO) to prevent SQL injection
- Input sanitization
- Password hashing
- Session-based authentication
- CSRF protection for forms

## Default Admin Account

- Email: admin@example.com
- Password: admin123