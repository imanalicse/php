<?php

namespace App\Controller\Component;



class FileHandlerComponent
{


	function resizeImage($orgimagepath, $thumbwidth, $thumbheight, $resizeFollow = 'both'){
		$resizeWidthHeight = Array();

		$imagepath = $orgimagepath;

		$dot = strrpos($imagepath, '.') + 1;
		$Ext =  substr($imagepath, $dot);
		$Ext = ".".$Ext;


		if(! is_file( $imagepath ) ) {
			$imagepath = str_replace($Ext, strtolower($Ext),$orgimagepath);
			if(! is_file( $imagepath ) ) {
				$imagepath = str_replace($Ext, strtoupper($Ext),$orgimagepath);
				if(! is_file( $imagepath ) ) {
					$imagepath = "";
				}
			}
		}

		if($imagepath){

			// Get the original geometry and calculate scales
			list($orgwidth, $orgheight) = getimagesize($imagepath);

			if($resizeFollow == 'width'){
				$modwidth = round($thumbwidth);
				$x = $orgwidth / $thumbwidth;
				$modheight  = round($orgheight/$x);
			}

			else if($resizeFollow == 'height'){
				$modheight = round($thumbheight);
				$x = $orgheight / $thumbheight;
				$modwidth  = round($orgwidth/$x);
			}
			else if($resizeFollow == 'max_width'){
				if($thumbwidth > $orgwidth){
					$modwidth = round($thumbwidth);
					$x = $orgwidth / $thumbwidth;
					$modheight  = round($orgheight/$x);
				}
				else{
					$modwidth = $thumbwidth;
					$ratio_orig = $orgwidth / $orgheight;
					$modheight = round($thumbwidth / $ratio_orig);
				}
			}

			else if($resizeFollow == 'max_height'){
				if($thumbheight > $orgheight){
					$modheight = round($thumbheight);
					$x = $orgheight / $thumbheight;
					$modwidth  = round($orgwidth/$x);
				}
				else{
					$modheight = $thumbheight;
					$ratio_orig = $orgwidth / $orgheight;
					$modwidth = round($thumbheight * $ratio_orig);
				}
			}
			else if($resizeFollow == 'max'){

				$modwidth = $thumbwidth;
				$modheight = $thumbheight;

				$ratio_orig = $orgwidth / $orgheight;

				if ($thumbwidth / $thumbheight > $ratio_orig) {
					$modwidth = round($thumbheight * $ratio_orig);
				} else {
					$modheight = round($thumbwidth / $ratio_orig);
				}
			}

			else{
				if ($orgheight < $thumbheight && $orgwidth < $thumbwidth){
					$modheight = $orgheight;
					$modwidth  = $orgwidth;
				}
				else{

					if ($orgwidth < $orgheight){ //(shrink image by height to fit)
						$modheight = round($thumbheight);
						$x = $orgheight / $thumbheight;
						$modwidth  = round($thumbheight/$x);

					}

					if ($orgwidth > $orgheight){ // (shrink image by width to fit)
						$modwidth = round($thumbwidth);
						$x = $orgwidth / $thumbwidth;
						$modheight  = round($thumbwidth/$x);
					}
				}
			}

			$resizeWidthHeight['width'] = $modwidth;
			$resizeWidthHeight['height'] = $modheight;
		}
		return $resizeWidthHeight;
	}

