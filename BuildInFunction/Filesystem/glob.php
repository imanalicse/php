<?php
/**
 * glob — Find pathnames matching a pattern
 *  glob(string $pattern, int $flags = 0): array|false
 */

$referenceImageUploadFolder = 'D:/wamp/www/image_process/southam/incoming/2022/';
$referenceImageUploadFolderCopyFrom = 'D:/wamp/www/image_process/southam/incoming/MappedImages/';
$referenceImageFullPath = 'D:/wamp/www/image_process/southam/processed/';

//Staging
$referenceImageThumbPath =  'D:/wamp/www/image_process/references/thumbs/';
$profImageDir = 'D:/wamp/www/image_process/watermark/proof/';
$profFontPath = 'D:/wamp/www/image_process/watermark/verdana.ttf';

$incomingGCodeImagePath = 'S221231UC016';
$image_name = 'S221231UC016-20.jpg';

$copy_from = $referenceImageUploadFolderCopyFrom . $image_name;
$copy_to = $referenceImageUploadFolder . $incomingGCodeImagePath . '/' . $image_name;
echo "<pre>";
print_r('$copy_from: '.$copy_from);
echo "</pre>";
echo "<pre>";
print_r('$copy_to: '. $copy_to);
echo "</pre>";
//if(copy($copy_from , $copy_to)) {
//    echo "<pre>";
//    print_r("copied");
//    echo "</pre>";
//}



$pattern = $referenceImageUploadFolder . '*/*/*/' . $incomingGCodeImagePath . '/' .$image_name;


echo "<pre>";
print_r($pattern);
echo "</pre>";
// $pattern = 'D:/wamp/www/image_process/southam/incoming/2022/*/*/*/S221231UC016/S221231UC016-20.jpg';
$files = glob($pattern, GLOB_BRACE);
echo "<pre>";
print_r($files);
echo "</pre>";
$fileNameArray = [];
foreach ($files AS $file) {
    //Check image already processed
    $check_file = str_replace($referenceImageUploadFolder, $referenceImageFullPath, $file);
    echo "<pre>";
    print_r($check_file);
    echo "</pre>";
    if (!file_exists($check_file)) {
        $fileNameArray[] = basename($file);
    }
}
echo "<pre>";
print_r($fileNameArray);
echo "</pre>";