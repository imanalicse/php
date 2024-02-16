<?php

namespace App\SDK\AWS;

require '../../global_config.php';

use Aws\S3\S3Client;

class AwsComponent
{
    public function awsBucket() {
        return getenv("AWS_BUCKET");
    }

    public function awsClientArguments() : array {
        return [
            'version' => 'latest',
            'region'  => getenv("AWS_REGION"),
            'credentials' => [
                'key'    => getenv("AWS_ACCESS_KEY_ID"),
                'secret' => getenv("AWS_SECRET_ACCESS_KEY"),
            ]
        ];
    }

    public function allowExtension() : array {
        return ['JPG', 'jpg', 'JPEG', 'jpeg'];
    }

    public function fetchMatchingImages($base_image) : array {
        $matched_images = [];

        $args =$this->awsClientArguments();
        $bucket = $this->awsBucket();
        $prefix = '231101';
        $s3Client = new S3Client($args);
        $rekognition_client = new \Aws\Rekognition\RekognitionClient($args);

        $list_objects = $s3Client->listObjects([
            'Bucket' => $bucket,
            'Prefix'  => $prefix,
        ]);
        if (isset($list_objects['Contents']) && !empty($list_objects['Contents'])) {
            foreach ($list_objects['Contents'] as $content) {
                $key = $content['Key'];
                $extension = pathinfo($key, PATHINFO_EXTENSION);
                if (in_array($extension, $this->allowExtension())) {
                    try {
                        $target_image_bytes = $this->getS3ObjectAsBytes($bucket, $key);
                        $compare_result = $rekognition_client->compareFaces([
                            'SimilarityThreshold' => 80,
                            'SourceImage' => [
                                'Bytes' => file_get_contents($base_image)
                            ],
                            'TargetImage' => [
                                'Bytes' => $target_image_bytes
                            ]
                        ]);
                        if (!empty($compare_result['FaceMatches'])) {
                            foreach ($compare_result['FaceMatches'] as $faceMatch) {
                                $similarity = $faceMatch['Similarity'];
                                $face = $faceMatch['Face'];
                                $matched_images[] = [
                                    'key' =>  $key,
                                    'similarity' =>  $similarity,
                                ];
                            }
                        }
                    }
                    catch (\Exception $exception) {
                        echo $key. ' -> Error: '. $exception->getMessage();
                    }
                }
            }
        }
        return $matched_images;
    }

    function getS3ObjectAsBytes($bucket, $key) {
        $args = $this->awsClientArguments();
        $s3Client = new S3Client($args);
        $file = $s3Client->getObject(array(
            'Bucket' => $bucket,
            'Key' => $key
        ));
        $body = $file->get('Body');
        $body->rewind();
        return $body;
    }

    public function faceRecognizedWithAwsImages() {
        $args =$this->awsClientArguments();
        $bucket = $this->awsBucket();
        $prefix = '231101';
        $s3Client = new S3Client($args);
        $rekognition_client = new \Aws\Rekognition\RekognitionClient($args);

        $target_image = 'C:\Users\iman\Desktop\images\RGS\face\compares/G221019DK083-BL011.JPG';

        $list_objects = $s3Client->listObjects([
            'Bucket' => $bucket,
            'Prefix'  => $prefix,
        ]);
        if (isset($list_objects['Contents']) && !empty($list_objects['Contents'])) {
            foreach ($list_objects['Contents'] as $content) {
                $key = $content['Key'];
                $extension = pathinfo($key, PATHINFO_EXTENSION);
                if (in_array($extension, $this->allowExtension())) {
                    echo '<pre>';
                    echo print_r($key);
                    echo '</pre>';
                    // $target_image_bytes = $this->getS3ObjectAsBytes($bucket, $key);
                }
            }
        }


        // Specify the source and target images (stored in S3)
        $sourceImage = [
            'S3Object' => [
                'Bucket' => $bucket,
                'Name'   => '231101/G221019DK083-BL011.JPG',
            ],
        ];

        $targetImage = [
            'S3Object' => [
                'Bucket' => $bucket,
                'Name'   => '231101/G221019DK083-BL011.JPG',
            ],
        ];

        $result = $rekognition_client->compareFaces([
            'SimilarityThreshold' => 70, // Adjust as needed
            'SourceImage'        => $sourceImage,
            'TargetImage'        => $targetImage,
        ]);
        echo '<pre>';
        echo print_r($result);
        echo '</pre>';
    }

    public function uploadToS3($image_abs_path, $s3_directory) : string {
        $args =$this->awsClientArguments();
        $bucket_name = $this->awsBucket();
        $s3Client = new S3Client($args);

        $aws_file_path = $s3_directory. '/'. basename($image_abs_path);

        $put_object = [
            'Bucket' => $bucket_name,
            'Key' => $aws_file_path,
            'Body' => fopen($image_abs_path, 'r'),
            // 'ACL' => 'public-read',
        ];

        $object_url = '';
        try {
            $result = $s3Client->putObject($put_object);
            // $this->customLog('base_image_upload_response: '. json_encode($result), 'aws', 'image');
            if (!empty($result)) {
                $status_code = $result['@metadata']['statusCode'] ?? '';
                if ($status_code == 200) {
                    $object_url = $result['ObjectURL'] ?? '';
                }
                else {
                    // $this->customLog('s3 upload status_code error: '. json_encode($result), 'aws_error', 'image');
                }
            }
        }
        catch (\Aws\S3\Exception\S3Exception $e) {
            echo "There was an error uploading the file.\n";
        }
        return $object_url;
    }
}

$aws_component_obj = new AwsComponent();
// $aws_component_obj->faceRecognizedWithAwsImages();
$s3_directory = 'compare/latrobe/230823';
$image_abs_path = 'C:\Users\iman\Desktop\RGS-Images\G230823LA002/G230823LA002-DA0003.JPG';
$aws_component_obj->uploadToS3($image_abs_path, $s3_directory);

