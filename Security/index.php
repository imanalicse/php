<?php

use App\Security\Sanitizer\Sanitizer;
use App\Logger\Log;

require '../global_config.php';
// $html_content = Sanitizer::purifyDOM('fdsafas');
$input = "eval(compile('for x in range(1):\n import time\n time.sleep(20)','a','single'))";
Log::write($input, 'dom_purify', 'security');
$input = Sanitizer::purifyInput($input);
Log::write($input, 'dom_purify', 'security');
