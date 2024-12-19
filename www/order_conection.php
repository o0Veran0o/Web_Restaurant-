<?php
require_once 'include/db.php';
// Fetch dishes from the database
$sql = "SELECT * FROM `Dishes`";
$result = $connect->query($sql);

// Check if there are results
if ($result && $result->num_rows > 0) {
    $dishes = array();

    // Fetch dishes and add to the array
    while ($row = $result->fetch_assoc()) {
        $dishes[] = $row;
    }

    // Return dishes as JSON
    header('Content-Type: application/json');
    echo json_encode($dishes);
} else {
    // No dishes found
    echo json_encode(['error' => 'No results']);
}

// Close connection
$connect->close();
?>
