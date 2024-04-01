<?php
$filename = "duplicate_images.txt";
$images_arr = file($filename, FILE_IGNORE_NEW_LINES);
echo '<pre>';
echo print_r($images_arr);
echo '</pre>';

$unique_images = [];
$unique_image_file = 'unique_images.txt';
foreach ($images_arr as $image_name) {
    $image_name = trim($image_name);
    if (!in_array($image_name, $unique_images)) {
        $unique_images[] = $image_name;
        $data = $image_name.PHP_EOL;
        $fp = fopen($unique_image_file, 'a');
        fwrite($fp, $data);
    }
}
echo '<pre>';
echo print_r($unique_images);
echo '</pre>';
