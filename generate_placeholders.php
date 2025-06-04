<?php
// Function to create a placeholder image
function createPlaceholder($width, $height, $text, $filename) {
    $image = imagecreatetruecolor($width, $height);
    
    // Colors
    $bgColor = imagecolorallocate($image, 240, 240, 240);
    $textColor = imagecolorallocate($image, 8, 129, 120);
    
    // Fill background
    imagefill($image, 0, 0, $bgColor);
    
    // Add text
    $font = 5; // Built-in font
    $textWidth = imagefontwidth($font) * strlen($text);
    $textHeight = imagefontheight($font);
    
    // Center text
    $x = ($width - $textWidth) / 2;
    $y = ($height - $textHeight) / 2;
    
    // Draw text
    imagestring($image, $font, $x, $y, $text, $textColor);
    
    // Save image
    imagejpeg($image, $filename, 90);
    imagedestroy($image);
}

// Create placeholder images
createPlaceholder(400, 500, 'Upload Clothing Image', 'placeholder-clothing.jpg');
createPlaceholder(400, 500, 'Upload Model Image', 'placeholder-model.jpg');
createPlaceholder(400, 500, 'Result will appear here', 'placeholder-result.jpg');

echo "Placeholder images created successfully!";
?> 