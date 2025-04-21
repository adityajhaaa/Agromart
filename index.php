<?php
session_start();

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'agromart_db');

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === FALSE) {
    die("Error creating database: " . $conn->error);
}

$conn->select_db(DB_NAME);

$tables = [
    "CREATE TABLE IF NOT EXISTS users (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        address TEXT,
        city VARCHAR(100),
        state VARCHAR(100),
        zip_code VARCHAR(20),
        country VARCHAR(100),
        phone VARCHAR(20),
        role ENUM('admin', 'customer', 'farmer') NOT NULL DEFAULT 'customer',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS categories (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        image VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS products (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        sku VARCHAR(100) UNIQUE,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        sale_price DECIMAL(10,2),
        quantity INT NOT NULL DEFAULT 0,
        image VARCHAR(255),
        category_id INT,
        featured BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    )",
    
    "CREATE TABLE IF NOT EXISTS orders (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        shipping_address TEXT NOT NULL,
        billing_address TEXT NOT NULL,
        payment_method VARCHAR(50) NOT NULL,
        payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
        order_status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
        tracking_number VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )",
    
    "CREATE TABLE IF NOT EXISTS order_items (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )",
    
    "CREATE TABLE IF NOT EXISTS cart (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )",
    
    "CREATE TABLE IF NOT EXISTS reviews (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        product_id INT NOT NULL,
        user_id INT NOT NULL,
        rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
        comment TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )"
];

foreach ($tables as $table_query) {
    if ($conn->query($table_query) === FALSE) {
        die("Error creating table: " . $conn->error);
    }
}

$admin_check = $conn->query("SELECT * FROM users WHERE username = 'admin'");
if ($admin_check->num_rows == 0) {
    $admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
    $conn->query("INSERT INTO users (username, email, password, first_name, last_name, role) 
                VALUES ('admin', 'admin@agromart.com', '$admin_pass', 'Admin', 'User', 'admin')");
}

$categories = [
    ['name' => 'Seeds', 'description' => 'High-quality seeds for various crops', 'image' => 'images/categories/seeds.jpg'],
    ['name' => 'Fertilizers', 'description' => 'Organic and chemical fertilizers', 'image' => 'images/categories/fertilizers.jpg'],
    ['name' => 'Pesticides', 'description' => 'Pest control solutions', 'image' => 'images/categories/pesticides.jpg'],
    ['name' => 'Tools & Equipment', 'description' => 'Farming tools and equipment', 'image' => 'images/categories/tools.jpg'],
    ['name' => 'Irrigation', 'description' => 'Irrigation systems and accessories', 'image' => 'images/categories/irrigation.jpg']
];

foreach ($categories as $category) {
    $check = $conn->query("SELECT * FROM categories WHERE name = '{$category['name']}'");
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO categories (name, description, image) 
                    VALUES ('{$category['name']}', '{$category['description']}', '{$category['image']}')");
    }
}

$seeds_id = $conn->query("SELECT id FROM categories WHERE name = 'Seeds'")->fetch_assoc()['id'];
$fertilizers_id = $conn->query("SELECT id FROM categories WHERE name = 'Fertilizers'")->fetch_assoc()['id'];
$pesticides_id = $conn->query("SELECT id FROM categories WHERE name = 'Pesticides'")->fetch_assoc()['id'];
$tools_id = $conn->query("SELECT id FROM categories WHERE name = 'Tools & Equipment'")->fetch_assoc()['id'];
$irrigation_id = $conn->query("SELECT id FROM categories WHERE name = 'Irrigation'")->fetch_assoc()['id'];

