<?php
error_reporting(0);
session_start();
if (($_SESSION['username']) == "" || $_SESSION['role'] != "Manager") {
    header("location:index.php");
}

require_once 'include/db.php';

$itemsPerPage = 2;
$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($currentPage - 1) * $itemsPerPage;



/**
 * Delete Order Function.
 *
 * Deletes an order and its related records from the database.
 *
 * @param string $deleteOrderId The ID of the order to be deleted.
 * @return bool True if the order is successfully deleted, False if there is an error.
 */
function deleteOrder($deleteOrderId) {
    global $connect;

    $deleteOrderQuery = "DELETE FROM `Order` WHERE `id` = '$deleteOrderId'";
    
    if (mysqli_query($connect, $deleteOrderQuery)) {
        $deleteOrderCompQuery = "DELETE FROM `Order_comp` WHERE `order_id` = '$deleteOrderId'";
        mysqli_query($connect, $deleteOrderCompQuery);
        return true;
    } else {
      $var= 'Error deleting order: ' . mysqli_error($connect);
        return false;
    }
}

/**
 * Change Status Function.
 *
 * Toggles the status of an order between 'finished' and 'not-finished'.
 *
 * @param string $changeStatusOrderId The ID of the order for which the status should be changed.
 */
function changeOrderStatus($changeStatusOrderId) {
    global $connect;

    $currentStatusQuery = "SELECT `status` FROM `Order` WHERE `id` = '$changeStatusOrderId'";
    $currentStatusResult = mysqli_query($connect, $currentStatusQuery);

    if ($currentStatusResult) {
        $currentStatus = mysqli_fetch_assoc($currentStatusResult)['status'];
        $newStatus = ($currentStatus == 'finished') ? 'not-finished' : 'finished';

        $updateStatusQuery = "UPDATE `Order` SET `status` = '$newStatus' WHERE `id` = '$changeStatusOrderId'";
        
        if (!mysqli_query($connect, $updateStatusQuery)) {
             $var= 'Error updating status: ' . mysqli_error($connect);
        }
    } else {
         $var='Error fetching current status: ' . mysqli_error($connect);
    }
}

if (isset($_GET['delete_order_id'])) {
    $deleteOrderId = mysqli_real_escape_string($connect, $_GET['delete_order_id']);

    deleteOrder($deleteOrderId);
 
}

// Handle status change
if (isset($_GET['change_status_id'])) {
    $changeStatusOrderId = mysqli_real_escape_string($connect, $_GET['change_status_id']);
    changeOrderStatus($changeStatusOrderId);
}

// Fetch all orders
$totalOrdersQuery = "SELECT COUNT(*) AS total FROM `Order`";
$totalOrdersResult = mysqli_query($connect, $totalOrdersQuery);
$totalOrders = mysqli_fetch_assoc($totalOrdersResult)['total'];

// Calculate total number of pages
$totalPages = ceil($totalOrders / $itemsPerPage);

// Fetch orders for the current page
$fetchOrdersQuery = "SELECT `Order`.`id`, `Employees`.`email` AS `customer_email`, `Order`.`status`
                    FROM `Order`
                    INNER JOIN `Employees` ON `Order`.`customer_id` = `Employees`.`employes_id_serial`
                    LIMIT $itemsPerPage OFFSET $offset";
$ordersResult = mysqli_query($connect, $fetchOrdersQuery);

// Display orders
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/manager_edit.css">
    <title>Order Management</title>
</head>

<body>
    <?php
    echo "<p> $var </p>";
    ?>
    <a href="menu.php"><img src="images/arrow.png" id="f3" alt="HTML"></a>
    <main>
        <header>Order Management</header>

        <table  class="p1">
            <tr>
                <th>Order ID</th>
                <th>Customer email</th>
                <th>Status</th>
                <th>Dishes</th>
                <th>Action</th>
            </tr>

            <?php
            while ($order = mysqli_fetch_assoc($ordersResult)) {
                echo '<tr>';
                echo '<td>' . $order['id'] . '</td>';
                echo '<td>' . $order['customer_email'] . '</td>';
                echo '<td>' . $order['status'] . '</td>';

                $fetchDish = "SELECT `Dishes`.`name`
                    FROM `Dishes`
                    INNER JOIN `Order_comp` ON `Order_comp`.`dish_id` = `Dishes`.`dishes_id_serial`
                    WHERE `Order_comp`.`order_id` = " . $order['id'];

                $dishResult = mysqli_query($connect, $fetchDish);
                $productNames = [];
                while ($product = mysqli_fetch_assoc($dishResult)) {
                    $productNames[] = $product['name'];
                }
                echo '<td>' . implode(', ', $productNames) . '</td>';
                echo '<td>';
                echo '<a href="?delete_order_id=' . $order['id'] . '" onclick="return confirm(\'Are you sure you want to delete this order?\')">Delete</a> | ';
                echo '<a href="?change_status_id=' . $order['id'] . '">Toggle Status</a>';
                echo '</td>';
                echo '</tr>';
            }
            ?>
        </table>
          <div>
            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                <a href="?page=<?php echo $i; ?>" class="pagination"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>

    </main>
</body>
</html>
