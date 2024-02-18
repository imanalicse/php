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

    public function createAwsRecognitionCollection($collection_id) : string {
        $args = $this->awsClientArguments();
        $created_collection_id = '';
        try {
            $rekognition_client = new \Aws\Rekognition\RekognitionClient($args);
            $result = $rekognition_client->createCollection([
                'CollectionId' => $collection_id, // REQUIRED
            ]);
            $status_code = $result['StatusCode'] ?? '';
            if ($status_code == 200) {
                $created_collection_id = $collection_id ;
            }
        }
        catch (\Exception $exception) {
            echo "Error: " . $exception->getMessage() . "\n";
        }
        return $created_collection_id;
    }

    public function searchAwsFacesByImage($collection_id, $aws_base_image_path) : array {
        $matched_images = [];
        $args = $this->awsClientArguments();
        $bucket_name = $this->awsBucket();
        $inputImage = [
            'S3Object' => [
                'Bucket' => $bucket_name,
                'Name'   => $aws_base_image_path,
            ]
        ];
        try {
            $rekognition_client = new \Aws\Rekognition\RekognitionClient($args);
            $compare_result = $rekognition_client->searchFacesByImage([
                'CollectionId' => $collection_id,
                'Image'        => $inputImage,
            ]);
            if (!empty($compare_result['FaceMatches'])) {
                foreach ($compare_result['FaceMatches'] as $faceMatch) {
                    $similarity = $faceMatch['Similarity'];
                    $face = $faceMatch['Face'];
                    $matched_images[] = [
                        'image_name' => basename($face['ExternalImageId']),
                        'similarity' => $similarity,
                    ];
                }
            }
        }
        catch (\Exception $exception) {
            echo "Error: " . $exception->getMessage() . "\n";
        }
        return $matched_images;
    }


    public function getAwsCollections() : array {
        $args =$this->awsClientArguments();
        $collect_ids = [];
        try {
            $rekognition_client = new \Aws\Rekognition\RekognitionClient($args);
            $result = $rekognition_client->listCollections();
            $collect_ids = $result['CollectionIds'] ?? [];
        }
        catch (\Exception $exception) {
            echo "Error: " . $exception->getMessage() . "\n";
        }
        return $collect_ids;
    }

    public function hasAwsCollection($collect_name) : bool {
        $collect_ids = $this->getAwsCollections();
        $has_collection = false;
        if (in_array($collect_name, $collect_ids)) {
            $has_collection = true;
        }
        return $has_collection;
    }


    public function deleteCollection($collect_id) : bool {
        $args = $this->awsClientArguments();
        try {
            $rekognition_client = new \Aws\Rekognition\RekognitionClient($args);
            $result = $rekognition_client->deleteCollection([
                'CollectionId' => $collect_id, // REQUIRED
            ]);
            $status_code = $result['StatusCode'] ?? '';
            if ($status_code == 200) {
                return true;
            }
        }
        catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
        return false;
    }

    public function addAwsIndexFaces($collection_id, $aws_image_path) : bool {

        $has_collection = $this->hasAwsCollection($collection_id);
        if (!$has_collection) {
            $collection_id = $this->createAwsRecognitionCollection($collection_id);
        }

        $args = $this->awsClientArguments();
        $bucket_name = $this->awsBucket();
        try {
            $ExternalImageId = basename($aws_image_path);
            $inputImage = [
                'S3Object' => [
                    'Bucket' => $bucket_name,
                    'Name'   => $aws_image_path
                ]
            ];
            $param = [
                'CollectionId' => $collection_id,
                'Image'        => $inputImage,
                'ExternalImageId'        => $ExternalImageId,
            ];
            $rekognition_client = new \Aws\Rekognition\RekognitionClient($args);
            $result = $rekognition_client->indexFaces($param);
            $status_code = $result['@metadata']['statusCode'] ?? null;
            $face_records = $result['FaceRecords'] ?? null;
            if ($status_code == 200 && !empty($face_records)) {
                return true;
            }
            else {
                echo "unable to add index for collection_id($collection_id) image($aws_image_path):". json_encode($result);
            }
        }
        catch (\Exception $exception) {
            echo "Error: " . $exception->getMessage() . "\n";
        }
        return false;
    }

    public function getListFaces($collect_id, $faceId = '') {
        $args =$this->awsClientArguments();
        try {
            $rekognition_client = new \Aws\Rekognition\RekognitionClient($args);
            $param = [
                'CollectionId' => $collect_id
            ];
            if (!empty($faceId)) {
                $param['FaceId'] = $faceId;
            }
            $result = $rekognition_client->listFaces($param);
            return $result;
        }
        catch (\Exception $exception) {
            echo "Error: " . $exception->getMessage() . "\n";
        }
    }

    public function deleteFaces($collect_id, $faceIdsToDelete) {
        $args =$this->awsClientArguments();
        try {
            $rekognition_client = new \Aws\Rekognition\RekognitionClient($args);

            $result = $rekognition_client->deleteFaces([
                'CollectionId' => $collect_id,
                'FaceIds' => $faceIdsToDelete
            ]);
            echo '<pre>';
            echo print_r($result);
            echo '</pre>';
        }
        catch (\Exception $exception) {
            echo "Error: " . $exception->getMessage() . "\n";
        }
    }
}

$aws_component_obj = new AwsComponent();
// $aws_component_obj->faceRecognizedWithAwsImages();
//$s3_directory = 'compare/latrobe/230823';
//$image_abs_path = 'C:\Users\iman\Desktop\RGS-Images\G230823LA002/G230823LA002-DA0003.JPG';
//$aws_component_obj->uploadToS3($image_abs_path, $s3_directory);
$collection_id = 'latrobe_230823';
//$is_created_collection = $aws_component_obj->createAwsRecognitionCollection($collection_id);
//var_dump($is_created_collection);


$collection_ids = $aws_component_obj->getAwsCollections();
var_dump($collection_ids);
$has_collection = $aws_component_obj->hasAwsCollection($collection_id);
var_dump($has_collection);
//$is_deleted = $aws_component_obj->deleteCollection('latrobe_230823_3');
//var_dump($is_deleted);

$bucket_name = $aws_component_obj->awsBucket();
// $aws_component_obj->searchFacesByImage($collection_id);

$inputImage = [
    'S3Object' => [
        'Bucket' => $bucket_name,
        'Name'   => 'compare/latrobe/230823/G230823LA001-DA0001.JPG',
        // 'Name'   => 'compare/latrobe/230823/S230823LA001-DA0021.JPG',
        // 'Name'   => 'compare/latrobe/230823/G230823LA002-DA0002.JPG',
    ]
];

$aws_image_path = 'compare/latrobe/230823/G230823LA002-DA0002.JPG';
$aws_component_obj->addAwsIndexFaces($collection_id, $aws_image_path);
// $faces = $aws_component_obj->getListFaces($collection_id);
//echo '<pre>';
//echo print_r($faces);
//echo '</pre>';
$faceIdsToDelete = [];
if(!empty($faces['Faces'])) {
    foreach ($faces['Faces'] as $face) {
        $faceIdsToDelete[] = $face['FaceId'];
    }
}
// $aws_component_obj->deleteFaces($collection_id, $faceIdsToDelete);
$aws_base_image_path = 'compare/latrobe/base/c9d0a7a0-6cb2-4d28-b7fd-46945df3336b.JPG';
//$match_images = $aws_component_obj->searchAwsFacesByImage($collection_id, $aws_base_image_path);
//var_dump($match_images);
