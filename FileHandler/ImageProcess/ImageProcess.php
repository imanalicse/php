<?php
namespace App\ImageProcess;

use App\Logger\Log;

class ImageProcess {

    private $referenceImageUploadFolder;

    private  $referenceImageFullPath;

    private $referenceImageThumbPath;

    private $error;

    private $imageLandscapeWidth = 700;

    private $imageLandscapeHeight = 560;

    private $imagePortraitWidth = 560;

    private $imagePortraitHeight = 700;

    private $profImageDir;

    private $profFontPath;

    private $profRandomStart = 1;

    private $profRandomEnd = 5;

    private $crop_full_image_ratio_max = 5;

    private $crop_full_image_ratio_min = 4;

    private $crop_thumb_image_ratio_max = 5;

    private $crop_thumb_image_ratio_min = 4;

    private $processNumberOfImageFolder = 'all'; //all,50, 100


    function __construct() {
        $this->profImageDir = __DIR__ . '/watermark/proof/';
        $this->profFontPath = __DIR__ . '/watermark/verdana.ttf';
    }

    public function imageProcess($source_image_path, $destination_image_folder, $add_water_mark = false): array {
        set_time_limit(604800);
        ini_set('memory_limit', '-1');

        $image_name = basename($source_image_path);

        //Check and Create Image Path Folder
        $destination_image_path = $destination_image_folder . '/' . $image_name;
        // Log::write('$source_image_path: '.$source_image_path, 'image_process', 'image_process');
        $response = [
            'status' => false,
            'thumb_image' => '',
            'processed_main_image' => '',
            'original_main_image' => $source_image_path,
        ];

        if (!is_file($source_image_path)) {
            $response['status'] = false;
            $response['message'] = "Source image not found";
            return $response;
        }

        if (!file_exists($destination_image_folder)) {
            $this->createDirectory($destination_image_folder);
        }

        //Set Image Size and dimension
        $imagePath = $source_image_path;
        $imagePathRotation = dirname($source_image_path) . '/rotation_' . $image_name;


        if (!empty($imagePath)) {
            $getFileInfo = pathinfo($imagePath);
            $checkSubStr = substr($getFileInfo['filename'], -1);
            if ($checkSubStr == "R") {
                /*$this->createDirectory($this->referenceImageFullPath . $this->getDestinationImagePath($key));
                $imageDestinationPath = $this->referenceImageFullPath . $this->getDestinationImagePath($key) . '/' . $imgVal;
                $this->copyImageFile($imagePath, $imageDestinationPath);*/
                // continue;
            }
        }

        //Copy Image for rotation
        $isCopyRotationFile = $this->copyImageFile($imagePath, $imagePathRotation);
        if ($isCopyRotationFile) {
            $this->processImageRotation($imagePathRotation, $imagePathRotation);

            //Get Image Size
            $referenceImageSize = $this->getImageSize($imagePathRotation);

            //Get image Dimension
            $checkImageDimension  = $this->checkImageLandscapeOrPortrait($imagePathRotation);

            $referenceImageInfoArray = array();

            $referenceImageInfoArray['image'] = $image_name;
            $referenceImageInfoArray['image_name'] = pathinfo($imagePath, PATHINFO_FILENAME );

            $referenceImageInfoArray['image_path'] = $imagePathRotation;

            $image_path_full = $destination_image_path;
            $referenceImageInfoArray['destination_image_path_full'] = $image_path_full;

            $thumb_directory = $destination_image_folder . '/thumb';
            if (!file_exists($thumb_directory)) {
                $this->createDirectory($thumb_directory);
            }
            $image_path_thumb = $thumb_directory . '/' . $image_name;

            $referenceImageInfoArray['destination_image_path_thumb'] = $image_path_thumb;

            $referenceImageInfoArray['proof_image_path'] = $this->getProofImagePath($checkImageDimension);

            $referenceImageInfoArray['org_width'] = $referenceImageSize['width'];
            $referenceImageInfoArray['org_height'] = $referenceImageSize['height'];

            $referenceImageInfoArray['dimension'] = $checkImageDimension;

            $resize_width = ($checkImageDimension == 'LANDSCAPE') ? $this->imageLandscapeWidth : $this->imagePortraitWidth;
            $resize_height = ($checkImageDimension == 'LANDSCAPE') ? $this->imageLandscapeHeight : $this->imagePortraitHeight;
            $referenceImageInfoArray['resize_width'] = $resize_width;
            $referenceImageInfoArray['resize_height'] = $resize_height;

            //Crop Size
            $imageCropSiz = $this->imageCropSiz($referenceImageSize['width'], $referenceImageSize['height'], $this->crop_thumb_image_ratio_max, $this->crop_thumb_image_ratio_min);
            $referenceImageInfoArray['org_crop_width'] = $imageCropSiz['width'];
            $referenceImageInfoArray['org_crop_height'] = $imageCropSiz['height'];

            $referenceImageInfoArray['crop_start_x'] = $imageCropSiz['crop_start_x'];
            $referenceImageInfoArray['crop_start_y'] = $imageCropSiz['crop_start_y'];

            //Create thumbs Image
            $resizeImage  = $this->imageResize($referenceImageInfoArray);

            if($resizeImage){
                $response['status'] = true;
                $response['thumb_image'] = $image_path_thumb;
                //Watermark Image
                if ($add_water_mark) {
                    $isSuccessWatermark = $this->addWatermarkImage($referenceImageInfoArray['image_name'], $referenceImageInfoArray['destination_image_path_thumb'], $referenceImageInfoArray['proof_image_path'], $referenceImageInfoArray['resize_width'], $referenceImageInfoArray['resize_height']);
                }
                else {
                    $isSuccessWatermark = true;
                }

                if ($isSuccessWatermark){

                    // START: Crop Fill Image image
                    $imageCropSizeOriginal = $this->imageCropSiz($referenceImageSize['width'], $referenceImageSize['height'], $this->crop_full_image_ratio_max, $this->crop_full_image_ratio_min);

                    $referenceImageInfoArray['crop_start_x'] = $imageCropSizeOriginal['crop_start_x'];
                    $referenceImageInfoArray['crop_start_y'] = $imageCropSizeOriginal['crop_start_y'];

                    $referenceImageInfoArray['resize_width'] = $imageCropSizeOriginal['width'];
                    $referenceImageInfoArray['resize_height'] = $imageCropSizeOriginal['height'];

                    $referenceImageInfoArray['org_crop_width'] = $imageCropSizeOriginal['width'];
                    $referenceImageInfoArray['org_crop_height'] = $imageCropSizeOriginal['height'];

                    $referenceImageInfoArray['destination_image_path_thumb'] = $referenceImageInfoArray['destination_image_path_full'];
                    $resizeImageOriginal  = $this->imageResize($referenceImageInfoArray);
                    //END : Crop Fill Image image


                    //Move Full Image
                    //$isCopyFile = $this->copyImageFile($imagePathRotation, $referenceImageInfoArray['destination_image_path_full']);

                    if($resizeImageOriginal){
                        //Delete Source Image
                        //@unlink($imagePath);
                        @unlink($imagePathRotation);
                        $response['processed_main_image'] = $referenceImageInfoArray['destination_image_path_full'];
                    }
                }
            }
        }
        return $response;
    }


