<?php
include "functions.php";

$reference_code_images = [];

$reference_code_images[] = [
    'success'=> true,
    'src_path' => 'Yes',
];

$reference_code_images[] = [
    'success'=> false,
    'src_path' => 'Hello',
];

$reference_code_images[] = [
    'success'=> true,
    'src_path' => 'Hello',
];



$reference_code_images = [];
echo "<pre>";
print_r($reference_code_images);
echo "</pre>";

$reference_code_image_status = true;
if(!empty($reference_code_images)) {
    foreach ($reference_code_images as $reference_code_image) {
        if(!$reference_code_image['success']) {
            $reference_code_image_status = false;
        }
    }
} else {
    $reference_code_image_status = false;
}

echo "<pre>";
print_r('$reference_code_image_status='.$reference_code_image_status);
echo "</pre>";