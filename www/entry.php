<?php
session_start();
error_reporting(0);
session_save_path('sessions');
ini_set('session.gc_probability', 1);

$employes_id_serial = "";
$name = "";
$role = "";
$restaurant = "";
$phone_number = "";
$email = "";
$password = "";

require_once 'include/db.php';

$_SESSION['username'] = "";
$_SESSION['email'] = "";
$_SESSION['phone_number'] = "";
$_SESSION['image_url'] = "";
$_SESSION['role'] = "";
/**
 * Get values from the form.
 *
 * @return array An array containing form input values.
 */
function getPosts()
{
    $posts = array();
    $posts[0] = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
    $posts[1] = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '';
    $posts[2] = isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '';
    return $posts;
}


// Search
if (isset($_POST['search'])) {
    $data = getPosts();

    $enteredEmail = $data[0];
    $enteredPhone = $data[2];
    $enteredPassword = $data[1];

    // Use prepared statement
    $query = "SELECT * FROM Employees WHERE (email = ? OR phone_number = ?)";
    $stmt = mysqli_prepare($connect, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ss', $enteredEmail, $enteredPhone);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $hashedPasswordFromDatabase = $row['password'];

            // Verify the entered password against the hashed password from the database
            if (password_verify($enteredPassword, $hashedPasswordFromDatabase) || $enteredPassword == $hashedPasswordFromDatabase) {
                // Passwords match, proceed with login
                session_start();
                $_SESSION['username'] = $row['name']; // Storing the username in the session
                $_SESSION['email'] =  $row['email'];
                $_SESSION['phone_number'] =  $row['phone_number'];
                $_SESSION['image_url'] = $row['images_url']; // Store image URL in session
                // Check if the user has the role 'Manager'
                if ($row['role'] === 'Manager') {
                    $_SESSION['role'] = 'Manager';
                    header("location: menu.php"); // Redirect to the main page or dashboard for Manager
                    exit();
                } else {
                    $_SESSION['role'] = 'customer';
                    header("location: index.php"); // Redirect to the main page for non-Managers
                    exit();
                }
            } else {
                // Passwords do not match, handle the error
                 $var1= "Invalid password";
            }
        } else {
            // User not found or other error handling
            $var1= "Invalid email or phone number";
        }

        mysqli_stmt_close($stmt);
    } else {
        // Handle prepared statement error
          $var1= "Query preparation error";
    }

    mysqli_close($connect);
}

session_write_close();
?>
<!DOCTYPE Html>
<html lang="en">


<head>
    <title>Entry form</title>
    <link rel="stylesheet" href="css/entry_style.css">
    <link rel="shortcut icon" href="images/icon.png" type="image/png">
</head>

<body>
    <?php
   echo "<p> $var1 </p>";
   ?>
    <form method="post">
        <div class="container">
              <a href="index.php"><img src="images/arrow.png" id="f3" alt="HTML"></a>
            <h1>Entry</h1>
            <p>Please fill out this form to sign in to your account.</p>
            <hr>

            <label for="email"><b>Email</b></label>
            <input type="text" placeholder="Enter email" name="email" id="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

            <label for="Phone"><b>Phone number</b></label>
            <input type="tel" placeholder="Enter phone" name="phone" id="Phone" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">

            <label for="password"><b>Password</b></label>
            <input type="password" placeholder="Enter Password" name="password" id="password" required>

            <button type="submit" name="search" class="registerbtn">Entry</button>
        </div>

        <div class="container signin">
            <p>Don't have an account? <a href="registration.php">Register in</a>.</p>
        </div>
    </form>

</body>

</html>