	function thumbnail_img($file, $thumbWidth, $thumbHeight, $filepath = null){
        $filename = $file->getClientFilename();
        $subDomain = $this->getComponent('CommonFunction')->getSubDomainName();
        if (isset($filename)) {

			if(!$filepath) {
				$controller_name = strtolower($this->getController()->getName());
				$filepath = WWW_ROOT.'uploads'.DS. $subDomain .DS.$controller_name;
				$thubm_imgpath = WWW_ROOT.'uploads'.DS. $subDomain .DS.$controller_name.DS.'resized';
			}
			else{
				$thubm_imgpath = $filepath.DS.'resized';
			}

			if (!is_dir($thubm_imgpath) && !is_file($thubm_imgpath)) {
				$this->createFolder($thubm_imgpath,'0777');
			}

			if (!$this->isImage($file->getClientFilename())) {
				$this->Session->setFlash(__('Sample could not be saved. Please, try again.', true), 'default', array('class' => 'error'));
				return false;
			}

			if (!$this->Thumbnail( $filepath.DS.$this->_uploadimgname, $thubm_imgpath.DS.$this->_uploadimgname, $thumbWidth, $thumbHeight )) {
				$this->Session->setFlash(__('Error. Unable to upload file', true), 'default', array('class' => 'error'));
				return false;
			}
		}
		return true;
	}


	function Thumbnail($sourcePath,$thumbPath,$thumbWidth,$thumbHeight) {
		//make sure the GD library is installed
		if(!function_exists("gd_info")) {
			echo 'You do not have the GD Library installed.  This class requires the GD library to function properly.' . "\n";
			echo 'visit http://us2.php.net/manual/en/ref.image.php for more information';
			exit;
		}
		//initialize variables
		$errmsg               = '';
		$error                = false;
		$fileName             = $sourcePath;

		//check to see if file exists
		if(!file_exists($fileName )) {
			$errmsg = 'File not found';
			$error = true;
		}
		//check to see if file is readable
		elseif(!is_readable($fileName )) {
			$errmsg = 'File is not readable';
			$error = true;
		}

		//if there are no errors, determine the file format
		if($error == false) {
			//check if gif
			if(stristr(strtolower($fileName ),'.gif')) $format = 'GIF';
			//check if jpg
			elseif(stristr(strtolower($fileName ),'.jpg') || stristr(strtolower($fileName ),'.jpeg')) $format = 'JPG';
			//check if png
			elseif(stristr(strtolower($fileName ),'.png')) $format = 'PNG';
			//unknown file format
			else {
				$errmsg = 'Unknown file format';
				$error = true;
			}
		}

		//initialize resources if no errors
		if($error == false) {
            switch($format) {
                case 'GIF':
                    $sourceImage = imagecreatefromgif($sourcePath);
                    $sourceWidth = imagesx($sourceImage);
                    $sourceHeight = imagesy($sourceImage);

                    $targetImage = imagecreatetruecolor($thumbWidth,$thumbWidth);
                    imagecopyresampled($targetImage,$sourceImage,0,0,0,0,$thumbWidth,$thumbHeight,imagesx($sourceImage),imagesy($sourceImage));
                    imagegif($targetImage,$thumbPath);
                    break;
                case 'JPG':
                case 'JPEG' :

                    $sourceImage = imagecreatefromjpeg($sourcePath);
                    $sourceWidth = imagesx($sourceImage);
                    $sourceHeight = imagesy($sourceImage);

                    $targetImage = imagecreatetruecolor($thumbWidth,$thumbWidth);
                    imagecopyresampled($targetImage,$sourceImage,0,0,0,0,$thumbWidth,$thumbHeight,imagesx($sourceImage),imagesy($sourceImage));
                    imagejpeg($targetImage,$thumbPath,100);
                    break;

                case 'PNG':
                    $sourceImage = imagecreatefrompng($sourcePath);
                    $sourceWidth = imagesx($sourceImage);
                    $sourceHeight = imagesy($sourceImage);

                    $targetImage = imagecreatetruecolor($thumbWidth,$thumbWidth);
                    imagecopyresampled($targetImage,$sourceImage,0,0,0,0,$thumbWidth,$thumbHeight,imagesx($sourceImage),imagesy($sourceImage));
                    imagepng($targetImage,$thumbPath);
                    break;
            }

		}

		if($error == true) {
			$this->Session->setFlash(__($errmsg, true), 'default', array('class' => 'error'));
			return false;
		}
		return true;
	}

}
