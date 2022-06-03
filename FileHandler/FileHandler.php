<?php
namespace App\FileHandler;

class FileHandler
{
    public $_uploadimgname;

    function uploadFile($file, $filepath = null, $allow_extensions = [], $max_size = null, $new_file_name = null, $dimension = []) : array
    {
        if(!$filepath) {
            $filepath = __DIR__ . '/uploads';
        }
        $response = ['is_success' => false, 'message' => 'File not found'];

        if (isset($file['name']) && !empty($file['name'])) {
            $validate_file_extension = $this->validateFileExtension($file['name'], $allow_extensions);
            if (!$validate_file_extension['is_success']) {
                return $validate_file_extension;
            }

            $validate_file_size = $this->validateFileSize($file['size'], $max_size);
            if (!$validate_file_size['is_success']) {
                return $validate_file_size;
            }

            if( $this->isImageByAbsPath($file) ){
                $validate_dimension = $this->validateDimension($file, $dimension);
                if (!$validate_dimension['is_success']) {
                    return $validate_dimension;
                }
            }

            if (!is_dir($filepath) && !is_file($filepath)) {
                $this->createFolder($filepath,'0775');
            }

            //set image name
            $new_file_name = $new_file_name ?? $file['name'];
            $this->setUniqueName($filepath, $new_file_name);
            $filepath = $filepath. DIRECTORY_SEPARATOR . $this->_uploadimgname;

            if ($this->upload($file['tmp_name'], $filepath)) {
                $response = [
                    'is_success' => true,
                    'message' => 'File uploaded successfully'
                ];
            } else {
                $baseDir = dirname($filepath);
                $response = [
                    'is_success' => false,
                    'message' => 'File not uploaded. Please try again.',

                    //debug data: we should not be here in live!
                    'is_writable' => is_writeable($baseDir),
                    'src'  => $file['tmp_name'],
                    'dest' => $filepath,
                    'test' => 0
                ];
            }
        }
        return $response;
    }

    function isPdf( $fileName ) : bool {
        return preg_match("/pdf/i",$fileName);
    }

    function isExel( $fileName ) : bool {
        return preg_match("/xls|xlsx/i",$fileName);
    }

    function isCsv( $fileName ) : bool {
        return preg_match("/csv/i",$fileName);
    }

    function isFile( $file ) : bool {
        $file_name = basename($file);
        $file_types = implode('|', $this->allowFileUploadExtensions());
        return preg_match("/$file_types/i",$file_name);
    }

    function isImage($file_name) : bool {
        $image_types = 'xcf|odg|gif|jpg|png|bmp|jpeg|ico';
        return preg_match("/$image_types/i", $file_name);
    }

    function isVideo($file_name) : bool {
        $types = 'swf|flv|mp3|wma|mp4';
        return preg_match("/$types/i", $file_name);
    }

    function isAudio($file_name) : bool {
        $types = 'wav|flv|mp3|wma|m4a';
        return preg_match("/$types/i", $file_name);
    }

    function isImageByAbsPath($path) : bool {
        $a = getimagesize($path);
        $image_type = $a[2]??'';
        if (!empty($image_type) && in_array($image_type , array(IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG,IMAGETYPE_BMP))) {
            return true;
        }
        return false;
    }

    function imageExtensions() : array {
        return ['jpg','jpeg','gif','png','bmp','jfif'];
    }

    function allowFileUploadExtensions() : array {
        return ['gif','png','jpg','jpeg','tiff','tif','pdf', 'csv', 'xls', 'xlsx', 'doc', 'docx', 'rtf', 'ppt', 'pptx',
            'mp3', 'mp4', 'mpeg', 'mpg', 'mpeg', 'wma', 'wav', 'avi', 'mov', 'acc', 'flac', 'm4a', 'ai', 'psd',
        ];
    }

