<?php
session_start();
// Read the raw POST data
$orderData = json_decode(file_get_contents("php://input"), true);

// Extract order information
$orderItems = $orderData['items'];
$totalSum = $orderData['totalSum'];

// Format the order information for the email
$emailContent = "New Order\n\n";
foreach ($orderItems as $item) {
    $emailContent .= "Item: {$item['name']}, Cost: {$item['cost']} Kč\n";
}
$emailContent .= "\nTotal Sum: $totalSum Kč";

// Email configuration
$to = 'valera.nabo.r@gmail.com';
$subject = 'New Order';
$headers = 'From:'.$_SESSION['email']; // Replace with your email address

// Send the email
$mailSuccess = mail($to, $subject, $emailContent, $headers);

// Respond with success or failure
if ($mailSuccess) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}

?>