<?php

namespace App\Controller\Component;


use Cake\Controller\Component;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\Http\Session\DatabaseSession;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Core\Configure;
use Cake\Log\Log;

class FileHandlerComponent extends FileStructureHandlerComponent
{
	var $controller;

    public $default_components = array('Session','Flash');

	var $_errorMsg = null;
	var $_uploadimgname = null;


	function startup(Event $event)
	{
        $this->controller = $this->_registry->getController();
	}

	function uploadImage($file, $filepath = null )
	{
	    $this->log('uploadImage 001');

		if (!empty($file->getClientFilename())) {

            $subDomain = $this->getComponent('CommonFunction')->getSubDomainName();
			if(!$filepath) {
				$controller_name = strtolower($this->getController()->getName());
				$filepath = WWW_ROOT.'uploads' . DS . $subDomain . DS . $controller_name;
			}

			if (!is_dir($filepath) && !is_file($filepath)) {
				$this->createFolder($filepath, '0777');
			}

			if (!$this->isImage($file->getClientFilename())) {
				//$this->Session->setFlash(__('Sample could not be saved. Please, try again.', true), 'default', array('class' => 'error'));
                $this->getComponent('Flash')->adminError('Sample could not be saved. Please, try again.', ['key'=>'admin_error']);
                return false;
			}

			//set image name
			$this->setUniqueName($filepath, $file->getClientFilename());

			$filepath = $filepath . DS . $this->_uploadimgname;
            $mytmp = $file->getStream()->getMetadata('uri');

			if (!$this->upload($mytmp, $filepath)) {
			    $this->log($filepath);
			    $this->log($mytmp);
				//$this->Session->setFlash(__('Error. Unable to upload file', true), 'default', array('class' => 'error'));
                $this->getComponent('Flash')->adminError('Unable to upload file.', ['key'=>'admin_error']);
                return false;
			}
		}
		return true;
	}

	function uploadVideo( $file, $filepath = null )
	{
        $filename = $file->getClientFilename();
        $subDomain = $this->getComponent('CommonFunction')->getSubDomainName();
		if (isset($filename)) {

			if(!$filepath) {
				$controller_name = strtolower($this->getController()->getName());
				$filepath = WWW_ROOT.'uploads'. DS . $subDomain .DS.$controller_name;
			}

			if (!is_dir($filepath) && !is_file($filepath)) {
				$this->createFolder($filepath,'0777');
			}

			if (!$this->isVideo($file->getClientFilename())) {
				//$this->Session->setFlash(__('Sample could not be saved. Please, try again.', true), 'default', array('class' => 'error'));
                $this->getComponent('Flash')->adminError('Sample could not be saved. Supported format is (swf,flv,mp3,wma,mp4) Please, try again.', ['key'=>'admin_error']);
                return false;
			}

			//set image name
			$this->setUniqueName($filepath, $file->getClientFilename());

			$filepath = $filepath.DS.$this->_uploadimgname;
            $mytmp = $file->getStream()->getMetadata('uri');
			if (!$this->upload($mytmp, $filepath)) {
				//$this->Session->setFlash(__('Error. Unable to upload file', true), 'default', array('class' => 'error'));
                $this->getComponent('Flash')->adminError('Unable to upload file.', ['key'=>'admin_error']);
                return false;
			}
		}
		return true;
	}

	function generateUploadFilePath($sub_directory = null, $item_type = null, $subdomain = null, $is_secure = null, $directory_root = 'uploads') {
        $filepath = $this->getPartialUploadPathWithBase($sub_directory, $item_type, $subdomain, $is_secure, $directory_root);
        return $filepath;
    }

	function uploadfile( $file, $filepath = null, $sub_directory = null, $item_type = null, $subdomain = null, $is_secure = null )
	{
//        $item_type can be audio, reports, tmp, imports, pdf
        $filename=$file->getClientFilename();

		if (isset($filename)) {

			if(!$filepath) {
			    if(empty($item_type)) {
                    $item_type = strtolower($this->getController()->getName());
                }
                $filepath = $this->generateUploadFilePath($sub_directory, $item_type, $subdomain, $is_secure);
			}

			if (!is_dir($filepath) && !is_file($filepath)) {
				$this->createFolder($filepath,'0777');
			}

			//set image name
			$this->setUniqueName($filepath, $file->getClientFilename());
            $filepath = substr($filepath, -1,1) == DS ? $filepath : $filepath.DS;
			$filepath = $filepath.$this->_uploadimgname;
            $mytmp = $file->getStream()->getMetadata('uri');;
			if (!$this->upload($mytmp, $filepath)) {
			    $this->getController()->Flash->adminError('Error. Unable to upload file', ['key'=>'admin_error']);
                //$this->getComponent('Flash')->adminError('Error. Unable to upload file', ['key'=>'admin_error']);
				return false;
			}
		}
		return true;
	}

	function upload($src, $dest){

		$ret = false;
		$dest = $this->clean($dest);
		$baseDir = dirname($dest);
		if (is_writeable($baseDir) && move_uploaded_file($src, $dest)) {
            $ret = true;
			/*if ($this->setPermissions($dest)) {
				$ret = true;
			}
			else {
				//JError::raiseWarning(21, JText::_('WARNFS_ERR01'));
			}*/
		}
		else {
			//JError::raiseWarning(21, JText::_('WARNFS_ERR02'));
		}

		return $ret;

	}

    function rename($src, $dest){
        $ret = false;
        $dest = $this->clean($dest);
        if(file_exists($dest)) {
            return $ret;
        }
        $baseDir = dirname($dest);
        if (is_writeable($baseDir) && rename($src, $dest)) {
            $ret = true;
        }
        return $ret;
    }

