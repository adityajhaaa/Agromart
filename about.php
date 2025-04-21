<?php
require_once 'index.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - AgroMart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="about-page">
        <section class="about-hero">
            <div class="container">
                <h1>About AgroMart</h1>
                <p>Your Trusted Partner in Agricultural Products</p>
            </div>
        </section>

        <section class="about-content">
            <div class="container">
                <div class="about-grid">
                    <div class="about-text">
                        <h2>Our Story</h2>
                        <p>AgroMart was founded with a simple mission: to connect farmers and agricultural suppliers with customers who value quality and sustainability. We believe in supporting local agriculture while providing access to the best farming products worldwide.</p>
                        
                        <h2>Our Mission</h2>
                        <p>To revolutionize the agricultural marketplace by providing a seamless platform for buying and selling high-quality farming products, while promoting sustainable agricultural practices.</p>
                        
                        <h2>Our Values</h2>
                        <ul>
                            <li>Quality Assurance</li>
                            <li>Sustainable Practices</li>
                            <li>Customer Satisfaction</li>
                            <li>Support for Local Farmers</li>
                            <li>Innovation in Agriculture</li>
                        </ul>
                    </div>
                    
                    <div class="about-stats">
                        <div class="stat-card">
                            <i class="fas fa-users"></i>
                            <h3>10,000+</h3>
                            <p>Happy Customers</p>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-truck"></i>
                            <h3>5,000+</h3>
                            <p>Products Delivered</p>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-store"></i>
                            <h3>100+</h3>
                            <p>Verified Suppliers</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html> 