    function formatSizeUnits($bytes) : string {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    function maxFileSize() : int {
        // 1MB = 1048576 Bytes (1*1024*1024)
        $size = 20 * 1048576; // 20MB
        return $size;
    }

    function validateFileSize($file_size, $max_size = null) : array {
        $response = ['is_success' => true, 'message' => 'File size is ok'];
         $file_size = is_array($file_size) && isset($file_size['size'])  ? $file_size['size'] : $file_size;
         $allow_max_size = $this->maxFileSize();
         if ($file_size == 0) {
            $response = ['is_success' => false, 'message' => 'File size 0 is not allowed'];
         } else if ($max_size && $file_size > $max_size) {
            $size_with_unit = $this->formatSizeUnits($max_size);
            $response = ['is_success' => false, 'message' => 'File size can not be more than '. $size_with_unit];
         } else if ($file_size > $allow_max_size) {
            $size_with_unit = $this->formatSizeUnits($max_size);
            $response = ['is_success' => false, 'message' => 'File size can not be more than '. $size_with_unit];
         }
         return $response;
    }

    function validateFileExtension($file, $allow_extensions = []): array {
        $response = [
            'is_success' => true,
            'message' => 'File extension allowed.'
        ];
        $file_name = basename($file);
        $ext = strtolower($this->getExt($file_name));
        if (!empty($allow_extensions) && !in_array($ext, $allow_extensions)) {
           $response = [
                'is_success' => false,
                'message' => 'Wrong file format. Please upload the file in proper format ('.implode(', ', $allow_extensions).').'
            ];
        } else if (!in_array($ext, $this->allowFileUploadExtensions())) {
           $response = [
                'is_success' => false,
                'message' => 'File with extension .'. $ext . ' not allowed to upload.'
            ];
        }
        return $response;
    }

    function validateDimension($file, $dimension = []) : array {
        $response = ['is_success' => true, 'message' => 'Dimension is ok'];
        if (!empty($dimension) && isset($file["tmp_name"])) {
            list($width, $height) = getimagesize($file["tmp_name"]);
            if (isset($dimension["min_width"]) && isset($dimension["min_height"])) {
                $min_width = $dimension["min_width"]; $min_height = $dimension["min_height"];
                if (($width < $min_width) || ($height < $min_height)) {
                    $response = ['is_success' => false, 'message' => 'image size must be greater than '. $min_width . 'x' . $min_height . ' pixel.'];
                }
            }
        }
        return $response;
    }

    function upload($src, $dest) : bool {
        $ret = false;
        $dest = $this->clean($dest);
        $baseDir = dirname($dest);
        if (is_writeable($baseDir) && move_uploaded_file($src, $dest)) {
            $ret = true;
        }
        return $ret;
    }

    function clean($path) : string {
        return preg_replace('#[/\\\\]+#', DIRECTORY_SEPARATOR, trim($path));
    }

    function download($absolute_path) : bool {
        ob_start();
        if (!is_file($absolute_path)) {
            return false;
        }
        $ext = strtolower($this->getExt($absolute_path));

        // required for IE, otherwise Content-disposition is ignored
        if (ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', 'Off');
        }
        switch ($ext) {
            case "pdf":
                $content_type = "application/pdf";
                break;
            case "exe":
                $content_type = "application/octet-stream";
                break;
            case "rar":
            case "zip":
                $content_type = "application/zip";
                break;
            case "txt":
                $content_type = "text/plain";
                break;
            case "doc":
                $content_type = "application/msword";
                break;
            case "xls":
                $content_type = "application/vnd.ms-excel";
                break;
            case "ppt":
                $content_type = "application/vnd.ms-powerpoint";
                break;
            case "gif":
                $content_type = "image/gif";
                break;
            case "png":
                $content_type = "image/png";
                break;
            case "jpeg":
            case "jpg":
                $content_type = "image/jpg";
                break;
            case "mp3":
                $content_type = "audio/mpeg";
                break;
            default:
                $content_type = "application/force-download";
        }

        header("Pragma: public"); // required
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false); // required for certain browsers
        header("Content-Type: $content_type");
        //quotes to allow spaces in filenames
        header("Content-Disposition: attachment; filename=\"" . basename($absolute_path) . "\";");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . filesize($absolute_path));
        ob_clean();
        flush();
        readfile($absolute_path);
        return true;
    }

    function getExt($file) : string {
        return pathinfo($file, PATHINFO_EXTENSION);
    }

    function createFolder($path, $mode = 0777) : bool {
        $folder_permissions = $mode;
        $folder = $path;
        if (strlen($folder) > 0) {
            if (!is_dir($folder) && !is_file($folder)) {

                switch((int)$folder_permissions) {
                    case 777:
                        mkdir($folder, 0777, true);
                        break;
                    case 705:
                        mkdir($folder, 0705, true);
                        break;
                    case 666:
                        mkdir($folder, 0666, true);
                        break;
                    case 644:
                        mkdir($folder, 0644, true);
                        break;
                    case 755:
                    default:
                        mkdir($folder, 0755, true);
                        break;
                }
                //@JFolder::create($folder, $folder_permissions );
                if (isset($folder)) {
                    $this->writeFile($folder . DIRECTORY_SEPARATOR . "index.html", "<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>");
                }
                // folder was not created
                if (!is_dir($folder)) {
                    $errorMsg = "CreatingFolder";
                    return false;
                }
            } else {
                $errorMsg = "Folder Already Exists";
                return false;
            }
        } else {
            $errorMsg = "Folder Name Empty";
            return false;
        }
        return true;
    }

    function writeFile($file, $buffer) {
        if (!is_file(dirname($file))) {
            $fp = fopen($file, "w+");
            fwrite($fp, $buffer);
            fclose($fp);
        }
    }

    function setUniqueName ($filePath, $fileName) {
        $fileName = $this->makeSafe($fileName);
        if (file_exists($filePath . DIRECTORY_SEPARATOR . $fileName)) {
            $this->_uploadimgname = time() . "_".$fileName;
        }
        else{
            $this->_uploadimgname =  $fileName;
        }
    }

    function makeSafe($file) : string {
        $regex = ['#(\.){2,}#', '#[^A-Za-z0-9\.\_\- ]#', '#^\.#', '/\s+/'];
        return preg_replace($regex, '_', $file);
            /*
             Replacing following character by _
            \/;,:$#*%^&(){}[]~<>?"'|
            */
    }
}