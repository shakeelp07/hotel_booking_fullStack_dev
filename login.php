<!-- filepath: c:\xampp\htdocs\wt_project\login.php -->
<?php
// Start session
session_start();

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

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if user exists
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Store user ID in session
            $_SESSION['user_id'] = $user['id'];
            echo "<script>alert('Login successful!');</script>";
            // Redirect to the homepage or dashboard
            header("Location: index.php");
            exit();
        } else {
            echo "<script>alert('Invalid password.');</script>";
        }
    } else {
        echo "<script>alert('No user found with this email.');</script>";
    }

    $stmt->close();
}

// Handle signup form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into the database
    $sql = "INSERT INTO users (email, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $hashedPassword);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! You can now log in.');</script>";
        // Redirect to login page
        header("Location: login.php");
        exit();
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login & Signup | Rayal Park</title>
  <style>
    /* Your existing CSS styles */
    :root {
      --theme-signup: #e82574;
      --theme-signup-darken: rgb(192, 33, 97);
      --theme-signup-background: #2C3034;
      --theme-login: #0c0a09;
      --theme-login-darken: rgb(0, 0, 0);
      --theme-login-background: #f9f9f9;
      --theme-dark: #212121;
      --theme-light: #e3e3e3;
      --font-default: 'Roboto', sans-serif;
    }

    body {
      margin: 0;
      height: 100%;
      overflow: hidden;
      width: 100%;
      box-sizing: border-box;
      font-family: var(--font-default);
    }

    .backRight {
      position: absolute;
      right: 0;
      width: 50%;
      height: 100%;
      background: var(--theme-signup);
    }

    .backLeft {
      position: absolute;
      left: 0;
      width: 50%;
      height: 100%;
      background: var(--theme-login);
    }

    #back {
      width: 100%;
      height: 100%;
      position: absolute;
      z-index: -999;
    }

    .canvas-back {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 10;
    }

    #slideBox {
      width: 50%;
      max-height: 100%;
      height: 100%;
      overflow: hidden;
      margin-left: 50%;
      position: absolute;
      box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
    }

    .topLayer {
      width: 200%;
      height: 100%;
      position: relative;
      left: -100%;
    }

    label {
      font-size: 0.8em;
      text-transform: uppercase;
    }

    input {
      background-color: transparent;
      border: 0;
      outline: 0;
      font-size: 1em;
      padding: 8px 1px;
      margin-top: 0.1em;
    }

    .left, .right {
      width: 50%;
      height: 100%;
      overflow: scroll;
      position: absolute;
    }

    .left {
      background: var(--theme-signup-background);
      left: 0;
    }

    .left label { color: var(--theme-light); }
    .left input {
      border-bottom: 1px solid var(--theme-light);
      color: var(--theme-light);
    }
    .left input:focus, .left input:active {
      border-color: var(--theme-signup);
      color: var(--theme-signup);
    }

    .right {
      background: var(--theme-login-background);
      right: 0;
    }

    .right label { color: var(--theme-dark); }
    .right input {
      border-bottom: 1px solid var(--theme-dark);
    }
    .right input:focus, .right input:active {
      border-color: var(--theme-login);
    }

    .content {
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-height: 100%;
      width: 80%;
      margin: 0 auto;
    }

    .content h2 {
      font-weight: 300;
      font-size: 2.6em;
      margin: 0.2em 0 0.1em;
    }

    .left .content h2 {
      color: var(--theme-signup);
    }

    .right .content h2 {
      color: var(--theme-login);
    }

    .form-element {
      margin: 1.6em 0;
    }

    .form-stack {
      display: flex;
      flex-direction: column;
    }

    .checkbox {
      -webkit-appearance: none;
      outline: none;
      background-color: var(--theme-light);
      border: 1px solid var(--theme-light);
      box-shadow: 0 1px 2px rgba(0,0,0,0.05), inset 0px -15px 10px -12px rgba(0,0,0,0.05);
      padding: 12px;
      border-radius: 4px;
      display: inline-block;
      position: relative;
    }

    .checkbox:checked:after {
      content: '\2713';
      color: var(--theme-signup);
      font-size: 1.4em;
      font-weight: 900;
      position: absolute;
      top: -4px;
      left: 4px;
    }

    .form-checkbox {
      display: flex;
      align-items: center;
    }

    .form-checkbox label {
      margin: 0 6px 0;
      font-size: 0.72em;
    }

    button {
      padding: 0.8em 1.2em;
      margin: 0 10px 0 0;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 1em;
      color: #fff;
      border-radius: 3px;
      border: 0;
      outline: 0;
      transition: all 0.25s;
    }

    button.signup {
      background: var(--theme-signup);
    }

    button.login {
      background: var(--theme-login);
    }

    button.off {
      background: none;
      box-shadow: none;
      color: inherit;
    }

    @media only screen and (max-width: 768px) {
      #slideBox {
        width: 80%;
        margin-left: 20%;
      }
    }
  </style>
</head>
<body>

<div id="back">
  <canvas id="canvas" class="canvas-back"></canvas>
  <div class="backRight"></div>
  <div class="backLeft"></div>
</div>

