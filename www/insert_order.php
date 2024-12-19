<?php
// insert_order.php
error_reporting(0);
session_start();

require_once 'include/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate the dish ID (you may get this from the client-side)
    $data = json_decode(file_get_contents('php://input'), true);

    // Sanitize and validate the user's email
    $email = mysqli_real_escape_string($connect, $_SESSION['email']);
    $query = "SELECT employes_id_serial FROM Employees WHERE email = '$email'";
    $result = mysqli_query($connect, $query);

    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Error executing email query: ' . mysqli_error($connect)]);
        exit;
    }

    // Fetch the result row
    $row = mysqli_fetch_assoc($result);

    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'No employee found for the given email']);
        exit;
    }

    // Get the employee ID serial
    $employeeIdSerial = $row['employes_id_serial'];

    // Close the result set
    mysqli_free_result($result);

    // Construct the SQL query using a prepared statement
    $query = "INSERT INTO `Order`(`customer_id`, `status`) VALUES (?, 'not-finished')";
    $stmt = $connect->prepare($query);

    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Error preparing statement']);
        exit;
    }

    // Bind parameters and execute the statement
    $stmt->bind_param("i", $employeeIdSerial);

    if ($stmt->execute()) {
        // Close the statement
        $stmt->close();

        // Get the last inserted order ID
        $orderId = mysqli_insert_id($connect);

        // Insert into Order_comp table
        $query = "INSERT INTO `Order_comp` (`order_id`, `dish_id`) VALUES (?, ?)";
        $stmt = $connect->prepare($query);

        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Error preparing Order_comp statement: ' . $connect->error]);
            exit;
        }

        // Bind parameters and execute the statement
        $stmt->bind_param("ii", $orderId, $dishId);
           foreach ($data['dishIds'] as $dishId) {
            $stmt->execute();
        }
        // Close the statement
        $stmt->close();
        $connect->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error executing statement: ' . $stmt->error]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
