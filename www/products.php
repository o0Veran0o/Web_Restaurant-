<?php
  error_reporting(0); 
  session_start();
if (($_SESSION['username'])==""||$_SESSION['role']!="Manager"){
    header("location:index.php");
}
$product_id_serial = "";
$name = "";
$provider_id_serial = "";
$number_in_storage = "";
$number_of_ordered = "";
$date_of_creation = "";
$expiration_date = "";
$cost = "";

require_once 'include/db.php';

// get values from the form
function getPosts()
{
    $posts = array();
    $posts[0] = $_POST['product_id_serial'];
    $posts[1] = $_POST['name'];
    $posts[2] = $_POST['provider_id_serial'];
    $posts[3] = $_POST['number_in_storage'];
    $posts[4] = $_POST['number_of_ordered'];
    $posts[5] = $_POST['date_of_creation'];
    $posts[6] = $_POST['expiration_date'];
    $posts[7] = $_POST['cost'];
    return $posts;
}

// Search

if(isset($_POST['search'])) {
    $data = getPosts();

    // Use prepared statement
    $search_Query = "SELECT * FROM Products WHERE product_id_serial = ?";
    $stmt = $connect->prepare($search_Query);

    if ($stmt) {
        $stmt->bind_param('i', $data[0]);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($product_id_serial, $name, $provider_id_serial, $number_in_storage, $number_of_ordered, $date_of_creation, $expiration_date, $cost);
            $stmt->fetch();
            // You can now use these variables as needed
            $var1 = 'Data found';
        } else {
            $var1 = 'No Data For This id';
        }

        $stmt->close();
    } else {
        $var1 = 'Error in search query: ' . $connect->error;
    }
}


// Insert
if (isset($_POST['insert'])) {
    $data = getPosts();

    if (filter_var($data[3], FILTER_VALIDATE_INT) && filter_var($data[4], FILTER_VALIDATE_INT)) {
        $insert_Query = "INSERT INTO Products (name, provider_id_serial, number_in_storage, number_of_ordered, date_of_creation, expiration_date, cost) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connect->prepare($insert_Query);

        if ($stmt) {
            if ($data[5] == NULL && $data[6] != NULL) {
                $stmt->bind_param('siissd', $data[1], $data[2], $data[3], $data[4], $data[6], $data[7]);
            } elseif ($data[6] == NULL && $data[5] != NULL) {
                $stmt->bind_param('siissd', $data[1], $data[2], $data[3], $data[4], $data[5], $data[7]);
            } elseif ($data[5] == NULL && $data[6] == NULL) {
                $stmt->bind_param('siisd', $data[1], $data[2], $data[3], $data[4], $data[7]);
            } elseif ($data[5] != NULL && $data[6] != NULL) {
                $stmt->bind_param('siisssd', $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7]);
            }

            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $var1 = 'Data Inserted';
            } else {
                $var1 = 'Data Not Inserted';
            }

            $stmt->close();
        } else {
            $var1 = 'Error Insert: ' . $connect->error;
        }
    } else {
        $var1 = 'Invalid data';
    }
}


// Delete
if (isset($_POST['delete'])) {
    $data = getPosts();

    // Use prepared statement for the first delete query
    $deleteRecipeQuery = "DELETE FROM `Recipe products` WHERE `product_id_serial` = ?";
    $stmt = $connect->prepare($deleteRecipeQuery);

    if ($stmt) {
        $stmt->bind_param('i', $data[0]);
        $stmt->execute();
        $stmt->close();
    } else {
        $var1 = 'Error Delete Recipe: ' . $connect->error;
    }

    // Use prepared statement for the second delete query
    $deleteProductsQuery = "DELETE FROM `Products` WHERE `product_id_serial` = ?";
    $stmt = $connect->prepare($deleteProductsQuery);

    if ($stmt) {
        $stmt->bind_param('i', $data[0]);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $var1 = 'Data Deleted';
        } else {
            $var1 = 'Data Not Deleted';
        }

        $stmt->close();
    } else {
        $var1 = 'Error Delete Products: ' . $connect->error;
    }
}


