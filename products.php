<?php
session_start();
require_once 'config.php';

$category_id = isset($_GET['category']) ? intval($_GET['category']) : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT p.*, c.name as category_name FROM products p
        LEFT JOIN categories c ON p.category_id = c.id";

$where_conditions = [];
$params = [];
$param_types = '';

if ($category_id) {
    $where_conditions[] = "p.category_id = ?";
    $params[] = $category_id;
    $param_types .= 'i';
}

if ($search) {
    $where_conditions[] = "(p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $param_types .= 'ss';
}

if (!empty($where_conditions)) {
    $sql .= " WHERE " . implode(" AND ", $where_conditions);
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$products = [];

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Get all categories for the sidebar
$categories = [];
$cat_result = $conn->query("SELECT * FROM categories");
while ($row = $cat_result->fetch_assoc()) {
    $categories[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - AgroMart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-main">
                <div class="logo">
                    <a href="index.php">AgroMart</a>
                </div>
                
                <div class="search-bar">
                    <form action="products.php" method="GET">
                        <input type="text" name="search" placeholder="Search for products..." value="<?php echo htmlspecialchars($search); ?>">
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
            <div class="products-page">
                <aside class="sidebar">
                    <h3>Categories</h3>
                    <ul class="category-list">
                        <li><a href="products.php" <?php echo !$category_id ? 'class="active"' : ''; ?>>All Products</a></li>
                        <?php foreach($categories as $category): ?>
                            <li>
                                <a href="products.php?category=<?php echo $category['id']; ?>" 
                                   <?php echo $category_id == $category['id'] ? 'class="active"' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </aside>

                <div class="products-content">
                    <?php if ($search): ?>
                        <h2>Search Results for "<?php echo htmlspecialchars($search); ?>"</h2>
                    <?php elseif ($category_id): ?>
                        <h2><?php echo htmlspecialchars($categories[array_search($category_id, array_column($categories, 'id'))]['name']); ?></h2>
                    <?php else: ?>
                        <h2>All Products</h2>
                    <?php endif; ?>

                    <?php if (empty($products)): ?>
                        <p class="no-products">No products found.</p>
                    <?php else: ?>
                        <div class="products-grid">
                            <?php foreach($products as $product): ?>
                                <div class="product-card">
                                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    <div class="product-info">
                                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                        <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn">View Details</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
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