$products = [
    // Seeds (8 products)
    [
        'name' => 'Corn Seeds',
        'description' => 'High-yielding corn seeds suitable for various climate conditions',
        'price' => 12.99,
        'sale_price' => 10.99,
        'quantity' => 500,
        'stock' => 500,
        'category_id' => $seeds_id,
        'image' => 'images/products/corn-seeds.jpg',
        'sku' => 'SEED-001',
        'featured' => true
    ],
    [
        'name' => 'Wheat Seeds',
        'description' => 'Premium wheat seeds with disease resistance',
        'price' => 15.99,
        'sale_price' => null,
        'quantity' => 450,
        'stock' => 450,
        'category_id' => $seeds_id,
        'image' => 'images/products/wheat-seeds.jpg',
        'sku' => 'SEED-002',
        'featured' => false
    ],
    [
        'name' => 'Rice Seeds',
        'description' => 'High-quality rice seeds for increased yield',
        'price' => 14.99,
        'sale_price' => 13.99,
        'quantity' => 400,
        'stock' => 400,
        'category_id' => $seeds_id,
        'image' => 'images/products/rice-seeds.jpg',
        'sku' => 'SEED-003',
        'featured' => true
    ],
    [
        'name' => 'Soybean Seeds',
        'description' => 'GMO-free soybean seeds for organic farming',
        'price' => 16.99,
        'sale_price' => null,
        'quantity' => 350,
        'stock' => 350,
        'category_id' => $seeds_id,
        'image' => 'images/products/soybean-seeds.jpg',
        'sku' => 'SEED-004',
        'featured' => false
    ],
    [
        'name' => 'Tomato Seeds',
        'description' => 'Heirloom tomato seeds for home gardening',
        'price' => 6.99,
        'sale_price' => 5.99,
        'quantity' => 600,
        'stock' => 600,
        'category_id' => $seeds_id,
        'image' => 'images/products/tomato-seeds.jpg',
        'sku' => 'SEED-005',
        'featured' => true
    ],
    [
        'name' => 'Cucumber Seeds',
        'description' => 'High-yielding cucumber seeds with disease resistance',
        'price' => 5.99,
        'sale_price' => null,
        'quantity' => 550,
        'stock' => 550,
        'category_id' => $seeds_id,
        'image' => 'images/products/cucumber-seeds.jpg',
        'sku' => 'SEED-006',
        'featured' => false
    ],
    [
        'name' => 'Carrot Seeds',
        'description' => 'Organic carrot seeds for home and commercial farming',
        'price' => 4.99,
        'sale_price' => null,
        'quantity' => 700,
        'stock' => 700,
        'category_id' => $seeds_id,
        'image' => 'images/products/carrot-seeds.jpg',
        'sku' => 'SEED-007',
        'featured' => false
    ],
    [
        'name' => 'Cotton Seeds',
        'description' => 'Premium cotton seeds for commercial farming',
        'price' => 19.99,
        'sale_price' => 17.99,
        'quantity' => 300,
        'stock' => 300,
        'category_id' => $seeds_id,
        'image' => 'images/products/cotton-seeds.jpg',
        'sku' => 'SEED-008',
        'featured' => true
    ],
    
    // Fertilizers (6 products)
    [
        'name' => 'Organic Compost',
        'description' => '10kg bag of organic compost for all plants',
        'price' => 24.99,
        'sale_price' => 21.99,
        'quantity' => 200,
        'stock' => 200,
        'category_id' => $fertilizers_id,
        'image' => 'images/products/organic-compost.jpg',
        'sku' => 'FERT-001',
        'featured' => true
    ],
    [
        'name' => 'NPK Fertilizer',
        'description' => '5kg balanced NPK fertilizer for general use',
        'price' => 29.99,
        'sale_price' => null,
        'quantity' => 180,
        'stock' => 180,
        'category_id' => $fertilizers_id,
        'image' => 'images/products/npk-fertilizer.jpg',
        'sku' => 'FERT-002',
        'featured' => false
    ],
    [
        'name' => 'Urea Fertilizer',
        'description' => '10kg urea fertilizer with high nitrogen content',
        'price' => 34.99,
        'sale_price' => 31.99,
        'quantity' => 150,
        'stock' => 150,
        'category_id' => $fertilizers_id,
        'image' => 'images/products/urea-fertilizer.jpg',
        'sku' => 'FERT-003',
        'featured' => false
    ],
    [
        'name' => 'DAP Fertilizer',
        'description' => '5kg DAP fertilizer for phosphorus supplementation',
        'price' => 32.99,
        'sale_price' => null,
        'quantity' => 170,
        'stock' => 170,
        'category_id' => $fertilizers_id,
        'image' => 'images/products/dap-fertilizer.jpg',
        'sku' => 'FERT-004',
        'featured' => false
    ],
    [
        'name' => 'Bone Meal',
        'description' => '2kg bone meal for phosphorus and calcium enrichment',
        'price' => 19.99,
        'sale_price' => 17.99,
        'quantity' => 220,
        'stock' => 220,
        'category_id' => $fertilizers_id,
        'image' => 'images/products/bone-meal.jpg',
        'sku' => 'FERT-005',
        'featured' => true
    ],
    [
        'name' => 'Vermicompost',
        'description' => '5kg vermicompost for organic farming',
        'price' => 26.99,
        'sale_price' => null,
        'quantity' => 190,
        'stock' => 190,
        'category_id' => $fertilizers_id,
        'image' => 'images/products/vermicompost.jpg',
        'sku' => 'FERT-006',
        'featured' => false
    ],
    
    // Pesticides (5 products)
    [
        'name' => 'Organic Neem Spray',
        'description' => '1L organic neem extract spray for natural pest control',
        'price' => 16.99,
        'sale_price' => 14.99,
        'quantity' => 250,
        'stock' => 250,
        'category_id' => $pesticides_id,
        'image' => 'images/products/neem-spray.jpg',
        'sku' => 'PEST-001',
        'featured' => true
    ],
    [
        'name' => 'Insecticide Spray',
        'description' => '500ml broad-spectrum insecticide for crop protection',
        'price' => 22.99,
        'sale_price' => null,
        'quantity' => 200,
        'stock' => 200,
        'category_id' => $pesticides_id,
        'image' => 'images/products/insecticide.jpg',
        'sku' => 'PEST-002',
        'featured' => false
    ],
    [
        'name' => 'Fungicide Solution',
        'description' => '500ml fungicide for preventing and treating plant diseases',
        'price' => 24.99,
        'sale_price' => 21.99,
        'quantity' => 180,
        'stock' => 180,
        'category_id' => $pesticides_id,
        'image' => 'images/products/fungicide.jpg',
        'sku' => 'PEST-003',
        'featured' => false
    ],
    [
        'name' => 'Weedicide',
        'description' => '1L selective weedicide for weed control',
        'price' => 26.99,
        'sale_price' => null,
        'quantity' => 170,
        'stock' => 170,
        'category_id' => $pesticides_id,
        'image' => 'images/products/weedicide.jpg',
        'sku' => 'PEST-004',
        'featured' => true
    ],
    [
        'name' => 'Rodent Control',
        'description' => 'Safe and effective rodent control solution for farms',
        'price' => 19.99,
        'sale_price' => 17.99,
        'quantity' => 220,
        'stock' => 220,
        'category_id' => $pesticides_id,
        'image' => 'images/products/rodent-control.jpg',
        'sku' => 'PEST-005',
        'featured' => false
    ],
    
    // Tools & Equipment (6 products)
    [
        'name' => 'Garden Hoe',
        'description' => 'Durable garden hoe for weeding and soil preparation',
        'price' => 29.99,
        'sale_price' => 26.99,
        'quantity' => 150,
        'stock' => 150,
        'category_id' => $tools_id,
        'image' => 'images/products/garden-hoe.jpg',
        'sku' => 'TOOL-001',
        'featured' => true
    ],
    [
        'name' => 'Pruning Shears',
        'description' => 'Professional pruning shears for plant maintenance',
        'price' => 24.99,
        'sale_price' => null,
        'quantity' => 180,
        'stock' => 180,
        'category_id' => $tools_id,
        'image' => 'images/products/pruning-shears.jpg',
        'sku' => 'TOOL-002',
        'featured' => false
    ],
    [
        'name' => 'Shovel',
        'description' => 'Heavy-duty farming shovel with ergonomic handle',
        'price' => 34.99,
        'sale_price' => 31.99,
        'quantity' => 120,
        'stock' => 120,
        'category_id' => $tools_id,
        'image' => 'images/products/shovel.jpg',
        'sku' => 'TOOL-003',
        'featured' => false
    ],
    [
        'name' => 'Trowel Set',
        'description' => '3-piece gardening trowel set for planting and transplanting',
        'price' => 19.99,
        'sale_price' => null,
        'quantity' => 200,
        'stock' => 200,
        'category_id' => $tools_id,
        'image' => 'images/products/trowel-set.jpg',
        'sku' => 'TOOL-004',
        'featured' => true
    ],
    [
        'name' => 'Garden Rake',
        'description' => 'Sturdy garden rake for soil leveling and debris collection',
        'price' => 27.99,
        'sale_price' => 24.99,
        'quantity' => 140,
        'stock' => 140,
        'category_id' => $tools_id,
        'image' => 'images/products/garden-rake.jpg',
        'sku' => 'TOOL-005',
        'featured' => false
    ],
    [
        'name' => 'Seed Planter',
        'description' => 'Manual seed planter for precise seed placement',
        'price' => 39.99,
        'sale_price' => null,
        'quantity' => 100,
        'stock' => 100,
        'category_id' => $tools_id,
        'image' => 'images/products/seed-planter.jpg',
        'sku' => 'TOOL-006',
        'featured' => true
    ],
    
    // Irrigation (5 products)
    [
        'name' => 'Drip Irrigation Kit',
        'description' => 'Complete drip irrigation kit for 1/4 acre',
        'price' => 89.99,
        'sale_price' => 79.99,
        'quantity' => 80,
        'stock' => 80,
        'category_id' => $irrigation_id,
        'image' => 'images/products/drip-irrigation.jpg',
        'sku' => 'IRRI-001',
        'featured' => true
    ],
    [
        'name' => 'Sprinkler System',
        'description' => 'Rotating sprinkler system for medium-sized fields',
        'price' => 69.99,
        'sale_price' => null,
        'quantity' => 70,
        'stock' => 70,
        'category_id' => $irrigation_id,
        'image' => 'images/products/sprinkler.jpg',
        'sku' => 'IRRI-002',
        'featured' => false
    ],
    [
        'name' => 'Water Pump',
        'description' => '1HP water pump for irrigation purposes',
        'price' => 129.99,
        'sale_price' => 119.99,
        'quantity' => 50,
        'stock' => 50,
        'category_id' => $irrigation_id,
        'image' => 'images/products/water-pump.jpg',
        'sku' => 'IRRI-003',
        'featured' => true
    ],
    [
        'name' => 'Garden Hose',
        'description' => '50ft durable garden hose with adjustable nozzle',
        'price' => 34.99,
        'sale_price' => null,
        'quantity' => 120,
        'stock' => 120,
        'category_id' => $irrigation_id,
        'image' => 'images/products/garden-hose.jpg',
        'sku' => 'IRRI-004',
        'featured' => false
    ],
    [
        'name' => 'Watering Can',
        'description' => '5L watering can for manual irrigation',
        'price' => 19.99,
        'sale_price' => 16.99,
        'quantity' => 150,
        'stock' => 150,
        'category_id' => $irrigation_id,
        'image' => 'images/products/watering-can.jpg',
        'sku' => 'IRRI-005',
        'featured' => false
    ]
];

