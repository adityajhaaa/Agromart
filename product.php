<?php
session_start();
require_once 'config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$product_id = intval($_GET['id']);
$sql = "SELECT p.*, c.name as category_name FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit;
}

$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - AgroMart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <!-- Header content same as index.php -->
        <div class="container">
            <div class="header-main">
                <div class="logo">
                    <a href="index.php">AgroMart</a>
                </div>
                
                <div class="search-bar">
                    <form action="products.php" method="GET">
                        <input type="text" name="search" placeholder="Search for products...">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                
                <div class="header-right">
                    <div class="user-actions">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <a href="account.php">My Account</a> |
                            <a href="logout.php">Logout</a>
                        <?php else: ?>
                            <a href="login.php">Login</a> |
                            <a href="register.php">Register</a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="cart-icon">
                        <a href="cart.php">
                            <i class="fas fa-shopping-cart"></i>
                            <?php 
                            $cart_count = 0;
                            if(isset($_SESSION['user_id'])) {
                                $user_id = $_SESSION['user_id'];
                                $cart_result = $conn->query("SELECT SUM(quantity) as count FROM cart WHERE user_id = $user_id");
                                if($cart_result && $cart_result->num_rows > 0) {
                                    $cart_count = $cart_result->fetch_assoc()['count'] ?: 0;
                                }
                            }
                            ?>
                            <span class="cart-count"><?php echo $cart_count; ?></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="product-detail">
                <div class="product-image">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                <div class="product-info">
                    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                    <p class="category">Category: <?php echo htmlspecialchars($product['category_name']); ?></p>
                    <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>
                    <div class="price-box">
                        <?php if ($product['sale_price']): ?>
                            <p class="original-price">$<?php echo number_format($product['price'], 2); ?></p>
                            <p class="sale-price">$<?php echo number_format($product['sale_price'], 2); ?></p>
                        <?php else: ?>
                            <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="stock-status">
                        <?php if ($product['quantity'] > 0): ?>
                            <p class="in-stock">In Stock (<?php echo $product['quantity']; ?> available)</p>
                        <?php else: ?>
                            <p class="out-of-stock">Out of Stock</p>
                        <?php endif; ?>
                    </div>
                    <?php if ($product['quantity'] > 0): ?>
                        <form action="cart.php" method="POST" class="add-to-cart-form">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <div class="quantity-selector">
                                <label for="quantity">Quantity:</label>
                                <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?php echo $product['quantity']; ?>">
                            </div>
                            <button type="submit" class="btn add-to-cart">Add to Cart</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>About Us</h3>
                    <p>AgroMart is your trusted source for agricultural products.</p>
                </div>
                <div class="footer-section">
                    <h3>Contact</h3>
                    <p>Email: info@agromart.com</p>
                    <p>Phone: +1-234-567-8900</p>
                </div>
                <div class="footer-section">
                    <h3>Follow Us</h3>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> AgroMart. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html> 