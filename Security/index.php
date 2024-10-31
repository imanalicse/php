<?php

use App\Security\Sanitizer\Sanitizer;

require '../global_config.php';
echo Sanitizer::tmpFolder();

echo $id = Sanitizer::purifyDOM('fdsafas');
