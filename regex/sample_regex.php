<?php
/**
 * Start with G, inside any string and End with 001.JPG or 001.JPEG
 */
$image_name = 'G230621DK143-CD001.JPG';
if (preg_match('/^G.+(001\.(JPG|JPEG))$/i', $image_name)) {
    echo "Matched";
}