	function setPermissions($path, $filemode = '0644', $foldermode = '0755') {

		// Initialize return value
		$ret = true;

		if (is_dir($path))
		{
			$dh = opendir($path);
			while ($file = readdir($dh))
			{
				if ($file != '.' && $file != '..') {
					$fullpath = $path.'/'.$file;
					if (is_dir($fullpath)) {
						if (!$this->setPermissions($fullpath, $filemode, $foldermode)) {
							$ret = false;
						}
					} else {
						if (isset ($filemode)) {
							if (!@ chmod($fullpath, octdec($filemode))) {
								$ret = false;
							}
						}
					} // if
				} // if
			} // while
			closedir($dh);
			if (isset ($foldermode)) {
				if (!@ chmod($path, octdec($foldermode))) {
					$ret = false;
				}
			}
		}
		else
		{
			if (isset ($filemode)) {
				$ret = @ chmod($path, octdec($filemode));
			}
		} // if
		return $ret;
	}

	function clean($path, $ds=DS)
	{
		$path = trim($path);

		if (empty($path)) {
			$path = WWW_ROOT;
		}
		else {
			$path = preg_replace('#[/\\\\]+#', $ds, $path);
		}

		return $path;
	}

	function deleteFile( $imagename, $filepath = null ){

        $subDomain = $this->getComponent('CommonFunction')->getSubDomainName();
		if(!$filepath) {
			$controller_name = strtolower($this->getController()->getName());
			$imgpath = WWW_ROOT.'uploads'.DS . $subDomain.DS.$controller_name.DS.$imagename;
			$thubm_imgpath = WWW_ROOT.'uploads'. DS. $subDomain .DS.$controller_name.DS.'resized'.DS.$imagename;
		}
		else{
			$imgpath = $filepath.DS.$imagename;
			$thubm_imgpath = $filepath.DS.'resized'.DS.$imagename;
		}

		if (is_file($imgpath)) {
			unlink($imgpath);
		}

		if (is_file($thubm_imgpath)) {
			unlink($thubm_imgpath);
		}

	}

	function setUniqueName ($filePath, $fileName) {

		$fileName = $this->makeSafe($fileName);

		if( file_exists($filePath.DS.$fileName) ){
			$this->_uploadimgname = time() . "_".$fileName;
		}
		else{
			$this->_uploadimgname =  $fileName;
		}

	}

	function makeSafe($file) {
		$regex = array('#(\.){2,}#', '#[^A-Za-z0-9\.\_\- ]#', '#^\.#', '/\s+/');
		return preg_replace($regex, '_', $file);
	}

	function isImage( $fileName )
	{
		static $imageTypes = 'xcf|odg|gif|jpg|png|bmp|jpeg|ico';
		return preg_match("/$imageTypes/i",$fileName);
	}
    function filterFile( $fileName, $extension = null )
    {
        $f_ext = [];
        foreach ($extension as $ext) {
            array_push($f_ext, $ext);
        }
        $imageTypes = implode('|', $f_ext);
        return preg_match("/$imageTypes/i",$fileName);
    }

	function isVideo( $fileName )
	{
		static $imageTypes = 'swf|flv|mp3|wma|mp4';
		return preg_match("/$imageTypes/i",$fileName);
	}
	function isAudio( $fileName )
	{
		static $imageTypes = 'wav|flv|mp3|wma|m4a';
		return preg_match("/$imageTypes/i",$fileName);
	}
	//return $this->log_proper_error($file['error']);
	function log_proper_error($err_code) {
		switch ($err_code) {
			case UPLOAD_ERR_NO_FILE:
				return 0;
			case UPLOAD_ERR_INI_SIZE:
				$e = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$e = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
				break;
			case UPLOAD_ERR_PARTIAL:
				$e = 'The uploaded file was only partially uploaded.';
				break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$e = 'Missing a temporary folder.';
				break;
			case UPLOAD_ERR_CANT_WRITE:
				$e = 'Failed to write file to disk.';
				break;
			case UPLOAD_ERR_EXTENSION:
				$e = 'File upload stopped by extension.';
				break;
			default:
				$e = 'Unknown upload error. Did you add array(\'type\' => \'file\') to your form?';
		}
		return $this->log_cakephp_error_and_return($e);
	}


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

	function uploadAudio( $file, $filepath = null, $sub_directory = null, $item_type = null, $subdomain = null, $is_secure = null )
	{
        $filename = $file->getClientFilename();
		if (isset($filename)) {

			if(!$filepath) {
                if(empty($item_type)) {
                    $item_type = strtolower($this->getController()->getName());
                    if($item_type=='students'){
                        $item_type='users';
                    }
                }
                $filepath = $this->generateUploadFilePath($sub_directory, $item_type, $subdomain, $is_secure);
				//$filepath = WWW_ROOT.'uploads'.DS.$controller_name;
			}

			if (!is_dir($filepath) && !is_file($filepath)) {
				$this->createFolder($filepath,'0777');
			}

			if (!$this->isAudio($file->getClientFilename())) {
                //$this->getComponent('Flash')->set('Sample could not be saved. Please, try again.', ['element' => 'error']);
				return false;
			}

			$this->setUniqueName($filepath, $file->getClientFilename());

			$filepath = $filepath.DS.$this->_uploadimgname;
            $mytmp = $file->getStream()->getMetadata('uri');
			if (!$this->upload($mytmp, $filepath)) {
                //$this->getComponent('Flash')->set('Error. Unable to upload file', ['element' => 'error']);
				return false;
			}
		}
		return true;
	}

}