    private function imageCropSiz($org_width, $org_height, $crop_ratio_max = 5, $crop_ratio_min = 4){

        $imageCropSize = array();

        if($org_width > $org_height){ //Base Width

            $org_ratio =  $org_width /  $org_height;

            $ratio_width = $crop_ratio_max;
            $ratio_height = $crop_ratio_min;

            if($org_ratio >= ($ratio_width / $ratio_height)){
                //Resize Width
                $ratio = $org_height / $crop_ratio_min;  // 640

                $new_width = $ratio * $crop_ratio_max;    // 640 * 5 = 3200
                $new_height =  $org_height;  // 2560

                $imageCropSize['width'] = $new_width;
                $imageCropSize['height'] = $new_height;

                $crop_start_x =  ($org_width - $new_width) / 2;
                $imageCropSize['crop_start_x'] = $crop_start_x;
                $imageCropSize['crop_start_y'] = 0;
            }
            else{
                //Resize Height
                $ratio = $org_width / $crop_ratio_max;    // 512

                $new_width = $org_width;   // 2560
                $new_height =  $ratio * $crop_ratio_min;  //2048


                $imageCropSize['width'] = $new_width;
                $imageCropSize['height'] = $new_height;

                $crop_start_y =  ($org_height - $new_height) / 2;
                $imageCropSize['crop_start_x'] = 0;
                $imageCropSize['crop_start_y'] = $crop_start_y;
            }

        }
        else { //Base Height

            $org_ratio =  $org_width /  $org_height;

            $ratio_width = $crop_ratio_min;
            $ratio_height = $crop_ratio_max;

            if($org_ratio >= ($ratio_width / $ratio_height)){

                //Resize Width
                $ratio = $org_height / $crop_ratio_max;  // 640

                $new_width = $ratio * $crop_ratio_min;    // 640 * 5 = 3200
                $new_height =  $org_height;  // 2560

                $imageCropSize['width'] = $new_width;
                $imageCropSize['height'] = $new_height;

                $crop_start_x =  ($org_width - $new_width) / 2;
                $imageCropSize['crop_start_x'] = $crop_start_x;
                $imageCropSize['crop_start_y'] = 0;
            }
            else{
                //Resize Height
                $ratio = $org_width / $crop_ratio_min;    // 512

                $new_width = $org_width;   // 3168
                $new_height =  $ratio * $crop_ratio_max;  //2048

                $imageCropSize['width'] = $new_width;
                $imageCropSize['height'] = $new_height;

                $crop_start_y =  ($org_height - $new_height) / 2;
                $imageCropSize['crop_start_x'] = 0;
                $imageCropSize['crop_start_y'] = $crop_start_y;

            }
        }

        return $imageCropSize;

    }

