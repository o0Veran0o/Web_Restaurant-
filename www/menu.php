<?php
error_reporting(0);
session_start();
if (($_SESSION['username'])==""||$_SESSION['role']!="Manager"){
    header("location:index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant</title>
    <link rel="shortcut icon" href="images/icon.png" type="image/png">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <main>
        <header>
            <img width="80" src="images/icon.png" alt="HTML" class="logo">
            Manager
        </header>
        <nav >
            <ul id="menu">
                <li >
                    <a href="manager_order.php">Orders</a>
                </li>
                <li>
                    <a href="dishes.php">Dishes</a>
                </li>
                <li>
                    <a href="entry.php">Log out</a>
                </li>
            </ul>
        </nav>
        <article>
             <h1>Greetings, <?php echo $_SESSION['username'] ?>! </h1>
                       <h1>Greetings, <?php echo $_SESSION['role'] ?>! </h1>
             <p id="p2">
          In your role, you have the authority and capability to directly influence and edit our database systems. This places you at the forefront of ensuring accuracy, efficiency, and security within our data infrastructure. The ability to manipulate data is a powerful tool, and we trust that you will utilize it judiciously to enhance our operational efficiency.

             </p>
        </article>
        <footer>
        Nabok V.R.
        </footer>
    </main>
</body>
</html>