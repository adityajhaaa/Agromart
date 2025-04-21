<?php
// Create directory for product images if it doesn't exist
$imageDir = 'images/products';
if (!file_exists($imageDir)) {
    mkdir($imageDir, 0777, true);
}

// Function to create a test image
function createTestImage($text, $filepath) {
    // Check if GD is installed
    if (!extension_loaded('gd')) {
        // If GD is not available, create a simple placeholder image
        $placeholder_url = "https://via.placeholder.com/300x300.png?text=" . urlencode($text);
        file_put_contents($filepath, file_get_contents($placeholder_url));
        return true;
    }

    try {
        // Create image
        $width = 300;
        $height = 300;
        $image = imagecreatetruecolor($width, $height);
        
        if ($image === false) {
            throw new Exception("Failed to create image");
        }

        // Colors
        $bg = imagecolorallocate($image, 240, 240, 240);
        $textColor = imagecolorallocate($image, 50, 50, 50);

        // Fill background
        imagefilledrectangle($image, 0, 0, $width, $height, $bg);

        // Add text
        $fontSize = 5;
        $textWidth = strlen($text) * imagefontwidth($fontSize);
        $textHeight = imagefontheight($fontSize);
        
        $x = ($width - $textWidth) / 2;
        $y = ($height - $textHeight) / 2;
        
        imagestring($image, $fontSize, $x, $y, $text, $textColor);

        // Save image
        imagepng($image, $filepath);
        imagedestroy($image);
        
        return true;
    } catch (Exception $e) {
        error_log("Error creating image: " . $e->getMessage());
        return false;
    }
}

// Delete existing images
array_map('unlink', glob("$imageDir/*"));

// List of products with their image filenames
$products = [
    ['name' => 'Rice', 'image' => 'rice.png'],
    ['name' => 'Corn', 'image' => 'corn.png'],
    ['name' => 'Wheat', 'image' => 'wheat.png'],
    ['name' => 'Vegetables', 'image' => 'vegetables.png'],
    ['name' => 'Fruits', 'image' => 'fruits.png']
];

// Create test images for each product
foreach ($products as $product) {
    $filepath = $imageDir . '/' . $product['image'];
    if (createTestImage($product['name'], $filepath)) {
        echo "Created image for {$product['name']}\n";
    } else {
        echo "Failed to create image for {$product['name']}\n";
    }
}

echo "Image generation complete!\n";
?> 