<?php 
 error_reporting(0); 
session_start();
ini_set('session.gc_probability', 1);



?>
 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant</title>
    <link rel="shortcut icon" href="images/icon.png" type="image/png">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <main>
        <header>
            <img width="80" src="images/icon.png" alt="HTML">
            Menu
        </header>
        <nav >
            <ul id="menu">
                   <?php 
                if(!isset($_SESSION['username']) || $_SESSION['username'] == ""){
                    ?>

                    <li>
        <a href="registration.php">Registration</a>
                </li>
                <?php
                }
                ?>
                  <?php 
                if(!isset($_SESSION['username']) || $_SESSION['username'] == ""){
                    ?>
                    <li>
        <a href="entry.php">Sign in</a>
                </li>
                <?php
                }
                ?>
                   <li>
                    <?php
                    
                         if(isset($_SESSION['username']) && $_SESSION['username'] != ""){
                    echo "<a href=order.php>Order here, $_SESSION[username] !</a>";
                  
                }
                else
                    echo "You not log in";
                    ?>
                </li>
                <?php 
                if(isset($_SESSION['username']) && $_SESSION['username'] != ""){
                    ?>
                    <li>
                    <a href="entry.php">Log out</a>
                </li>
                   <li>
                    <a href="change.php">Change profile</a>
                    <img width="80" src="<?php echo $_SESSION['image_url']; ?>" alt="HTML">
                </li>
                <?php
                }
                ?>
            </ul>
        </nav>
                    

        <article>
            
             <h2 class="p1">Categories</h2>


    <?php
$dishes_id_serial = "";
$emploeer_id_serial = "";
$name = "";
$category = "";
$weight = "";
$cost = "";
$recipe = "";
require_once 'include/db.php';

function getPosts()
{
    $posts = array();
    $posts[0] = $_POST['dishes_id_serial'];
    $posts[1] = $_POST['emploeer_id_serial'];
    $posts[2] = $_POST['name'];
    $posts[3] = $_POST['category'];
    $posts[4] = $_POST['weight'];
    $posts[5] = $_POST['cost'];
    $posts[6] = $_POST['recipe'];
    return $posts;
}

$sql = "SELECT * FROM Dishes";
$array=[];
$result = $connect->query($sql);
?>

<form action="index.php" method='POST'>
   

    <?php
     echo "<input type=submit value='All' name='all'>";
     echo "<input type=submit value='Top sale' name='top'>";

    foreach ($result as $row) {

        if (!(in_array($row['category'], $array))) {
        
 echo "<input type=submit value='$row[category]' name='change'>";
 array_push($array, $row['category']);

}
}   
    ?>

</form>



<?php


if (isset($_POST['change'])) {
    $selectedCategory = $_POST['change'];
    $sql = "SELECT * FROM Dishes WHERE category = '$selectedCategory'";
    if ($result = $connect->query($sql)) {
        $rowsCount = $result->num_rows;
        echo "<p class='p1'>Amount of dishes: $rowsCount</p>";
        foreach ($result as $row) {
            echo "<div class='menu-item'> Name of dish : ". $row['name'] ."<br>". "Weight,gr : ".  $row['weight'] ."<br>"."Cost,Kč : ". $row['cost']."<br>". "Recipe : ".$row['recipe']."</div>" ;
        }
        $result->free();
    } else {
        echo "Error: " . $connect->error;
    }
    $connect->close();
}



if (isset($_POST['all'])) {
    $sql = "SELECT * FROM Dishes";
    if ($result = $connect->query($sql)) {
        $rowsCount = $result->num_rows;
        echo "<p class='p1'>Amount of dishes: $rowsCount</p>";
       
        foreach ($result as $row) {
            echo "<div class='menu-item'> Name of dish : ". $row['name'] ."<br>". "Weight,gr : ".  $row['weight'] ."<br>"."Cost,Kč : ". $row['cost']."<br>". "Recipe : ".$row['recipe']."</div>" ;
        }

       
        $result->free();
    } else {
        echo "Error: " . $connect->error;
    }
    $connect->close();
}

if (isset($_POST['top'])) {
    $sql = "SELECT * FROM Dishes WHERE cost<='20'";
    if ($result = $connect->query($sql)) {
        $rowsCount = $result->num_rows;
        echo "<p class='p1'>Amount of dishes: $rowsCount</p>";
       
        foreach ($result as $row) {
            echo "<div class='menu-item'> Name of dish : ". $row['name'] ."<br>". "Weight,gr : ".  $row['weight'] ."<br>"."Cost,Kč : ". $row['cost']."<br>". "Recipe : ".$row['recipe']."</div>" ;
        }

       
        $result->free();
    } else {
        echo "Error: " . $connect->error;
    }
    $connect->close();
}

?>
        </article>

        <footer>
        Nabok V.R.
        </footer>
    </main>
</body>
</html>