    private function getProofImagePath($imageDimension){

        $randomNumber = rand($this->profRandomStart, $this->profRandomEnd);

        $proofImagePath = '';
        if($imageDimension == 'PORTRAIT'){
            $proofImagePath = $this->profImageDir . 'portrait/' . $randomNumber . '.png';
        }
        else{
            $proofImagePath = $this->profImageDir . 'landscape/' . $randomNumber . '.png';
        }

        return  $proofImagePath;
    }

    private function moveImageFile($sourceFile, $destinationFile){
        if (@copy($sourceFile, $destinationFile)) {
            @unlink($sourceFile);
            return true;
        }
        else{
            return false;
        }

    }

    private function copyImageFile($sourceFile, $destinationFile){
        if (@copy($sourceFile, $destinationFile)) {
            return true;
        }
        else{
            return false;
        }

    }

    private function getImageSize($imagePath){
        $imageInfoArray = array();
        $imageInfo = @getimagesize($imagePath);

        $imageInfoArray['width'] = $imageInfo[0];
        $imageInfoArray['height'] = $imageInfo[1];

        return $imageInfoArray;
    }

    public function getFolderNameList($imageDirPath){
        $dirNameArray = array();

        if($handle = opendir($imageDirPath)) {
            while (false !== ($dirName = readdir($handle))) {
                if ($dirName != "." && $dirName != ".." && is_dir($imageDirPath . '/' . $dirName)) {
                    array_push($dirNameArray, $dirName);
                }
            }

            closedir($handle);
        }

        return $dirNameArray;
    }

