<?php
// Prevent HTML output
ob_start();

// Include only the database connection part of index.php
require_once 'index.php';

// Clear any output
ob_end_clean();

// Update product images
$products = [
    // Seeds
    'SEED-001' => 'images/products/corn-seeds.jpg',
    'SEED-002' => 'images/products/wheat-seeds.jpg',
    'SEED-003' => 'images/products/rice-seeds.jpg',
    'SEED-004' => 'images/products/soybean-seeds.jpg',
    'SEED-005' => 'images/products/tomato-seeds.jpg',
    'SEED-006' => 'images/products/cucumber-seeds.jpg',
    'SEED-007' => 'images/products/carrot-seeds.jpg',
    'SEED-008' => 'images/products/cotton-seeds.jpg',
    
    // Fertilizers
    'FERT-001' => 'images/products/organic-compost.jpg',
    'FERT-002' => 'images/products/npk-fertilizer.jpg',
    'FERT-003' => 'images/products/urea-fertilizer.jpg',
    'FERT-004' => 'images/products/dap-fertilizer.jpg',
    'FERT-005' => 'images/products/bone-meal.jpg',
    'FERT-006' => 'images/products/vermicompost.jpg',
    
    // Pesticides
    'PEST-001' => 'images/products/neem-spray.jpg',
    'PEST-002' => 'images/products/insecticide.jpg',
    'PEST-003' => 'images/products/fungicide.jpg',
    'PEST-004' => 'images/products/weedicide.jpg',
    'PEST-005' => 'images/products/rodent-control.jpg',
    
    // Tools
    'TOOL-001' => 'images/products/garden-hoe.jpg',
    'TOOL-002' => 'images/products/pruning-shears.jpg',
    'TOOL-003' => 'images/products/shovel.jpg',
    'TOOL-004' => 'images/products/trowel-set.jpg',
    'TOOL-005' => 'images/products/garden-rake.jpg',
    'TOOL-006' => 'images/products/seed-planter.jpg',
    
    // Irrigation
    'IRRI-001' => 'images/products/drip-irrigation.jpg',
    'IRRI-002' => 'images/products/sprinkler.jpg',
    'IRRI-003' => 'images/products/water-pump.jpg',
    'IRRI-004' => 'images/products/garden-hose.jpg',
    'IRRI-005' => 'images/products/watering-can.jpg'
];

// Update category images
$categories = [
    'Seeds' => 'images/categories/seeds.jpg',
    'Fertilizers' => 'images/categories/fertilizers.jpg',
    'Pesticides' => 'images/categories/pesticides.jpg',
    'Tools & Equipment' => 'images/categories/tools.jpg',
    'Irrigation' => 'images/categories/irrigation.jpg'
];

// Update product images
foreach ($products as $sku => $image_path) {
    $sql = "UPDATE products SET image = ? WHERE sku = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $image_path, $sku);
    if ($stmt->execute()) {
        echo "Updated image for product SKU: $sku\n";
    } else {
        echo "Failed to update image for product SKU: $sku\n";
    }
    $stmt->close();
}

// Update category images
foreach ($categories as $name => $image_path) {
    $sql = "UPDATE categories SET image = ? WHERE name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $image_path, $name);
    if ($stmt->execute()) {
        echo "Updated image for category: $name\n";
    } else {
        echo "Failed to update image for category: $name\n";
    }
    $stmt->close();
}

echo "\nImage path update complete!\n";
echo "You can now refresh your pages to see the updated images.\n";
?> 