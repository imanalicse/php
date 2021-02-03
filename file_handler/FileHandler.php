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
}