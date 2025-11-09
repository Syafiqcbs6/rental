<?php
session_start();
include("db_connect.php");

// Handle login feedback
$login_failed = isset($_GET['login']) && $_GET['login'] === 'failed';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RideWithPG</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&display=swap" rel="stylesheet">
  <style>
    section { scroll-margin-top: 80px; }

    /* Profile dropdown */
    .profile-container {
      display: <?php echo isset($_SESSION["username"]) ? "flex" : "none"; ?>;
      align-items: center;
      gap: 8px;
      position: relative;
      cursor: pointer;
    }
    .profile-pic {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      border: 2px solid #ff8533;
      background: url('profile.png') center/cover no-repeat;
    }
    .profile-name {
      color: #fff;
      font-weight: 600;
      font-size: 0.95rem;
    }
    .dropdown-menu {
      display: none;
      position: absolute;
      top: 48px;
      right: 0;
      background: rgba(20, 30, 45, 0.95);
      border-radius: 10px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.4);
      overflow: hidden;
      min-width: 140px;
      z-index: 999;
    }
    .dropdown-menu a {
      display: block;
      padding: 10px 16px;
      color: #fff;
      text-decoration: none;
      font-size: 0.9rem;
      transition: background 0.3s;
    }
    .dropdown-menu a:hover { background: rgba(255,133,51,0.2); }
    .show-menu { display: block; animation: fadeIn 0.3s ease; }
    @keyframes fadeIn { from { opacity:0; transform: translateY(-5px);} to {opacity:1; transform:translateY(0);} }

    /* -------------------- Map -------------------- */
    .map {
      padding: 60px 20px;
      text-align: center;
      background: linear-gradient(to right, #0a0f1c, #121c2a);
      color: #fff;
    }
    .map h2 {
      font-family: 'Bricolage Grotesque', sans-serif;
      font-size: 2rem;
      margin-bottom: 30px;
      color: #ffb37a;
    }
    .map-card {
      display: inline-block;
      background: rgba(6,12,20,0.7);
      padding: 24px;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.6);
      max-width: 960px;
      width: 100%;
      text-align: left;
      margin: 0 auto;
      transition: transform 0.3s ease, background 0.3s ease;
    }
    .map-card:hover {
      transform: translateY(-5px);
      background: rgba(255,255,255,0.05);
    }
    .map-card h3 {
      color: #ff8533;
      margin-bottom: 12px;
    }
    .map-card p {
      color: #cfe7ff;
      margin-bottom: 16px;
    }
    .map iframe {
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.6);
      width: 100%;
      height: 360px;
    }
    @media (max-width: 600px) {
      .map-card {
        padding: 20px;
      }
      .map iframe {
        height: 280px;
      }
    }
  </style>
</head>
<body>

<header>
  <h1>RideWithPG</h1>
  <nav>
    <ul>
      <li><a href="#home">Home</a></li>
      <li><a href="#cars">Cars</a></li>
      <li><a href="#contact">Contact Us</a></li>
      <li><a href="#faq">FAQ</a></li>
      <li><a href="#map">Find Us</a></li>

      <?php if(!isset($_SESSION["username"])): ?>
        <li><a href="javascript:void(0)" id="signinBtn">Sign In</a></li>
        <li><a href="javascript:void(0)" id="signupBtn">Sign Up</a></li>
      <?php else: ?>
        <?php if($_SESSION["role"] === "admin"): ?>
          <li><a href="admin/index.php">Dashboard</a></li>
        <?php endif; ?>
        <li><a href="user_bookings.php">My Bookings</a></li>
      <?php endif; ?>
    </ul>

    <?php if(isset($_SESSION["username"])): ?>
      <div class="profile-container" id="profileContainer">
        <span class="profile-name" id="profileName"><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
        <div class="profile-pic" style="background: url('<?php 
          if(isset($_SESSION['profile_pic']) && $_SESSION['profile_pic'] != null){
              echo 'data:image/jpeg;base64,'.base64_encode($_SESSION['profile_pic']); 
          } else { echo 'profile.png'; } 
        ?>') center/cover no-repeat;"></div>

        <div class="dropdown-menu" id="dropdownMenu">
          <a href="logout.php">Log Out</a>
        </div>
      </div>
    <?php endif; ?>
  </nav>
</header>

<section id="home" class="mainpic">
  <h2>Let's rent your dream car today!</h2>
  <p>Affordable. Reliable. Fast Service.</p>
  <a href="#cars" class="btn">Browse Cars</a>
