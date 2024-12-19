<?php 
error_reporting (0);
require_once 'include/db.php';
$employes_id_serial = "";
$name = "";
$role = "";
$restaurant = "";
$phone_number = "";
$email = "";
$password = "";
$password_confirm="";


/**
 * Get an array of posted data.
 *
 * @return array An array containing posted data.
 */
function getPosts()
{
    $posts = array();
    $posts[0] = htmlspecialchars($_POST['employes_id_serial']);
    $posts[1] = htmlspecialchars($_POST['name']);
    $posts[2] = htmlspecialchars($_POST['role']);
    $posts[3] = htmlspecialchars($_POST['restaurant']);
    $posts[4] = validatePhoneNumber(htmlspecialchars($_POST['phone_number']), htmlspecialchars($_POST['email']));
    $posts[5] = validateEmail(htmlspecialchars($_POST['email']));
    $posts[6] = validatePassword(htmlspecialchars($_POST['password']));
    $posts[7] = validatePasswordConfirmation(htmlspecialchars($_POST['password']), htmlspecialchars($_POST['password_confirm']));
    $posts[8] = $_FILES['account_image'];

    return $posts;
}


/**
 * Validate file format.
 *
 * @param array $file The file information from $_FILES.
 *
 * @return string|null Validated file format, or null if validation fails.
 */
function validateFileFormat($file)
{
    $allowedFormats = ['jpg', 'jpeg', 'png'];
    $fileFormat = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($fileFormat, $allowedFormats)) {
        global $errorMessage;
        $errorMessage .= "Invalid file format. Allowed formats: " . implode(', ', $allowedFormats) . "<br>";
        return NULL;
    }

    return $fileFormat;
}


/**
 * Validate phone number and email.
 *
 * @param string $phoneNumber The phone number to validate.
 * @param string $email       The email address to validate.
 *
 * @return string|null Validated phone number or email, or null if validation fails.
 */
function validatePhoneNumber($phoneNumber, $email)
{
    global $connect, $errorMessage;

    // Check if there are any invalid characters in the phone number
    if (!preg_match('/^[0-9\+]+$/', $phoneNumber)) {
        $errorMessage .= "Invalid characters in the phone number. Only numbers and '+' are allowed.<br>";
        return NULL;
    }

    if (empty($phoneNumber) && empty($email)) {
        $errorMessage .= "Please enter either a phone number or an email address<br>";
        return NULL;
    }

    if (!empty($phoneNumber)) {
        if (strlen($phoneNumber) >= 9) {

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
}

/**
 * Validate email address.
 *
 * @param string $email The email address to validate.
 *
 * @return string|null Validated email address, or null if validation fails.
 */
function validateEmail($email)
{
    global $connect, $errorMessage;
    if(!empty($email)){
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

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
}
/**
 * Validate password confirmation.
 *
 * @param string $password        The password to confirm.
 * @param string $passwordConfirm The confirmed password.
 *
 * @return string|null Validated password confirmation, or null if validation fails.
 */
function validatePasswordConfirmation($password, $passwordConfirm)
{
    global $errorMessage;

    if ($password != $passwordConfirm) {
        $errorMessage .= "Passwords do not match<br>";
        return NULL;
    }

    return $passwordConfirm;
}
/**
 * Validate and hash the password.
 *
 * @param string $password The password to validate and hash.
 *
 * @return string|null Validated and hashed password, or null if validation fails.
 */
function validatePassword($password)
{
   
    if (strlen($password) >= 8 && preg_match('/[A-Z]/', $password)) {
       return password_hash($password, PASSWORD_DEFAULT);
    } else {
        global $errorMessage;
        $errorMessage .= "Invalid password (must contain more than 8 character and one upper letter) <br>";
        return NULL;
    }
}

if (isset($_POST['insert'])) {
    $data = getPosts();
        $fileFormat = validateFileFormat($data[8]);
    if (!empty($errorMessage)) {
        $var1= " Registration failed ". $errorMessage;
    } else {
       $uploadDir = 'sessions/'; // Set your desired upload directory
$emailWithoutSpecialChars = preg_replace('/[^a-zA-Z0-9]/', '', $data[4]); // Remove special characters from email
$encodedFileName = urlencode($emailWithoutSpecialChars . '_' . basename($data[8]['name']));
$uploadFile = $uploadDir . $encodedFileName;

        if (move_uploaded_file($data[8]['tmp_name'], $uploadFile)) {
            // File uploaded successfully
            $insert_Query = "INSERT INTO `Employees`(`name`,`role`,`restaurant`,`phone_number`, `email`, `password`, `images_url`) VALUES (?, 'customer', 'U valirusika', ?, ?, ?, ?)";

            $stmt = mysqli_stmt_init($connect);
            if (mysqli_stmt_prepare($stmt, $insert_Query)) {
                mysqli_stmt_bind_param($stmt, 'sssss', $data[1], $data[4], $data[5], $data[6], $uploadFile);

                if (mysqli_stmt_execute($stmt)) {
                    session_start();
                    $_SESSION['username'] = $data[1];
                    $_SESSION['email'] = $data[4];
                    $_SESSION['phone_number'] = $data[5];
                    $_SESSION['image_url'] = $uploadFile; // Store image URL in session
                    header("location:index.php");
                } else {
                    $var1= 'Error Insert ' . mysqli_stmt_error($stmt);
                }

                mysqli_stmt_close($stmt);
            } else {
                $var1= 'Error: Unable to prepare statement';
            }
        } else {
            $var1= 'Error uploading image.';
        }
    }
}
?>


<!DOCTYPE Html>
<html lang="en">

    <head>
        <title>Registration form</title>
         <link rel="shortcut icon" href="images/icon.png" type="image/png">
             <link rel="stylesheet" href="css/reg_style.css">
    </head>
        <body>  
                <?php
   echo "<p> $var1 </p>";
   ?>
      
        <form  method="post" id = "serch_form"  enctype="multipart/form-data">
       <article class="container" >
    <h1>Register</h1>
      <a href="index.php"><img src="images/arrow.png" id="f3" alt="HTML"></a>
     <p>Please fill in this form to create an account.</p>
       <label for="name"><b>Name</b></label>
            <input type="text" name="name" placeholder="Name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" id="name" required><br><br>
            <label for="email"><b>Email</b></label>
            <input type="text" name="email" placeholder="Email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" id="email" required><br><br>
            <label for="phone_number"><b>Phone number</b></label>
            <input type="tel" name="phone_number" placeholder="Phone number" value="<?php echo isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : ''; ?>" id="phone_number" required><br><br>
            <label for="password"><b>Password</b></label>
            <input type="password" name="password" placeholder="More than 8 characters and one upper letter" value="<?php echo isset($_POST['password']) ? htmlspecialchars($_POST['password']) : ''; ?>" id="password" required><br><br>
            <label for="password_confirm"><b>Confirm password</b></label>
            <input type="password" name="password_confirm" placeholder="Enter password again" value="<?php echo isset($_POST['password_confirm']) ? htmlspecialchars($_POST['password_confirm']) : ''; ?>" id="password_confirm"><br><br>
            <label for="account_image"><b>Account Image</b></label>
            <input type="file" name="account_image" id="account_image" required><br><br>

            <input type="submit" name="insert" class="registerbtn" value="Register">

            <div class="container signin">
    <p>Already have an account? <a href="entry.php">Sign in</a>.</p>
  </div>
        </article>
        </form>
       
    </body>
</html> 
