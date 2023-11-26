<?php
namespace App\Controller\Component\V1;


class ImageComponent
{

    function getImagesByDirectory($directory) : array {
        return glob($directory . "/*.{jpg,JPG,jpeg,JPEG,gif,GIF,png,PNG,bmp,BMP}", GLOB_BRACE);
    }

    public function imageToBase64($path) : string {
        $base64 = '';
        if (is_file($path)){
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        return $base64;
    }
}
