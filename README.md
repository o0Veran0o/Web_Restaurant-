# Restaurant Website

## Description

This project is a dynamic restaurant website built using HTML, PHP, CSS, and JavaScript, incorporating AJAX for enhanced user experience.  It utilizes a database for storing information about dishes, users, and orders, and implements secure session management with PHP. The website offers different functionalities for regular users and managers, providing a comprehensive platform for online food ordering and restaurant management.

## Features

* **Dynamic Menu Display:**  The website dynamically fetches and displays menu items from the database, categorized for easy navigation.  PHP code retrieves data from the `Dishes` table and generates HTML to present the dishes, their details (name, weight, cost, recipe), and "Add to Order" buttons.  Pagination is implemented to handle large menus efficiently, as demonstrated in the `index.php` file:

   ```php
   $itemsPerPage = 5; // Number of items per page
   $page = isset($_GET['page']) ? $_GET['page'] : 1;
   $offset = ($page - 1) * $itemsPerPage;
   $sql .= " LIMIT ? OFFSET ?"; // Add limit and offset for pagination



* **Secure User Authentication:** The website features a secure login system that protects against SQL injection by using prepared statements. User credentials are verified against the Employees table in the database. Passwords are securely hashed using password_verify() or are checked in plain text(which isn't secure) , as shown in the entry.php file:

```php
$query = "SELECT * FROM Employees WHERE (email = ? OR phone_number = ?)";
$stmt = mysqli_prepare($connect, $query);  // Prepared statement

if (password_verify($enteredPassword, $hashedPasswordFromDatabase)|| $enteredPassword == $hashedPasswordFromDatabase) { // Secure password check
// ... login logic ...
```

* **Role-Based Access Control:** Different user roles (customer and Manager) have distinct permissions. Customers can view the menu, place orders, and manage their account information. Managers have additional privileges to modify database content, including the menu, user details, and other restaurant data. This is implemented using session variables in PHP:

```php
if ($row['role'] === 'Manager') {
    $_SESSION['role'] = 'Manager';
    header("location: menu.php"); // Redirect to manager page
} else {
   $_SESSION['role'] = 'customer';
   header("location: index.php"); // Redirect to customer page
}
```

* **AJAX-Powered Ordering System:** The ordering system uses AJAX to provide a seamless user experience. Users can add dishes to their order dynamically without page reloads. The ord_script.js file demonstrates how AJAX requests are made to order_conection.php to fetch and display menu items:

```js
fetch('order_conection.php')
    .then(response => response.json())
    .then(data => displayMenu(data))
    // ...
```


* **Order Management:** Users can view their order, add or remove items, and see the total cost updated in real time. The ord_script.js file handles this functionality. The order is then sent to the server for processing using another AJAX call, enhancing the user experience with dynamic updates.

* **Session Management:** PHP sessions are used to store user information and maintain login status. Session security is enhanced by setting appropriate session parameters, like session.gc_probability, as seen in multiple PHP files.

* **Database Interaction:** The project uses PHP to interact with a database (presumably MySQL). Prepared statements are used to prevent SQL injection vulnerabilities, ensuring data security.

Technologies Used
- HTML: Structure and content of the web pages.
- PHP: Server-side scripting for dynamic content generation, database interaction, and session management.
- CSS: Styling and visual presentation.
- JavaScript: Client-side scripting for interactivity, AJAX functionality, and DOM manipulation.
- AJAX: Asynchronous communication with the server for dynamic updates.
- Database (MySQL): Data storage and retrieval.
- Security
- SQL Injection Prevention: Prepared statements are used throughout the PHP code to protect against SQL injection.
- Future Enhancements (Suggestions)
- Input Validation: Implement more robust client-side and server-side input validation to prevent potential security issues and improve data integrity.
- Error Handling: Enhance error handling to provide more informative messages to users and log errors for debugging.
- Modern JavaScript Frameworks: Consider using a modern JavaScript framework (like React, Vue, or Angular) to improve front-end development and maintainability.
