<?php
namespace App\SDK\AWS;
include "AwsComponent.php";

use App\SDK\AWS\AwsComponent;

$aws_component_obj = new AwsComponent();
$public_bucket = $aws_component_obj->awsBucketPublic();
$aws_file_path = 'test/S231231LA001-88.mp4';
$response = $aws_component_obj->deleteS3Object($aws_file_path, $public_bucket);
echo '<pre>';
echo print_r($response);
echo '</pre>';
