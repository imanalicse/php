<?php
namespace App\SDk\AWS;

require '../../global_config.php';

use Aws\S3\S3Client;
use App\SDK\AWS\AwsComponent;

$base_image = 'C:\Users\iman\Desktop\images\RGS\face/G221019DK083-BL001.JPG';

$aws_component = new AwsComponent();
$images = $aws_component->fetchMatchingImages($base_image);
echo '<pre>';
echo print_r($images);
echo '</pre>';

