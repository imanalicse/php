<?php

class FileHandler
{
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