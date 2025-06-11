<!-- filepath: c:\xampp\htdocs\wt_project\checkout.php -->
<?php
session_start(); // Resume the session

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hotel_booking";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in to book a room.');</script>";
    header("Location: login.php");
    exit();
}

// Get the service ID from the query parameter
if (isset($_GET['service_id'])) {
    $service_id = intval($_GET['service_id']);

    // Fetch service details
    $sql = "SELECT * FROM services WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $service = $result->fetch_assoc();
    } else {
        echo "<script>alert('Invalid service selected.');</script>";
        header("Location: services.php");
        exit();
    }
} else {
    echo "<script>alert('No service selected.');</script>";
    header("Location: services.php");
    exit();
}

// Handle booking submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $checkin_date = $_POST['checkin_date'];
    $checkout_date = $_POST['checkout_date'];
    $guests = intval($_POST['guests']);
    $location = $service['location']; // Fetch location from the service details

    // Validate dates
    $today = date("Y-m-d");
    if ($checkin_date < $today || $checkout_date < $today) {
        echo "<script>
                alert('Check-in and check-out dates cannot be in the past.');
                window.location.href = 'checkout.php?service_id=$service_id';
              </script>";
        exit();
    }

    if ($checkout_date <= $checkin_date) {
        echo "<script>
                alert('Check-out date must be after the check-in date.');
                window.location.href = 'checkout.php?service_id=$service_id';
              </script>";
        exit();
    }

    // Check if the number of bookings exceeds the available quantity
    $sql = "SELECT COUNT(*) AS total_bookings FROM bookings 
            WHERE service_id = ? 
            AND (checkin_date <= ? AND checkout_date >= ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $service_id, $checkout_date, $checkin_date);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $total_bookings = $row['total_bookings'];

    if ($total_bookings >= $service['quantity']) {
        echo "<script>
                alert('Sorry, no rooms are available for the selected dates.');
                window.location.href = 'services.php';
              </script>";
        exit();
    }

    // Insert booking into the database
    $sql = "INSERT INTO bookings (user_id, service_id, location, checkin_date, checkout_date, guests) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisssi", $user_id, $service_id, $location, $checkin_date, $checkout_date, $guests);

    if ($stmt->execute()) {
        // Alert and redirect to the home page
        echo "<script>
                alert('Booking successful!');
                window.location.href = 'index.php';
              </script>";
        exit();
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Checkout | Rayal Park</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f0f4f8;
      margin: 0;
      padding: 0;
    }

    .booking__container {
      max-width: 800px;
      margin: 4rem auto;
      background: #fff;
      padding: 2rem;
      border-radius: 1rem;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .section__header {
      text-align: center;
      font-size: 2rem;
      margin-bottom: 2rem;
      color: #333;
    }

    .checkout__details {
      margin-bottom: 2rem;
      padding: 1rem;
      background: #f9f9f9;
      border-radius: 0.5rem;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .checkout__details h3 {
      margin-bottom: 1rem;
      color: #333;
    }

    .checkout__details p {
      margin: 0.5rem 0;
      color: #555;
    }

    .booking__form {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
    }

    .form-group {
      display: flex;
      flex-direction: column;
    }

    label {
      font-weight: bold;
      margin-bottom: 0.5rem;
      color: #555;
    }

    input[type="date"],
    input[type="number"] {
      padding: 0.5rem;
      border: 1px solid #ccc;
      border-radius: 0.5rem;
      font-size: 1rem;
    }

    .btn {
      padding: 0.75rem 1.5rem;
      background-color:rgb(225, 32, 203);
      color: white;
      border: none;
      border-radius: 0.5rem;
      cursor: pointer;
      font-weight: bold;
      font-size: 1rem;
      align-self: flex-start;
    }

    .btn:hover {
      background-color:rgb(155, 3, 152);
    }
  </style>
</head>
<body>
  <header class="header">
    <nav>
    <ul class="nav__links">
                    <li><a href="index.php" >Home</a></li>
                    <li><a href="services.php" >Services</a></li>
                
          
         
        </ul>
      </div>
    </nav>
  </header>

  <section class="booking__container">
    <h2 class="section__header">Book Your Stay</h2>

    <div class="checkout__details">
      <h3>Room Details</h3>
      <p><strong>Room Size:</strong> <?php echo $service['room_size']; ?></p>
      <p><strong>Description:</strong> <?php echo $service['description']; ?></p>
      <p><strong>Cost Per Night:</strong> $<?php echo $service['cost_per_night']; ?></p>
    </div>

    <form method="POST" action="checkout.php?service_id=<?php echo $service_id; ?>" class="booking__form">
      <div class="form-group">
        <label for="checkin_date">Check-in Date</label>
        <input type="date" id="checkin_date" name="checkin_date" required>
      </div>

      <div class="form-group">
        <label for="checkout_date">Check-out Date</label>
        <input type="date" id="checkout_date" name="checkout_date" required>
      </div>

      <div class="form-group">
        <label for="guests">Number of Guests</label>
        <input type="number" id="guests" name="guests" min="1" required>
      </div>

      <button type="submit" class="btn">Confirm Booking</button>
    </form>
  </section>
</body>
</html>