// Edit
if (isset($_POST['update'])) {
    $data = getPosts();

    if (filter_var($data[3], FILTER_VALIDATE_INT) && filter_var($data[4], FILTER_VALIDATE_INT)) {
        // Use prepared statement
        $update_Query = "UPDATE `Products` SET `name`=?, `provider_id_serial`=?, `number_in_storage`=?, `number_of_ordered`=?, `date_of_creation`=?, `expiration_date`=?, `cost`=? WHERE `product_id_serial`=?";
        $stmt = $connect->prepare($update_Query);

        if ($stmt) {
            $stmt->bind_param('siiisssi', $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[0]);
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
    } else {
        $var1 = 'Invalid data';
    }
}


?>

<!DOCTYPE Html>
<html lang="en">
    <head>
        <title>Products</title>
             <link rel="shortcut icon" href="images/icon_bd.png" type="image/png">
        <link rel="stylesheet" href="css/db_edit.css">
    </head>
    <body>
<a href="menu.php"><img src="images/arrow.png" id = f3 alt="HTML"></a>
        <form  method="post">
       <article class="shadowbox" >

        <h1 id=f2> Products</h1>
            <input type="number"  name="product_id_serial" placeholder="id" value="<?php echo $product_id_serial;?>"><br><br>
            <input type="text"   name="name" placeholder="Name" value="<?php echo $name;?>"><br><br>
            <input type="number" name="provider_id_serial" placeholder="provider id" value="<?php echo $provider_id_serial;?>"><br><br>
            <input type="text"  name="number_in_storage" placeholder="number in storage" value="<?php echo $number_in_storage;?>"><br><br>
            <input type="text"  name="number_of_ordered" placeholder="number of ordered" value="<?php echo $number_of_ordered;?>"><br><br>
            <input type="datetime-local"  name="date_of_creation"  value="<?php echo $date_of_creation;?>"><br><br>
            <input type="datetime-local"  name="expiration_date"  value="<?php echo $expiration_date;?>"><br><br>
            <input type="text" name="cost"  placeholder="cost" value="<?php echo $cost;?>"><br><br>
            <div>
                <!-- Input For Add Values To Database-->
                <input type="submit" name="insert" value="Add">
                
                <!-- Input For Edit Values -->
                <input type="submit" name="update" value="Update">
                
                <!-- Input For Clear Values -->
                <input type="submit" name="delete" value="Delete">
                
                <!-- Input For Find Values With The given product_id_serial -->
                <input type="submit" name="search" value="Find">
            </div>
       

<?php
$sql = "SELECT * FROM Products";
if($result = $connect->query($sql)){
    $rowsCount = $result->num_rows; // количество полученных строк
    echo "<p class='p1'> $var1 </p>";
    echo "<p  class='p1'>Amount of rows: $rowsCount</p>";
    echo "  <table   class='p1'> <tr><th>Id</th><th>Name</th><th>Provider id</th><th>Number in storage</th><th>Number of ordered</th><th>Date of creation</th><th>Expiration date</th><th>Cost</th></tr>";
    foreach($result as $row){
        echo "<tr>";
            echo "<td>" . $row['product_id_serial'] . "</td>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['provider_id_serial'] . "</td>";
 echo "<td>" . $row['number_in_storage'] . "</td>";
 echo "<td>" . $row['number_of_ordered'] . "</td>";
echo "<td>" . $row['date_of_creation'] . "</td>";
echo "<td>" . $row['expiration_date'] . "</td>";
echo "<td>" . $row['cost'] . "</td>";

           
        echo "</tr>";
    }
 
    echo "</table>";
    $result->free();
} else{
    echo "Error: " . $connect->error;
}
$connect->close();
?>
 </article>
    </form>
    </body>
</html>