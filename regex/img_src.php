<?php
require '../global_config.php';

use App\Logger\Log;
$content = file_get_contents('img_src_content.txt');
$regex = '/<img.*?src=["|\'](.*?)["|\']/';
$regex2 = '/<img\s+[^>]*src=["\']([^"\']+)["\']/';
preg_match_all($regex, $content, $matches);
preg_match($regex, $content, $match);

Log::write('preg_match', 'img_src', 'regex');
Log::write($match, 'img_src', 'regex');

Log::write('preg_match_all', 'img_src', 'regex');
Log::write($matches, 'img_src', 'regex');


echo '<pre>';
echo print_r($match[1]);
echo '</pre>';
echo '<pre>';
echo print_r($matches[1]);
echo '</pre>';