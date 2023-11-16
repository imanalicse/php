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
}
