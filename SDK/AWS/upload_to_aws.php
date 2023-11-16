<?php
require_once(__DIR__ . '/../../vendor/autoload.php');

use Aws\S3\S3Client;

$aws_access_key_id = '';
$aws_secret_access_key = '';
$bucket_name = '';
$region = '';

// Instantiate an Amazon S3 client.
$s3 = new S3Client([
    'version' => 'latest',
    'region'  => $region,
    'credentials' => [
        'key'    => $aws_access_key_id,
        'secret' => $aws_secret_access_key,
    ]
]);

// Upload a publicly accessible file. The file size and type are determined by the SDK.
$file_path = 'C:\Users\iman\Desktop\images\230621DK\G230621DK143/G230621DK143-CD001.JPG';

$aws_file_path = '231101/'. basename($file_path);

$put_object = [
    'Bucket' => $bucket_name,
    'Key' => $aws_file_path,
    'Body' => fopen($file_path, 'r'),
    // 'ACL' => 'public-read',
];

try {
    $result = $s3->putObject($put_object);
    echo '<pre>';
    echo print_r($result);
    echo '</pre>';
    die();
}
catch (Aws\S3\Exception\S3Exception $e) {
    echo "There was an error uploading the file.\n";
}