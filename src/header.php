<?php
require_once 'includes/db.php';

$cartCount = getCartCount();
$current_page = basename($_SERVER['PHP_SELF']);
$user = getUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaCare - Your Trusted Pharmacy</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <i class="fas fa-prescription-bottle-alt"></i>
                <span>PharmaCare</span>
            </div>
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>
            <div class="nav-menu" id="navMenu">
                <a href="index.php" class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a>
                <a href="products.php" class="nav-link <?php echo $current_page == 'products.php' ? 'active' : ''; ?>">Products</a>
                <a href="about.php" class="nav-link <?php echo $current_page == 'about.php' ? 'active' : ''; ?>">About</a>
                <a href="contact.php" class="nav-link <?php echo $current_page == 'contact.php' ? 'active' : ''; ?>">Contact</a>
                <div class="nav-divider"></div>
                <a href="user/cart.php" class="nav-link cart-link">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count"><?php echo $cartCount; ?></span>
                </a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($user && $user['role'] == 'admin'): ?>
                        <a href="admin/dashboard.php" class="nav-link">Admin</a>
                    <?php endif; ?>
                    <a href="user/profile.php" class="nav-link">
                        <i class="fas fa-user-circle"></i>
                        <?php echo htmlspecialchars($user['name']); ?>
                    </a>
                    <a href="logout.php" class="btn btn-outline">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline">Login</a>
                    <a href="register.php" class="btn btn-primary">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>