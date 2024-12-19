<?php
  error_reporting(0); 
  session_start();
if (($_SESSION['username'])==""||$_SESSION['role']!="Manager"){
    header("location:index.php");
}
$provider_id_serial = "";
$name = "";
$firm = "";
$phone_number = "";
$email = "";
$password = "";

require_once 'include/db.php';

// get values from the form
function getPosts()
{
    $posts = array();
    $posts[0] = $_POST['provider_id_serial'];
    $posts[1] = $_POST['name'];
    $posts[2] = $_POST['firm'];
    $posts[3] = $_POST['phone_number'];
    $posts[4] = $_POST['email'];
    $posts[5] = $_POST['password'];
    return $posts;
}

// Search

if (isset($_POST['search'])) {
    $data = getPosts();
    if ($data[0] == 0) {
        $var1 = 'Result Error';
    } else {
        // Use prepared statement
        $search_Query = "SELECT * FROM `Provider` WHERE `provider_id_serial` = ?";
        $stmt = $connect->prepare($search_Query);

        if ($stmt) {
            $stmt->bind_param('i', $data[0]);
            $stmt->execute();

            $search_Result = $stmt->get_result();

            if ($search_Result) {
                if ($search_Result->num_rows > 0) {
                    while ($row = $search_Result->fetch_assoc()) {
                        $provider_id_serial = $row['provider_id_serial'];
                        $name = $row['name'];
                        $firm = $row['firm'];
                        $phone_number = $row['phone_number'];
                        $email = $row['email'];
                        $password = $row['password'];
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
}


// Insert
if (isset($_POST['insert'])) {
    $data = getPosts();

    // Use prepared statement
    $insert_Query = "INSERT INTO `Provider`(`name`, `firm`, `phone_number`, `email`, `password`) VALUES (?, ?, ?, ?, ?)";
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
        $var1 = 'Error Insert: ' . $connect->error;
    }
}


// Delete
if (isset($_POST['delete'])) {
    $data = getPosts();

    // Use prepared statement for the first delete query
    $deleteRecipeQuery = "DELETE FROM `Recipe products` WHERE `product_id_serial` IN (SELECT `product_id_serial` FROM `Products` WHERE `provider_id_serial` = ?)";
    $stmt = $connect->prepare($deleteRecipeQuery);

    if ($stmt) {
        $stmt->bind_param('i', $data[0]);
        $stmt->execute();
        $stmt->close();
    } else {
        $var1 = 'Error Delete Recipe: ' . $connect->error;
    }

    // Use prepared statement for the second delete query
    $deleteProductsQuery = "DELETE FROM `Products` WHERE `provider_id_serial` = ?";
    $stmt = $connect->prepare($deleteProductsQuery);

    if ($stmt) {
        $stmt->bind_param('i', $data[0]);
        $stmt->execute();
        $stmt->close();
    } else {
        $var1 = 'Error Delete Products: ' . $connect->error;
    }

    // Use prepared statement for the third delete query
    $deleteProviderQuery = "DELETE FROM `Provider` WHERE `provider_id_serial` = ?";
    $stmt = $connect->prepare($deleteProviderQuery);

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
        $var1 = 'Error Delete Provider: ' . $connect->error;
    }
}

// Edit
if (isset($_POST['update'])) {
    $data = getPosts();

    // Use prepared statement
    $update_Query = "UPDATE `Provider` SET `name`=?, `firm`=?, `phone_number`=?, `email`=?, `password`=? WHERE `provider_id_serial`=?";
    $stmt = $connect->prepare($update_Query);

    if ($stmt) {
        $stmt->bind_param('sssssi', $data[1], $data[2], $data[3], $data[4], $data[5], $data[0]);
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
}


?>

<!DOCTYPE Html>
<html lang="en">
    <head>
        <title>Provider</title>
        <link rel="shortcut icon" href="images/icon.png" type="image/png">
        <link rel="stylesheet" href="css/db_edit.css">
    </head>
    <body>
       <a href="menu.php"><img src="images/arrow.png" id = f3 alt="HTML"></a>
        <form method="post">
       <article class="shadowbox" >
        <h2 id=f2> Provider</h2>
            <input type="number"   name="provider_id_serial" placeholder="id" value="<?php echo $provider_id_serial;?>"><br><br>
            <input type="text"  name="name" placeholder="Name" value="<?php echo $name;?>"><br><br>
            <input type="text"  name="firm" placeholder="firm" value="<?php echo $firm;?>"><br><br>
            <input type="text"  name="phone_number" placeholder="Phone number" value="<?php echo $phone_number;?>"><br><br>
            <input type="text"  name="email" placeholder="Email" value="<?php echo $email;?>"><br><br>
            <input type="password"   name="password" placeholder="Password" value="<?php echo $password;?>"><br><br>
            <div>
                <!-- Input For Add Values To Database-->
                <input type="submit" name="insert" value="Add">
                
                <!-- Input For Edit Values -->
                <input type="submit" name="update" value="Update">
                
                <!-- Input For Clear Values -->
                <input type="submit" name="delete" value="Delete">
                
                <!-- Input For Find Values With The given provider_id_serial -->
                <input type="submit" name="search" value="Find">
            </div>
    

        <?php
 
$sql = "SELECT * FROM Provider";
if($result = $connect->query($sql)){
    $rowsCount = $result->num_rows; 
    echo "<p class='p1'> $var1 </p>";
    echo "<p class='p1'>Amount of rows: $rowsCount</p>";
    echo "  <table  class='p1'> <tr><th>Id</th><th>Name</th><th>Firm</th><th>Phone number</th><th>Email</th><th>Password</th></tr>";
    foreach($result as $row){
        echo "<tr>";
            echo "<td>" . $row['provider_id_serial'] . "</td>";
            echo "<td>" . $row['name'] . "</td>";
 echo "<td>" . $row['firm'] . "</td>";
 echo "<td>" . $row['phone_number'] . "</td>";
echo "<td>" . $row['email'] . "</td>";
echo "<td>" . $row['password'] . "</td>";
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