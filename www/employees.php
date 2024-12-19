<?php 
  error_reporting(0); 
  session_start();
if (($_SESSION['username'])==""||$_SESSION['role']!="Manager"){
    header("location:index.php");
}
$employes_id_serial = "";
$name = "";
$role = "";
$restaurant = "";
$phone_number = "";
$email = "";
$password = "";

require_once 'include/db.php';

// get values from the form
function getValidatedPosts()
{
    $posts = array();
    $posts[0] = $_POST['employes_id_serial'];
    $posts[1] = $_POST['name'];
    $posts[2] = $_POST['role'];
    $posts[3] = $_POST['restaurant'];
    $posts[4] = validatePhoneNumber($_POST['phone_number']);
    $posts[5] = validateEmail($_POST['email']);
    $posts[6] = validatePassword($_POST['password']);
    return $posts;
}
function getPosts()
{
    $posts = array();
    $posts[0] = $_POST['employes_id_serial'];
    $posts[1] = $_POST['name'];
    $posts[2] = $_POST['role'];
    $posts[3] = $_POST['restaurant'];
    $posts[4] = $_POST['phone_number'];
    $posts[5] = $_POST['email'];
    $posts[6] = $_POST['password'];
    return $posts;
}

// Search

if (isset($_POST['search'])) {
    $data = getPosts();    
    $search_Query = "SELECT * FROM Employees WHERE employes_id_serial = ? OR phone_number = ? OR email = ?";
    
    $stmt = mysqli_prepare($connect, $search_Query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'sss', $data[0], $data[4], $data[5]);
        mysqli_stmt_execute($stmt);

        $search_Result = mysqli_stmt_get_result($stmt);

        if ($search_Result) {
            if (mysqli_num_rows($search_Result) > 0) {
                while ($row = mysqli_fetch_array($search_Result)) {
                    $employes_id_serial = $row['employes_id_serial'];
                    $name = $row['name'];
                    $role = $row['role'];
                    $restaurant = $row['restaurant'];
                    $phone_number = $row['phone_number'];
                    $email = $row['email'];
                    $var1 = 'Data found';
                }
            } else {
                $var1 = 'No data for this id, phone number, or email';
            }
        } else {
            $var1 = 'Result Error';
        }

        mysqli_stmt_close($stmt);
    } else {
        $var1 = 'Statement Error';
    }
}
// Insert

function validatePhoneNumber($phoneNumber)
{
    global $connect, $errorMessage;
    // Example: Check if it's a valid phone number format (10 digits)
    $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber); // Remove non-numeric characters

    if (strlen($phoneNumber) >= 9) {
        // Check for duplicate phone number in the database
        $query = "SELECT * FROM Employees WHERE phone_number = '$phoneNumber'";
        $result = mysqli_query($connect, $query);

        if (mysqli_num_rows($result) > 0) {
            $errorMessage .= "Phone number already exists<br>";
            return NULL;
        }

        return $phoneNumber;
    } else {
        $errorMessage .= "Invalid phone number<br>";
        return NULL;
    }
}

function validateEmail($email)
{
    global $connect, $errorMessage;

    // Example: Use PHP's built-in filter_var to check for a valid email format
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Check for duplicate email in the database
        $query = "SELECT * FROM Employees WHERE email = '$email'";
        $result = mysqli_query($connect, $query);

        if (mysqli_num_rows($result) > 0) {
            $errorMessage .= "Email address already exists<br>";
            return NULL;
        }

        return $email;
    } else {
        $errorMessage .= "Invalid email address<br>";
        return NULL;
    }
}


function validatePassword($password)
{
    // Example: Check if it meets criteria (more than 8 characters, one upper letter)
    if (strlen($password) >= 8 && preg_match('/[A-Z]/', $password)) {
       return password_hash($password, PASSWORD_DEFAULT);
    } else {
        global $errorMessage;
        $errorMessage .= "Invalid password (must contain more than 8 character and at least one upper letter) <br>";
        return NULL;
    }
}

if (isset($_POST['insert'])) {
    $data = getValidatedPosts();
    
    if (!empty($errorMessage)) {
        $var1 = 'Insertion failed: ' . $errorMessage;
        $errorMessage = '';
    } else {
        $insert_Query = "INSERT INTO `Employees` (`employes_id_serial`, `name`, `role`, `restaurant`, `phone_number`, `email`, `password`) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        try {
            $stmt = mysqli_prepare($connect, $insert_Query);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "issssss", $data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6]);
                $insert_Result = mysqli_stmt_execute($stmt);

                if ($insert_Result) {
                    $var1 = 'Data Inserted';
                } else {
                    $var1 = 'Data Not Inserted';
                }
            } else {
                $var1 = 'Error in preparing statement';
            }

            mysqli_stmt_close($stmt);
        } catch (Exception $ex) {
            $var1 = 'Data Not Inserted: ' . $ex->getMessage();
        }
    }
}

