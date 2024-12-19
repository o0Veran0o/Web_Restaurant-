<?php
  error_reporting(0); 
  session_start();
if (($_SESSION['username'])==""||$_SESSION['role']!="Manager"){
    header("location:index.php");
}
$dishes_id_serial = "";
$name = "";
$category = "";
$weight = "";
$cost = "";
$recipe = "";

require_once 'include/db.php';

// get values from the form
function getPosts()
{
    $posts = array();
    $posts[0] = ($_POST['dishes_id_serial']);
    $posts[1] = ($_POST['name']);
    $posts[2] = ($_POST['category']);
    $posts[3] = ($_POST['weight']);
    $posts[4] = ($_POST['cost']);
    $posts[5] = ($_POST['recipe']);
    return $posts;
}

// Search
if (isset($_POST['search'])) {
    $data = getPosts();

    // Use prepared statement
    $search_Query = "SELECT * FROM `Dishes` WHERE name = ?";
    $stmt = $connect->prepare($search_Query);

    if ($stmt) {
        $stmt->bind_param('s', $data[1]);
        $stmt->execute();

        $search_Result = $stmt->get_result();

        if ($search_Result) {
            if ($search_Result->num_rows > 0) {
                while ($row = $search_Result->fetch_assoc()) {
                    $name = $row['name'];
                    $category = $row['category'];
                    $weight = $row['weight'];
                    $cost = $row['cost'];
                    $recipe = $row['recipe'];
                }
            } else {
                $var1 = 'No Data For This id';
            }
        } else {
            $var1 = 'Result Error: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        $var1 = 'Statement Error: ' . $connect->error;
    }
}


// Insert
if (isset($_POST['insert'])) {
    $data = getPosts();

    // Validate data obtained from getPosts()
    if (!is_numeric($data[3]) || !is_numeric($data[4]))  {
        $var1= 'Invalid data received';
    }
    else{
    try {
        $insert_Query = "INSERT INTO `Dishes`(`name`, `category`, `weight`, `cost`, `recipe`) VALUES (?, ?, ?, ?, ?)";
        $stmt = $connect->prepare($insert_Query);

        if ($stmt) {
            $stmt->bind_param('sssss', $data[1], $data[2], $data[3], $data[4], $data[5]);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $var1 = 'Data Inserted';
            } else {
                $var1 = 'Data Not Inserted';
            }

            $stmt->close();
        } else {
             $var1 = 'Error preparing statement: ' . $connect->error;
        }
    } catch (Exception $e) {
         $var1 = 'Error: ' . $e->getMessage();
    }
  }
}


// Delete
if (isset($_POST['delete'])) {
    $data = getPosts();

    // Use prepared statement for the second delete query
    $deleteDishesQuery = "DELETE FROM `Dishes` WHERE `name` = ?";
    $stmt = $connect->prepare($deleteDishesQuery);

    if ($stmt) {
        $stmt->bind_param('s', $data[1]);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $var1 = 'Data Deleted';
        } else {
            $var1 = 'Data Not Deleted';
        }

        $stmt->close();
    } else {
       $var1 = 'Error Delete Dishes: ' . $connect->error;
    }
}


// Edit
if (isset($_POST['update'])) {
    $data = getPosts();
    
    // Validate numeric data
    if (!is_numeric($data[3]) || !is_numeric($data[4])) {
        $var1 = 'Invalid data received';
    } else {
        try {
            // Use a different identifier for updating (e.g., ID) instead of name
            $update_Query = "UPDATE `Dishes` SET `name`=?, `category`=?, `weight`=?, `cost`=?, `recipe`=? WHERE `name`=?";
            $stmt = $connect->prepare($update_Query);

            if ($stmt) {
                $stmt->bind_param('ssssss', $data[1], $data[2], $data[3], $data[4], $data[5], $data[1]);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $var1 = 'Data Updated';
                } else {
                    $var1 = 'Data Not Updated';
                }

                $stmt->close();
            } else {
                $var1 = 'Error Update: ' . $connect->error;
            }
        } catch (Exception $e) {
            $var1 = 'Error: ' . $e->getMessage();
        }
    }
}



?>

<!DOCTYPE Html>
<html lang="en">
    <head>
        <title>Dishes</title>
          <link rel="shortcut icon" href="images/icon.png" type="image/png">
        <link rel="stylesheet" href="css/manager_edit.css">
    </head>
    <body>
       <a href="menu.php"><img src="images/arrow.png" id = "f3" alt="HTML"></a>
        <form  method="post">
       <article class="shadowbox">
         <h1 id=f2>Dishes</h1>
<input type="text" name="name" placeholder="Name" value="<?php echo htmlspecialchars($name); ?>"><br>
<input type="text" name="category" placeholder="category" value="<?php echo htmlspecialchars($category); ?>"><br>
<input type="text" name="weight" placeholder="weight" value="<?php echo htmlspecialchars($weight); ?>"><br>
<input type="text" name="cost" placeholder="cost" value="<?php echo htmlspecialchars($cost); ?>"><br>
<input type="text" name="recipe" placeholder="recipe" value="<?php echo htmlspecialchars($recipe); ?>"><br>

            <div>
                <!-- Input For Add Values To Database-->
                <input type="submit" name="insert" value="Add">
                
                <!-- Input For Edit Values -->
                <input type="submit" name="update" value="Update">
                
                <!-- Input For Clear Values -->
                <input type="submit" name="delete" value="Delete">
                
                <!-- Input For Find Values With The given dishes_id_serial -->
                <input type="submit" name="search" value="Find">
            </div>
       

                <?php
$itemsPerPage = 10; // Adjust this value as needed
if (isset($_GET['page'])) {
    $currentPage = $_GET['page'];
} else {
    $currentPage = 1;
}

$startFrom = ($currentPage - 1) * $itemsPerPage;

$sql = "SELECT * FROM Dishes LIMIT $startFrom, $itemsPerPage";

if ($result = $connect->query($sql)) {
    $rowsCount = $result->num_rows; // количество полученных строк
    echo "<p class ='p1'> $var1 </p>";
    echo "<p class ='p1'>Amount of rows: $rowsCount</p>";
    echo "  <table class ='p1'> <tr><th>Name</th><th>Category</th><th>Weight</th><th>Cost</th><th>Recipe</th></tr>";

    foreach ($result as $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
        echo "<td>" .  htmlspecialchars($row['weight']) . "</td>";
        echo "<td>" .  htmlspecialchars($row['cost']) . "</td>";
        echo "<td>" .   htmlspecialchars ($row['recipe']) . "</td>";
        echo "</tr>";
    }

    echo "</table>";

    // Pagination links
    $sql = "SELECT * FROM Dishes";
    $result = $connect->query($sql);
      $rowsCount = $result->num_rows;
    $totalPages = ceil($rowsCount / $itemsPerPage);
    echo "<div>";
    for ($i = 1; $i <= $totalPages; $i++) {
        echo "<a href='?page=$i'  class='pagination'>$i</a>";
    }
    echo "</div>";

    $result->free();
} else {
    echo "Error: " . $connect->error;
}
$connect->close();
?>
    </article>
     </form>
    </body>
</html>