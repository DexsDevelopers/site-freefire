<?php
require 'api/db.php';

$slug = 'cod';
$newImage = '/img/call-of-duty.png';

$stmt = $conn->prepare("UPDATE products SET image_url = ? WHERE slug = ?");
$stmt->bind_param("ss", $newImage, $slug);

if ($stmt->execute()) {
    echo "Product image updated successfully.";
} else {
    echo "Error updating product image: " . $conn->error;
}
?>