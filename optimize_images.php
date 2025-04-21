<?php
header('Content-Type: text/html; charset=utf-8');
echo "<pre>";

// Check if GD library is installed
if (!extension_loaded('gd')) {
    die("GD library is not installed. Please install it to optimize images.");
}

// Function to optimize image
function optimizeImage($source, $destination, $quality = 80) {
    // Get image info
    $info = getimagesize($source);
    if (!$info) {
        echo "Invalid image: $source\n";
        return false;
    }

    // Create image from source
    switch ($info[2]) {
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($source);
            break;
        default:
            echo "Unsupported image type: $source\n";
            return false;
    }

    if (!$image) {
        echo "Failed to create image from: $source\n";
        return false;
    }

    // Optimize and save
    $success = false;
    switch ($info[2]) {
        case IMAGETYPE_JPEG:
            $success = imagejpeg($image, $destination, $quality);
            break;
        case IMAGETYPE_PNG:
            $success = imagepng($image, $destination, 9);
            break;
    }

    imagedestroy($image);
    
    if ($success) {
        echo "Optimized: $source -> $destination\n";
        return true;
    }
    
    echo "Failed to optimize: $source\n";
    return false;
}

// Process all product images
$product_dir = 'images/products';
if (!file_exists($product_dir)) {
    die("Product images directory not found: $product_dir");
}

$files = glob($product_dir . '/*.{jpg,jpeg,png}', GLOB_BRACE);
$total = count($files);
$optimized = 0;

echo "Found $total images to optimize\n";

foreach ($files as $file) {
    $filename = basename($file);
    $temp_file = $product_dir . '/temp_' . $filename;
    
    if (optimizeImage($file, $temp_file)) {
        // Replace original with optimized version
        if (unlink($file) && rename($temp_file, $file)) {
            $optimized++;
        } else {
            echo "Failed to replace original file: $file\n";
            unlink($temp_file);
        }
    }
}

echo "\nOptimization complete!\n";
echo "Successfully optimized $optimized out of $total images.\n";
echo "You can now refresh your product pages to see the optimized images.</pre>";
?> 