foreach ($products as $product) {
    $check = $conn->query("SELECT * FROM products WHERE sku = '{$product['sku']}'");
    if ($check->num_rows == 0) {
        $sale_price = $product['sale_price'] ? $product['sale_price'] : "NULL";
        $featured = $product['featured'] ? 1 : 0;
        
        $sql = "INSERT INTO products (name, sku, description, price, sale_price, quantity, image, category_id, featured) 
                VALUES ('{$product['name']}', '{$product['sku']}', '{$product['description']}', {$product['price']}, 
                {$sale_price}, {$product['quantity']}, '{$product['image']}', {$product['category_id']}, {$featured})";
        
        if (!$conn->query($sql)) {
            die("Error inserting product: " . $conn->error);
        }
    }
}

$folders = [
    'images', 
    'images/categories', 
    'images/products', 
    'uploads',
    'css',
    'js'
];

foreach ($folders as $folder) {
    if (!file_exists($folder)) {
        mkdir($folder, 0755, true);
    }
}

function check_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
    return true;
}

function is_admin() {
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') {
        return true;
    }
    return false;
}

function get_products($limit = 10, $category = null, $featured = false) {
    global $conn;
    
    $sql = "SELECT p.*, c.name as category_name FROM products p
            LEFT JOIN categories c ON p.category_id = c.id";
    
    if ($category) {
        $sql .= " WHERE p.category_id = $category";
    }
    
    if ($featured) {
        if (strpos($sql, "WHERE") !== false) {
            $sql .= " AND p.featured = 1";
        } else {
            $sql .= " WHERE p.featured = 1";
        }
    }
    
    $sql .= " LIMIT $limit";
    
    $result = $conn->query($sql);
    $products = [];
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    
    return $products;
}

