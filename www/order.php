<?php
error_reporting(0);
session_start();
if (($_SESSION['username'])==""){
    header("location:index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/icon.png" type="image/png">
    <link rel="stylesheet" href="css/order_style.css">
    <title>Order Page</title>
</head>
<body>
    <a href="index.php"><img src="images/arrow.png" id="f3" alt="HTML"></a>
    <main>
        <header>Order</header>
                <div class="pagination">
                <button id="prevPageBtn">Previous Page</button>
                <button id="nextPageBtn">Next Page</button>
            </div>
        <div id="menu"></div>
        <div class="order_style">
            <h2>Your Order</h2>
            <div id="order"></div>
            <p id="totalSum" class="sum">Total Sum: 0 Kč</p>
            <button id="placeOrderBtn">Place Order</button>

            <!-- Pagination Buttons -->
    
        </div>

        <footer>
            Nabok V.R.
        </footer>
    </main>
    <script src="ord_script.js"></script>
</body>
</html>