    private function getDestinationImagePath($dirName){

        $dir1 = substr($dirName, 0, 3);

        $dir2 = substr($dirName, 3, 2);

        $dir3 = substr($dirName, 5, 4);

        $dir4 = $dirName;

        $dstPath =  $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . $dir4;

        return $dstPath;
    }

    private function getDestinationThumbImagePath($dirName){

        $prefixDir = 'graduation';
        if(substr($dirName, 0, 1) == 'F'){
            $prefixDir = 'facebook';
        }
        else if(substr($dirName, 0, 1) == 'S'){
            $prefixDir = 'stage';
        }

        $dir1 = substr($dirName, 0, 3);

        $dir2 = substr($dirName, 3, 2);

        $dir3 = substr($dirName, 5, 4);

        $dir4 = $dirName;

        $dstPath =  $prefixDir . '/' . $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . $dir4;

        return $dstPath;
    }

    public function createDirectory($dirName){

        if (!is_file($dirName) && !is_dir($dirName)) {
            @mkdir($dirName, 0755, true);
        }

    }

    private function checkImageLandscapeOrPortrait($imagePath){
        $imageInfo = $this->getImageSize($imagePath);

        if ($imageInfo['width'] > $imageInfo['height']) {
            return 'LANDSCAPE';
        }
        else {
            return 'PORTRAIT';
        }
    }

    private function  getFileName($imagePath){
        return basename($imagePath);
    }

    private function imageResize($item =array(), $quality = 100){

        $sourcePath = $item['image_path'];
        $destinationPath = $item['destination_image_path_thumb'];
        $thumbWidth = $item['resize_width'];
        $thumbHeight = $item['resize_height'];

        //make sure the GD library is installed
        if(!function_exists("gd_info")) {
            echo 'You do not have the GD Library installed.  This class requires the GD library to function properly.' . "\n";
            echo 'visit http://us2.php.net/manual/en/ref.image.php for more information';
            exit;
        }
        //initialize variables
        $errmsg               = '';
        $error                = false;

        //check to see if file exists
        if(!file_exists($sourcePath)) {
            $errmsg = 'File not found';
            $error = true;
        }
        //check to see if file is readable
        elseif(!is_readable($sourcePath)) {
            $errmsg = 'File is not readable';
            $error = true;
        }


        $imageIns = $this->imageCreateFromExtension($sourcePath);

        $targetImageIns = imagecreatetruecolor($thumbWidth, $thumbHeight);
        imagecopyresized($targetImageIns, $imageIns, 0, 0, $item['crop_start_x'], $item['crop_start_y'], $thumbWidth, $thumbHeight, $item['org_crop_width'], $item['org_crop_height']);

        $this->imageOutput($sourcePath, $destinationPath, $targetImageIns, $quality);

        if($this->error == true) {
            return false;
        }

        return true;
    }

    public function addWatermarkImage($referenceNumber, $imagePath, $profImagePath, $thumbWidth, $thumbHeight, $quality = 100){

        /* START: Main Image*/
        $mainImageIns = $this->imageCreateFromExtension($imagePath);
        /* END: Main Image*/

        # Prof Image 145 x 116
        $profImageIns = @imagecreatefrompng($profImagePath);

        #Merge Prof Image AND Main Image
        imagecopyresampled($mainImageIns, $profImageIns, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $thumbWidth, $thumbHeight);

        #Add Reference Number
        $ImageFont = $this->profFontPath;
        $black = imagecolorallocatealpha($mainImageIns, 0, 0, 0, 47);
        $white  = imagecolorallocate($mainImageIns, 255, 255, 255);

        $x_pos = ($thumbWidth - 188) / 2;
        $y_pos = 20;

        //Add Border
        imagettftext($mainImageIns, 12, 0, $x_pos + 1, $y_pos + 1, $white, $ImageFont, $referenceNumber);
        imagettftext($mainImageIns, 12, 0, $x_pos - 1, $y_pos - 1, $white, $ImageFont, $referenceNumber);

        //Add Text
        imagettftext($mainImageIns, 12, 0, $x_pos, $y_pos, $black, $ImageFont, $referenceNumber);

        $this->imageOutput($imagePath, $imagePath, $mainImageIns, $quality);

        ImageDestroy ($mainImageIns);
        ImageDestroy ($profImageIns);

        return true;
    }