// Delete
if (isset($_POST['delete'])) {
    $data = getPosts();

    // Get employee ID
    $selectIdQuery = "SELECT employes_id_serial FROM Employees WHERE phone_number = ? OR email = ?";
    $stmtSelectId = mysqli_prepare($connect, $selectIdQuery);
    mysqli_stmt_bind_param($stmtSelectId, "ss", $data[4], $data[5]);
    mysqli_stmt_execute($stmtSelectId);
    $selectIdResult = mysqli_stmt_get_result($stmtSelectId);

    if ($selectIdResult) {
        if (mysqli_num_rows($selectIdResult) > 0) {
            $selectIdRow = mysqli_fetch_assoc($selectIdResult);
            $employeeId = $selectIdRow['employes_id_serial'];

            // Delete records from 'Recipe products'
            $deleteRecipeQuery = "DELETE FROM `Recipe products` WHERE `dishes_id_serial` IN (SELECT `dishes_id_serial` FROM `Dishes` WHERE `emploeer_id_serial` = ?)";
            $stmtDeleteRecipe = mysqli_prepare($connect, $deleteRecipeQuery);
            mysqli_stmt_bind_param($stmtDeleteRecipe, "i", $employeeId);
            $deleteRecipeResult = mysqli_stmt_execute($stmtDeleteRecipe);

            if (!$deleteRecipeResult) {
                $var1 = 'Error Delete Recipe: ' . mysqli_error($connect);
            }

            // Delete records from 'Dishes'
            $deleteDishesQuery = "DELETE FROM `Dishes` WHERE `emploeer_id_serial` = ?";
            $stmtDeleteDishes = mysqli_prepare($connect, $deleteDishesQuery);
            mysqli_stmt_bind_param($stmtDeleteDishes, "i", $employeeId);
            $deleteDishesResult = mysqli_stmt_execute($stmtDeleteDishes);

            if (!$deleteDishesResult) {
                $var1 = 'Error Delete Dishes: ' . mysqli_error($connect);
            }

            // Delete record from 'Employees'
            $deleteEmployeesQuery = "DELETE FROM `Employees` WHERE `employes_id_serial` = ?";
            $stmtDeleteEmployees = mysqli_prepare($connect, $deleteEmployeesQuery);
            mysqli_stmt_bind_param($stmtDeleteEmployees, "i", $employeeId);
            $deleteEmployeesResult = mysqli_stmt_execute($stmtDeleteEmployees);

            if ($deleteEmployeesResult) {
                if (mysqli_affected_rows($connect) > 0) {
                    $var1 = 'Data Deleted';
                } else {
                    $var1 = 'Data Not Deleted';
                }
            } else {
                $var1 = 'Error Delete Employees: ' . mysqli_error($connect);
            }
        } else {
            $var1 = 'No data for this id, phone number, or email';
        }
    } else {
        $var1 = 'Error Select ID: ' . mysqli_error($connect);
    }

    // Close prepared statements
    mysqli_stmt_close($stmtSelectId);
    mysqli_stmt_close($stmtDeleteRecipe);
    mysqli_stmt_close($stmtDeleteDishes);
    mysqli_stmt_close($stmtDeleteEmployees);
}


// Edit
if (isset($_POST['update'])) {
    $data = getValidatedPosts();
    if (!empty($errorMessage)) {
        $var1 = 'Failed to Update: ' . $errorMessage;
        $errorMessage = '';
    } else {
        $update_Query = "UPDATE `Employees` SET `name`=?, `role`=?, `restaurant`=?, `phone_number`=?, `email`=?, `password`=? WHERE `employes_id_serial` = ?";
        try {
            $stmt = mysqli_prepare($connect, $update_Query);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssssssi", $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[0]);
                $result = mysqli_stmt_execute($stmt);

                if ($result) {
                    $var1 = 'Data Updated';
                } else {
                    $var1 = 'Data Not Updated';
                }
            } else {
                $var1 = 'Error in preparing statement';
            }

            mysqli_stmt_close($stmt);
        } catch (Exception $ex) {
            $var1 = 'Error Update ' . $ex->getMessage();
        }
    }
}


?>

<!DOCTYPE Html>
<html lang="en">
    <head>
        <title>Employees</title>
         <link rel="shortcut icon" href="images/icon_bd.png" type="image/png">
        <link rel="stylesheet" href="css/db_edit.css">
    </head>
    <body>
       <a href="menu.php"><img src="images/arrow.png" id = f3  alt="HTML"></a>
        <form method="post">
       <article class="shadowbox">
        <h1 id=f2> Employees</h1>
            <input type="number" name="employes_id_serial" placeholder="id" value="<?php echo $employes_id_serial;?>"><br><br>
            <input type="text" name="name" placeholder="Name" value="<?php echo $name;?>"><br><br>
            <input type="text" name="role" placeholder="Role" value="<?php echo $role;?>"><br><br>
            <input type="text" name="restaurant" placeholder="Shop name" value="<?php echo $restaurant;?>"><br><br>
            <input type="text" name="phone_number" placeholder="Phone number" value="<?php echo $phone_number;?>"><br><br>
            <input type="text" name="email" placeholder="Email" value="<?php echo $email;?>"><br><br>
             <input type="password" name="password" placeholder="password" value="<?php echo $password;?>"><br><br>
            <div>
                <!-- Input For Add Values To Database-->
                <input type="submit" name="insert" value="Add">
                
                <!-- Input For Edit Values -->
                <input type="submit" name="update" value="Update">
                
                <!-- Input For Clear Values -->
                <input type="submit" name="delete" value="Delete">
                
                <!-- Input For Find Values With The given employes_id_serial -->
                <input type="submit" name="search" value="Find">
            </div>
                    <?php
$sql = "SELECT * FROM Employees";
if($result = $connect->query($sql)){
    $rowsCount = $result->num_rows; // количество полученных строк
    echo "<p class='p1'> $var1 </p>";
    echo "<p class='p1'>Amount of rows: $rowsCount</p>";
    echo "  <table> <tr><th>Id</th><th>Name</th><th>Role</th><th>Shop name</th> <th>Phone number</th> <th>Email</th><th>Password</th></tr>";
    foreach($result as $row){
        echo "<tr>";
            echo "<td>" . $row['employes_id_serial'] . "</td>";
            echo "<td>" . $row['name'] . "</td>";
 echo "<td>" . $row['role'] . "</td>";
 echo "<td>" . $row['restaurant'] . "</td>";
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