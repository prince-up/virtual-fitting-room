<?php
// Create directory if it doesn't exist
$dir = 'assets/images';
if (!file_exists($dir)) {
    mkdir($dir, 0777, true);
}

// List of required images
$required_images = [
    'blue-tshirt.png',
    'white-blouse.png',
    'striped-polo.png',
    'vneck-sweater.png',
    'black-jeans.png',
    'blue-jeans.png',
    'black-pants.png',
    'red-dress.png',
    'floral-dress.png',
    'black-dress.png',
    'leather-jacket.png',
    'denim-jacket.png',
    'bomber-jacket.png',
    'silk-kurta.png',
    'anarkali.png',
    'abstract-shirt.png',
    'geometric-dress.png',
    'tie-dye-hoodie.png'
];

echo "Required images for the virtual fitting room:\n\n";
foreach ($required_images as $image) {
    echo "- $image\n";
}

echo "\nInstructions:\n";
echo "1. Create a folder structure:\n";
echo "   assets/\n";
echo "   └── images/\n\n";
echo "2. Add your clothing images to the assets/images directory\n";
echo "3. Make sure the image filenames match exactly with the list above\n";
echo "4. Image requirements:\n";
echo "   - Format: PNG\n";
echo "   - Size: 800x1000 pixels (recommended)\n";
echo "   - Aspect ratio: 4:5 (portrait)\n";
echo "   - File size: Under 500KB per image\n";
echo "   - Background: White or transparent\n";
echo "   - Quality: High resolution, clear product shots\n\n";
echo "5. After adding the images, run the sample_clothing.sql script to populate the database\n";
?> 