<div id="slideBox">
  <div class="topLayer">
    <!-- Signup Form -->
    <div class="left">
      <div class="content">
        <h2>Sign Up</h2>
        <form action="login.php" method="POST">
          <div class="form-element form-stack">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" required>
          </div>
          <div class="form-element form-stack">
            <label for="password-signup" class="form-label">Password</label>
            <input id="password-signup" type="password" name="password" required>
          </div>
          <div class="form-element form-checkbox">
            <input id="confirm-terms" type="checkbox" name="confirm" value="yes" class="checkbox" required>
            <label for="confirm-terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
          </div>
          <div class="form-element form-submit">
            <button id="signUp" class="signup" type="submit" name="signup">Sign up</button>
            <button id="goLeft" class="signup off">Log In</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Login Form -->
    <div class="right">
      <div class="content">
        <h2>Login</h2>
        <form action="login.php" method="POST">
          <div class="form-element form-stack">
            <label for="email-login" class="form-label">Email</label>
            <input id="email-login" type="email" name="email" required>
          </div>
          <div class="form-element form-stack">
            <label for="password-login" class="form-label">Password</label>
            <input id="password-login" type="password" name="password" required>
          </div>
          <div class="form-element form-submit">
            <button id="logIn" class="login" type="submit" name="login">Log In</button>
            <button id="goRight" class="login off">Sign Up</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Paper.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/paper.js/0.12.15/paper-full.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
  $(document).ready(function () {
    $('#goRight').on('click', function () {
      $('#slideBox').animate({ 'marginLeft': '0' });
      $('.topLayer').animate({ 'marginLeft': '100%' });
    });
    $('#goLeft').on('click', function () {
      if (window.innerWidth > 769) {
        $('#slideBox').animate({ 'marginLeft': '50%' });
      } else {
        $('#slideBox').animate({ 'marginLeft': '20%' });
      }
      $('.topLayer').animate({ 'marginLeft': '0' });
    });
  });

  paper.install(window);
  paper.setup(document.getElementById("canvas"));

  var canvasWidth, canvasHeight, canvasMiddleX, canvasMiddleY;
  var shapeGroup = new Group();
  var positionArray = [];

  function getCanvasBounds() {
    canvasWidth = view.size.width;
    canvasHeight = view.size.height;
    canvasMiddleX = canvasWidth / 2;
    canvasMiddleY = canvasHeight / 2;

    positionArray = [
      { x: (canvasMiddleX - 50) + (canvasMiddleX / 2), y: 150 },
      { x: 200, y: canvasMiddleY },
      { x: canvasWidth - 130, y: canvasHeight - 75 },
      { x: 0, y: canvasMiddleY + 100 },
      { x: (canvasMiddleX / 2) + 100, y: 100 },
      { x: canvasMiddleX + 80, y: canvasHeight - 50 },
      { x: canvasWidth + 60, y: canvasMiddleY - 50 },
      { x: canvasMiddleX + 100, y: canvasMiddleY + 100 }
    ];
  }

  function initializeShapes() {
    getCanvasBounds();
    var shapePathData = [
      'M231,352l445-156L600,0L452,54L331,3L0,48L231,352',
      'M0,0l64,219L29,343l535,30L478,37l-133,4L0,0z',
      'M0,65l16,138l96,107l270-2L470,0L337,4L0,65z',
      'M333,0L0,94l64,219L29,437l570-151l-196-42L333,0',
      'M331.9,3.6l-331,45l231,304l445-156l-76-196l-148,54L331.9,3.6z',
      'M389,352l92-113l195-43l0,0l0,0L445,48l-80,1L122.7,0L0,275.2L162,297L389,352',
      'M 50 100 L 300 150 L 550 50 L 750 300 L 500 250 L 300 450 L 50 100',
      'M 700 350 L 500 350 L 700 500 L 400 400 L 200 450 L 250 350 L 100 300 L 150 50 L 350 100 L 250 150 L 450 150 L 400 50 L 550 150 L 350 250 L 650 150 L 650 50 L 700 150 L 600 250 L 750 250 L 650 300 L 700 350'
    ];

    for (var i = 0; i < shapePathData.length; i++) {
      var headerShape = new Path({
        strokeColor: 'rgba(255, 255, 255, 0.5)',
        strokeWidth: 2,
        parent: shapeGroup,
      });
      headerShape.pathData = shapePathData[i];
      headerShape.scale(2);
      headerShape.position = positionArray[i];
    }
  }

  initializeShapes();

  view.onFrame = function (event) {
    if (event.count % 4 === 0) {
      for (var i = 0; i < shapeGroup.children.length; i++) {
        shapeGroup.children[i].rotate(i % 2 === 0 ? -0.1 : 0.1);
      }
    }
  };

  view.onResize = function () {
    getCanvasBounds();
    for (var i = 0; i < shapeGroup.children.length; i++) {
      shapeGroup.children[i].position = positionArray[i];
    }
    shapeGroup.children[3].opacity = canvasWidth < 700 ? 0 : 1;
    shapeGroup.children[2].opacity = canvasWidth < 700 ? 0 : 1;
    shapeGroup.children[5].opacity = canvasWidth < 700 ? 0 : 1;
  };
</script>

</body>
</html>