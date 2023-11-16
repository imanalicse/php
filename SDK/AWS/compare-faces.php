<?php
require_once(__DIR__ . '/../../vendor/autoload.php');

use Aws\S3\S3Client;



// Instantiate an Amazon S3 client.
$bucket = 'stage-rgs';
$args = getAwsArguments();
function getAwsArguments(): array {
    $aws_access_key_id = '';
    $aws_secret_access_key = '';
    $region = '';

    $args = [
        'version' => 'latest',
        'region'  => $region,
        'credentials' => [
            'key'    => $aws_access_key_id,
            'secret' => $aws_secret_access_key,
        ]
    ];

    return $args;
}
$s3Client = new S3Client($args);
$rekognition_client = new Aws\Rekognition\RekognitionClient($args);


// Upload a publicly accessible file. The file size and type are determined by the SDK.
$source_image = 'C:\Users\iman\Desktop\images\RGS\face/G221019DK083-BL001.JPG';
// $source_image = 'C:\Users\iman\Desktop\images\RGS\face/G221019DK518-BV001.jpg';
// $target_image = 'C:\Users\iman\Desktop\images\RGS\face\compares/G221019DK083-BL001.JPG';
$target_image = 'C:\Users\iman\Desktop\images\RGS\face\compares/G221019DK083-BL011.JPG';

$prefix = '231101';
try {
    $list_objects = $s3Client->listObjects([
        'Bucket' => $bucket,
        'Prefix'  => $prefix,
    ]);

    if (isset($list_objects['Contents']) && !empty($list_objects['Contents'])) {
        foreach ($list_objects['Contents'] as $index => $content) {
            $key = $content['Key'];
            $extension = pathinfo($key, PATHINFO_EXTENSION);
            if ($key == '231101/G230621DK143-CD001.JPG') {
            // if (in_array($extension, ['JPG', 'jpg', 'JPEG', 'jpeg'])) {
                $target_image_bites = getS3Object($bucket, $key);
                echo '<pre>';
                echo print_r($index. '-> '. $key);
                echo '</pre>';
                echo '<pre>';
                echo print_r($target_image_bites);
                echo '</pre>';
                $compare_result = $rekognition_client->compareFaces([
                    'SimilarityThreshold' => 80,
                    'SourceImage' => [
                        'Bytes' => file_get_contents($source_image)
                    ],
                    'TargetImage' => [
                        'Bytes' => $target_image_bites //file_get_contents($target_image)
                    ]
                ]);
//                echo '<pre>';
//                echo print_r($compare_result['FaceMatches']);
//                echo '</pre>';
                if (!empty($compare_result['FaceMatches'])) {
                    foreach ($compare_result['FaceMatches'] as $faceMatch) {
                        $face = $faceMatch['Face'];
                    }
                }
            }
        }
    }


    /*
    $compare_result = $rekognition_client->compareFaces([
          'SimilarityThreshold' => 80,
          'SourceImage' => [
              'Bytes' => file_get_contents($source_image)
          ],
          'TargetImage' => [
              'Bytes' => file_get_contents($target_image)
          ]
    ]);
    echo '<pre>';
    echo print_r($compare_result);
    echo '</pre>';
    */
}
catch (Aws\S3\Exception\S3Exception $e) {
    echo "There was an error comparing face.". $e->getMessage();
}

function getS3Object($bucket, $key) {
    $args = getAwsArguments();
    $s3Client = new S3Client($args);
    $file = $s3Client->getObject(array(
        'Bucket' => $bucket,
        'Key' => $key
    ));
    $body = $file->get('Body');
    // $body->rewind();
    return $body;
}
