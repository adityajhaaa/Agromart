<?php
require_once 'index.php';

// Sample blog posts data
$blog_posts = [
    [
        'id' => 1,
        'title' => 'Sustainable Farming Practices for 2024',
        'excerpt' => 'Discover the latest sustainable farming techniques that are revolutionizing agriculture.',
        'image' => 'images/blog/sustainable-farming.jpg',
        'date' => '2024-03-15',
        'author' => 'John Farmer'
    ],
    [
        'id' => 2,
        'title' => 'Organic vs. Conventional Farming: What You Need to Know',
        'excerpt' => 'A comprehensive guide to understanding the differences between organic and conventional farming methods.',
        'image' => 'images/blog/organic-farming.jpg',
        'date' => '2024-03-10',
        'author' => 'Sarah Green'
    ],
    [
        'id' => 3,
        'title' => 'The Future of Smart Agriculture',
        'excerpt' => 'How technology is transforming the way we farm and produce food.',
        'image' => 'images/blog/smart-agriculture.jpg',
        'date' => '2024-03-05',
        'author' => 'Mike Tech'
    ],
    [
        'id' => 4,
        'title' => 'Seasonal Planting Guide for Home Gardeners',
        'excerpt' => 'Everything you need to know about planting and maintaining your home garden throughout the seasons.',
        'image' => 'images/blog/seasonal-planting.jpg',
        'date' => '2024-02-28',
        'author' => 'Lisa Garden'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - AgroMart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="blog-page">
        <section class="blog-hero">
            <div class="container">
                <h1>AgroMart Blog</h1>
                <p>Latest news, tips, and insights from the world of agriculture</p>
            </div>
        </section>

        <section class="blog-content">
            <div class="container">
                <div class="blog-grid">
                    <?php foreach ($blog_posts as $post): ?>
                    <article class="blog-card">
                        <div class="blog-image">
                            <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                        </div>
                        <div class="blog-info">
                            <div class="blog-meta">
                                <span class="date"><i class="far fa-calendar"></i> <?php echo date('F j, Y', strtotime($post['date'])); ?></span>
                                <span class="author"><i class="far fa-user"></i> <?php echo htmlspecialchars($post['author']); ?></span>
                            </div>
                            <h2><?php echo htmlspecialchars($post['title']); ?></h2>
                            <p><?php echo htmlspecialchars($post['excerpt']); ?></p>
                            <a href="blog-post.php?id=<?php echo $post['id']; ?>" class="btn">Read More</a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html> 