    private function getFileExt($fileName){
        $file = basename($fileName);
        $ext = pathinfo($file, PATHINFO_EXTENSION);

        return $ext;
    }

    private function getImageOrientation($imagePath){
        $exifInfo = @exif_read_data($imagePath, 0, true);
        if(isset($exifInfo['IFD0']['Orientation'])){
            return $exifInfo['IFD0']['Orientation'];
        }
        else{
            return 1;
        }
    }


    public function processImageRotation($imagePath, $destinationImagePath){

        $orientation = $this->getImageOrientation($imagePath);

        $imageIns = $this->imageCreateFromExtension($imagePath);

        switch($orientation)
        {
            case 1: // nothing
                return true;
                break;

            case 2: // horizontal flip
                $outputImgIns = $this->flipImage($imageIns, 1);
                break;

            case 3: // 180 rotate left
                $outputImgIns = $this->rotateImage($imageIns, 180);
                break;

            case 4: // vertical flip
                $outputImgIns = $this->flipImage($imageIns, 2);
                break;

            case 5: // vertical flip + 90 rotate right
                $outputImgIns = $this->flipImage($imageIns, 2);
                $outputImgIns = $this->rotateImage($outputImgIns, -90);
                break;

            case 6: // 90 rotate right
                $outputImgIns = $this->rotateImage($imageIns, -90);
                break;

            case 7: // horizontal flip + 90 rotate right
                $outputImgIns = $this->flipImage($imageIns, 1);
                $outputImgIns = $this->rotateImage($outputImgIns, -90);
                break;

            case 8:    // 90 rotate left
                $outputImgIns = $this->rotateImage($imageIns, 90);
                break;
            default:
                return true;
        }


        $this->imageOutput($imagePath, $destinationImagePath, $outputImgIns, 100);

        return true;

    }

    private function imageCreateFromExtension($imagePath){

        $ext = strtoupper($this->getFileExt($imagePath));

        switch($ext) {
            case 'GIF':
                $imageIns = @imagecreatefromgif($imagePath);
                break;

            case 'JPG':
            case 'JPEG':
                $imageIns = @imagecreatefromjpeg($imagePath);
                break;

            case 'PNG':
                $imageIns = @imagecreatefrompng($imagePath);
                break;
        }

        return $imageIns;
    }

    private function imageOutput($imagePath, $destinationImagePath, $mgIns, $quality){

        $ext = strtoupper($this->getFileExt($imagePath));

        switch($ext) {
            case 'GIF':
                imagegif($mgIns, $destinationImagePath, $quality);
                break;

            case 'JPG':
            case 'JPEG':
                imagejpeg($mgIns, $destinationImagePath, $quality);
                break;

            case 'PNG':
                imagepng($mgIns,$destinationImagePath, $quality);
                break;
        }
    }

    private function rotateImage($imgIns, $degrees){
        $rotate = imagerotate($imgIns, $degrees, -1);

        return $rotate;
    }

    private function flipImage($imgIns, $mode = 1){

        $width = imagesx($imgIns);
        $height = imagesy($imgIns);

        $src_x = 0;
        $src_y = 0;
        $src_width = $width;
        $src_height = $height;

        switch ( (int) $mode )
        {
            case 1:
                $src_y = $height;
                $src_height = -$height;
                break;

            case 2:
                $src_x = $width;
                $src_width = -$width;
                break;
        }

        $flipImgIns = imagecreatetruecolor ( $width, $height );
        imagecopyresampled ( $flipImgIns, $imgIns, 0, 0, $src_x, $src_y, $width, $height, $src_width, $src_height );

        return $flipImgIns;

    }
}