function get_categories() {
    global $conn;
    
    $sql = "SELECT * FROM categories";
    $result = $conn->query($sql);
    $categories = [];
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }
    
    return $categories;
}

// Get featured products and categories for the homepage
$featured_products = get_products(8, null, true);
$categories = get_categories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroMart - Your Agricultural Products Marketplace</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/8.4.5/swiper-bundle.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="header-top">
            <div class="container">
                <div class="text-center">Free Shipping on Orders Over $100 | Call us: +1-234-567-8900</div>
            </div>
        </div>
        
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
        
        <nav>
            <div class="container">
                <ul class="nav-menu">
                    <li><a href="index.php">Home</a></li>
                    <li class="dropdown">
                        <a href="products.php">Products <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <?php foreach($categories as $category): ?>
                                <li><a href="products.php?category=<?php echo $category['id']; ?>"><?php echo $category['name']; ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="blog.php">Blog</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="hero-content">
                <h1>Farm Fresh Products Direct to You</h1>
                <p>Discover quality agricultural products from trusted farmers and suppliers.</p>
                <a href="products.php" class="btn">Shop Now</a>
            </div>
        </section>

        <section class="featured-categories">
            <div class="container">
                <h2>Shop by Category</h2>
                <div class="categories-grid">
                    <?php foreach($categories as $category): ?>
                    <div class="category-card">
                        <img src="<?php echo htmlspecialchars($category['image']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>">
                        <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                        <a href="products.php?category=<?php echo $category['id']; ?>" class="btn">View Products</a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="featured-products">
            <div class="container">
                <h2>Featured Products</h2>
                <div class="products-grid">
                    <?php foreach($featured_products as $product): ?>
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
            </div>
        </section>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/8.4.5/swiper-bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>