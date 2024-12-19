<?php
error_reporting(0);
session_start();
ini_set('session.gc_probability', 1);
require_once 'include/db.php';

// Define the number of items to display per page
$itemsPerPage = 5;
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
            <img width="80" src="images/icon.png" alt="HTML" class="logo">
            Menu
            <?php
            if(isset($_SESSION['username']) && $_SESSION['username'] != "")
                echo "<img width='50' src='" . $_SESSION['image_url'] . "' alt='HTML'>";
            ?> 
        </header>
        <nav>
            <ul id="menu">
                <?php if(!isset($_SESSION['username']) || $_SESSION['username'] == ""): ?>
                    <li><a href="<?php echo "registration.php"; ?>">Registration</a></li>
                <?php endif; ?>

                <?php if(!isset($_SESSION['username']) || $_SESSION['username'] == ""): ?>
                    <li><a href="<?php echo "entry.php"; ?>">Sign in</a></li>
                <?php endif; ?>

                <li>
                    <?php
                    if(isset($_SESSION['username']) && $_SESSION['username'] != ""):
                        echo "<a href='" . "order.php" . "'>Order here, " . $_SESSION['username'] . "!</a>";
                    else:
                        echo "You are not logged in";
                    endif;
                    ?>
                </li>

                <?php if(isset($_SESSION['username']) && $_SESSION['username'] != ""): ?>
                    <li><a href="<?php echo "entry.php"; ?>">Log out</a></li>
                    <li><a href="<?php echo "change.php"; ?>">Change profile</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <article>
            <h2 class="p1">Categories</h2>

            <div>
                <a href="<?php echo "index.php?category="; ?>" class="categories">All</a>

                <?php
                $array = [];
                $result = $connect->query("SELECT DISTINCT category FROM Dishes");

                foreach ($result as $row) {
                    echo "<a href='" . "index.php?category=" . urlencode($row['category']) . "' class='categories'>" . htmlspecialchars($row['category'], ENT_QUOTES, 'UTF-8') . "</a>";
                    $array[] = $row['category'];
                }
                ?>
            </div>

            <?php
            $selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';

            if ($selectedCategory) {
                $sql = "SELECT * FROM Dishes WHERE category = ?";
            } else {
                $sql = "SELECT * FROM Dishes";
            }

            // Pagination: Calculate offset based on the current page
            $page = isset($_GET['page']) ? $_GET['page'] : 1;
            $offset = ($page - 1) * $itemsPerPage;

            // Add LIMIT and OFFSET to the SQL query
            $sql .= " LIMIT ? OFFSET ?";

            $stmt = $connect->prepare($sql);

            // Bind parameters
            if ($selectedCategory) {
                $stmt->bind_param("sii", $selectedCategory, $itemsPerPage, $offset);
            } else {
                $stmt->bind_param("ii", $itemsPerPage, $offset);
            }
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result) {
                $rowsCount = $result->num_rows;
                echo "<p class='p1'>Amount of dishes: " . htmlspecialchars($rowsCount, ENT_QUOTES, 'UTF-8') . "</p>";

                while ($row = $result->fetch_assoc()) {
                    echo "<div class='menu-item'> Name of dish : " . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . "<br>" . "Weight,gr : " . htmlspecialchars($row['weight'], ENT_QUOTES, 'UTF-8') . "<br>" . "Cost,Kč : " . htmlspecialchars($row['cost'], ENT_QUOTES, 'UTF-8') . "<br>" . "Recipe : " . htmlspecialchars($row['recipe'], ENT_QUOTES, 'UTF-8') . "</div>";
                }

                $stmt->close();
                
                if ($selectedCategory) {
                    $sqlpage = "SELECT * FROM Dishes WHERE category = ?";
                } else {
                    $sqlpage = "SELECT * FROM Dishes";
                }

                $stmt = $connect->prepare($sqlpage);

                if ($selectedCategory) {
                    $stmt->bind_param("s", $selectedCategory);
                }

                $stmt->execute();

                $result = $stmt->get_result();
                $rowsCount = $result->num_rows;

                // Add pagination links
                echo "<div class='pagination'>";
                $totalPages = ceil($rowsCount / $itemsPerPage);
                for ($i = 1; $i <= $totalPages; $i++) {
                    echo "<a href='" . "index.php?category=" . urlencode($selectedCategory) . "&page=$i' class='pages'>" . htmlspecialchars($i, ENT_QUOTES, 'UTF-8') . "</a>";
                }
                echo "</div>";

                $stmt->close();
            } else {
                echo "Error: " . htmlspecialchars($connect->error, ENT_QUOTES, 'UTF-8');
            }

            $connect->close();
            ?>
        </article>

        <footer>
            Nabok V.R.
        </footer>
    </main>
</body>
</html>