</section>

<section id="cars" class="cars">
  <h2>Available Cars</h2>
  <div class="car-list">
  <?php
  $sql = "SELECT * FROM cars WHERE availability_status = 'available'";
  $result = mysqli_query($conn, $sql);

  if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
      echo "
      <div class='car-card'>
        <img src='uploads/{$row['image']}' alt='{$row['model']}'>
        <h3>{$row['model']}</h3>
        <p>{$row['brand']}</p>
        <p><strong>RM {$row['price_per_day']}/day</strong></p>
        <a href='booking.php?car_id={$row['id']}' class='btn'>Book Now</a>
      </div>
      ";
    }
  } else {
    echo '<p>No cars available right now.</p>';
  }
  ?>
  </div>
</section>

<section id="contact" class="contact">
  <h2>Contact Us</h2>
  <p>Email: shadowcreeper@rally.com</p>
  <p>Phone: +60 10-336 2960</p>
</section>

<section id="faq" class="faq">
  <h2>Frequently Asked Questions</h2>
  <div class="faq-item"><h4>What documents do I need?</h4><p>License and ID/passport.</p></div>
  <div class="faq-item"><h4>Is there a deposit?</h4><p>Yes, refundable deposit depends on car type.</p></div>
  <div class="faq-item"><h4>Can I extend my rental?</h4><p>Yes, contact support to extend your booking.</p></div>
  <div class="faq-item"><h4>What happen if I crashed?</h4><p>You pay la.</p></div>
  <div class="faq-item"><h4>Do I have to pay for the gas?</h4><p>If you don't fill, you don't move.</p></div>
</section>

<!-- Modern Map Section -->
<section id="map" class="map">
  <h2>Find Us</h2>
  <div class="map-card">
    <h3>Our Base: TRX</h3>
    <p>Come visit us at our main office located in the heart of TRX, Kuala Lumpur.</p>
    <iframe 
      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d484635.55449570005!2d101.64075494999999!3d3.274088850000002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc4135c818aa17%3A0xea7ab3b0099a86ac!2sGombak%20District%2C%20Selangor!5e1!3m2!1sen!2smy!4v1759002903391!5m2!1sen!2smy" 
      allowfullscreen="" 
      loading="lazy" 
      referrerpolicy="no-referrer-when-downgrade">
    </iframe>
  </div>
</section>

<footer>
  <p>&copy; 2025 RideWithPG SDN BHD. All rights reserved.</p>
</footer>

<?php if(!isset($_SESSION["username"])): ?>
<div id="signinModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Sign In</h2>
    <form action="login.php" method="POST" id="signinForm">
      <label for="signin-username">Username:</label>
      <input type="text" name="username" id="signin-username" required>
      <label for="signin-password">Password:</label>
      <input type="password" name="password" id="signin-password" required>
      <button type="submit">Login</button>
    </form>
    <p class="switch-form">Don’t have an account? <a href="#" id="openSignup">Sign up here</a></p>
  </div>
</div>

<div id="signupModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Sign Up</h2>
    <form id="signupForm" action="signup.php" method="POST">
      <label for="signup-username">Username:</label>
      <input type="text" id="signup-username" name="username" required>
      <label for="signup-email">Email:</label>
      <input type="email" id="signup-email" name="email" required>
      <label for="signup-password">Password:</label>
      <input type="password" id="signup-password" name="password" required>
      <button type="submit">Register</button>
    </form>
  </div>
</div>
<?php endif; ?>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const profileContainer = document.getElementById("profileContainer");
  const dropdownMenu = document.getElementById("dropdownMenu");

  if(profileContainer)
    profileContainer.addEventListener("click", () => dropdownMenu.classList.toggle("show-menu"));

  const signinModal = document.getElementById("signinModal");
  const signupModal = document.getElementById("signupModal");

  document.querySelectorAll(".modal .close").forEach(btn => btn.onclick = () => btn.closest(".modal").style.display="none");

  document.getElementById("signinBtn")?.addEventListener("click",()=>signinModal.style.display="flex");
  document.getElementById("signupBtn")?.addEventListener("click",()=>signupModal.style.display="flex");
  document.getElementById("openSignup")?.addEventListener("click", e=>{
    e.preventDefault();
    signinModal.style.display="none";
    signupModal.style.display="flex";
  });

  <?php if($login_failed): ?>
    signinModal.style.display="flex";
    alert("Login failed. Please check your username and password.");
  <?php endif; ?>
});
</script>
</body>
</html>
