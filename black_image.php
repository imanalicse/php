<?php
$image = '';
$data = '';
$getImage = imagecreatefromstring($data);
$width = imagesx($getImage);
$height = imagesy($getImage);

$blankImage = 1;
for ($x = 0; $x < $width; $x++) {
    for ($y = 0; $y < $height; $y++) {
        $rgb = imagecolorat($getImage, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;

        $color = $r.$g.$b;
        if ($color != "000") {
            $blankImage = 0;
            break;
        }
    }
    if ($blankImage == 0) {
        break;
    }
}
