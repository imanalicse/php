<?php

class FileHandler
{
    function uploadFileNew($file, $filepath = null, $allow_extensions = [], $max_size = null, $new_file_name = null) : array
    {
        $response = [
            'is_success' => false,
            'message' => 'File not found'
        ];

        if (isset($file['name']) && !empty($file['name'])) {
//            $validate_file_extension = $this->validateFileExtension($file['name'], $allow_extensions);
//            if (!$validate_file_extension['is_success']) {
//                return $validate_file_extension;
//            }
//
//            $validate_file_size = $this->validateFileSize($file['size'], $max_size);
//            if (!$validate_file_size['is_success']) {
//                return $validate_file_size;
//            }
//
//            if(!$filepath) {
//                $controller_name = strtolower($this->getController()->name);
//                $filepath = WWW_ROOT.'uploads'.DS.$controller_name;
//            }
//
            if (!is_dir($filepath) && !is_file($filepath)) {
                $this->createFolder($filepath,'0775');
            }

            //set image name
            $new_file_name = $new_file_name ?? $file['name'];
            $this->setUniqueName($filepath, $new_file_name);
            $filepath = $filepath. DS . $this->_uploadimgname;

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

    function upload($src, $dest){
        $ret = false;
        $dest = $this->clean($dest);
        $baseDir = dirname($dest);
        if (is_writeable($baseDir) && move_uploaded_file($src, $dest)) {
            $ret = true;
        }
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

    function download($abspath)
    {
        ob_start();
        $file = basename($abspath);
        if (!is_file($abspath)) {
            return false;
        }

        $size = filesize($abspath);
        $ext = strtolower($this->getExt($abspath));

        // required for IE, otherwise Content-disposition is ignored
        if (ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', 'Off');
        }
        switch ($ext) {
            case "pdf":
                $ctype = "application/pdf";
                break;
            case "exe":
                $ctype = "application/octet-stream";
                break;
            case "rar":
            case "zip":
                $ctype = "application/zip";
                break;
            case "txt":
                $ctype = "text/plain";
                break;
            case "doc":
                $ctype = "application/msword";
                break;
            case "xls":
                $ctype = "application/vnd.ms-excel";
                break;
            case "ppt":
                $ctype = "application/vnd.ms-powerpoint";
                break;
            case "gif":
                $ctype = "image/gif";
                break;
            case "png":
                $ctype = "image/png";
                break;
            case "jpeg":
            case "jpg":
                $ctype = "image/jpg";
                break;
            case "mp3":
                $ctype = "audio/mpeg";
                break;
            default:
                $ctype = "application/force-download";
        }

        header("Pragma: public"); // required
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false); // required for certain browsers
        header("Content-Type: $ctype");
        //quotes to allow spaces in filenames
        header("Content-Disposition: attachment; filename=\"" . $file . "\";");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . $size);
        ob_clean();
        flush();
        readfile($abspath);
        return true;
    }

    function getExt($file) {
        $ext = trim(substr($file,strrpos($file,".")+1,strlen($file)));
        return $ext;
    }

    function createFolder($path, $mode = 0777) {
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
        if( file_exists($filePath.DS.$fileName) ){
            $this->_uploadimgname = time() . "_".$fileName;
        }
        else{
            $this->_uploadimgname =  $fileName;
        }
    }

    function setFileUniqueName ($filePath, $fileName) {
        $fileName = $this->makeSafe($fileName);
        $unique_name = $fileName;
        if (file_exists($filePath . DIRECTORY_SEPARATOR . $fileName)) {
            $unique_name = time() . "_" . $fileName;
        }
        return $unique_name;
    }

    function makeSafe($file) {
        $regex = ['#(\.){2,}#', '#[^A-Za-z0-9\.\_\- ]#', '#^\.#', '/\s+/'];
        return preg_replace($regex, '_', $file);
            /*
             Replacing following character by _

            \
            /
            ;
            ,
            :
            $
            #
            *
            %
            ^
            &
            (
            )
            {
            }
            [
            ]
            ~
            <
            >
            ?
            "
            '
            |
            */
    }

    function isImage( $fileName ) {
        static $imageTypes = 'xcf|odg|gif|jpg|png|bmp|jpeg|ico';
        return preg_match("/$imageTypes/i",$fileName);
    }

    function isVideo( $fileName ) {
        static $imageTypes = 'swf|flv|mp3|wma|mp4';
        return preg_match("/$imageTypes/i",$fileName);
    }

    function isAudio( $fileName ) {
        static $imageTypes = 'wav|flv|mp3|wma|m4a';
        return preg_match("/$imageTypes/i",$fileName);
    }
}