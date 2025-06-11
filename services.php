<!-- filepath: c:\xampp\htdocs\wt_project\services.php -->
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hotel_booking";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$whereClause = "";
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $filters = [];
    if (!empty($_GET['room_size'])) {
        $filters[] = "room_size = '" . $conn->real_escape_string($_GET['room_size']) . "'";
    }
    if (!empty($_GET['max_cost'])) {
        $filters[] = "cost_per_night <= " . $conn->real_escape_string($_GET['max_cost']);
    }
    if (!empty($_GET['location'])) {
        $filters[] = "location LIKE '%" . $conn->real_escape_string($_GET['location']) . "%'";
    }
    if (!empty($filters)) {
        $whereClause = "WHERE " . implode(" AND ", $filters);
    }
}

$sql = "SELECT * FROM services $whereClause";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services | Rayal Park</title>
    <link rel="stylesheet" href="style.css">

    <style>
        /* NAVBAR STYLES FIXED */
        .nav__bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            
            z-index: 999;
            position: relative;
        }

        .logo img {
            background-color: transparent; /* Ensure no background color for the logo */
            height: 50px; /* Keep the height as it is */
            display: block; /* Ensure no inline spacing issues */
        }

        .nav__links {
            list-style: none;
            display: flex !important;
            gap: 2rem;
            margin: 0;
            padding: 0;
        }

        .nav__links li a {
            color: #ffffff !important;
            text-decoration: none;
            font-size: 1rem;
            font-weight: bold;
            position: relative;
            visibility: visible !important;
            display: inline-block !important;
        }

        .nav__links li a.active::after {
            content: "";
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background: #fff;
        }

        /* SERVICES STYLES */
        .services__grid {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: space-between;
        }

        .service__card {
            flex: 0 0 calc(20% - 1rem);
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            background-color: #fff;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .service__card:hover {
            transform: translateY(-5px);
        }

        .service__card__image img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .service__card__details {
            padding: 10px;
        }

        .service__card__details h4 {
            margin: 0.5rem 0;
            font-size: 1rem;
        }

        .service__card__details p {
            font-size: 0.9rem;
            color: #555;
        }

        .service__card__details h5 span {
            color: #007BFF;
        }

        .service__card__details .btn {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .service__card__details .btn:hover {
            background-color: #0056b3;
        }

        @media screen and (max-width: 1024px) {
            .service__card {
                flex: 0 0 calc(33.33% - 1rem);
            }
        }

        @media screen and (max-width: 768px) {
            .service__card {
                flex: 0 0 calc(50% - 1rem);
            }
        }

        @media screen and (max-width: 480px) {
            .service__card {
                flex: 0 0 100%;
            }
        }
    </style>
</head>
<body>
    <header class="header">
    <nav>
        <div class="nav__bar">
          <div class="logo">
            <a href="index.php"><img src="image/logo.png" alt="logo" /></a>
          </div>
          <div class="nav__menu__btn" id="menu-btn">
            <i class="ri-menu-line"></i>
          </div>
        </div>
        <ul class="nav__links" id="nav-links">
        <ul class="nav__links">
                    <li><a href="index.php" >Home</a></li>
                    <li><a href="services.php" >Services</a></li>
                
          
         
        </ul>
        <button class="btn nav__btn"><a href="login.php">Login | Signup</a></button>
      </nav>
    </header>

    <section class="section__container services__container">
        <h2 class="section__header">Our Services</h2>
        <form method="GET" action="services.php" class="filter__form">
            <div class="form-group">
                <label for="room_size">Room Size:</label>
                <select name="room_size" id="room_size">
                    <option value="">All</option>
                    <option value="Deluxe">Deluxe</option>
                    <option value="Executive">Executive</option>
                    <option value="Family">Family</option>
                </select>
            </div>
            <div class="form-group">
                <label for="max_cost">Max Cost Per Night:</label>
                <input type="number" name="max_cost" id="max_cost" placeholder="Enter max cost">
            </div>
            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" name="location" id="location" placeholder="Enter location">
            </div>
            <button type="submit" class="btn">Filter</button>
        </form>

        <div class="services__grid">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="service__card">';
                    echo '<div class="service__card__image">';
                    echo '<img src="' . $row['photo'] . '" alt="room">';
                    echo '</div>';
                    echo '<div class="service__card__details">';
                    echo '<h4>' . $row['room_size'] . '</h4>';
                    echo '<p>' . $row['description'] . '</p>';
                    echo '<p><strong>Location:</strong> ' . $row['location'] . '</p>';
                    echo '<h5>Starting from <span>$' . $row['cost_per_night'] . '/night</span></h5>';
                    echo '<div class="service__card__actions">';
                    echo '<br><a href="checkout.php?service_id=' . $row['id'] . '" class="btn">Book Now</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No services found matching your criteria.</p>';
            }
            ?>
        </div>
    </section>

    <footer class="footer">
        <div class="footer__container">
            <p>&copy; 2025 Rayal Park. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
<?php $conn->close(); ?>
