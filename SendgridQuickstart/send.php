<?php

require '../vendor/autoload.php';
use App\DotEnv;
use App\SendgridQuickstart\EmailAction;
use App\MySQL\QueryBuilder;

$a = new EmailAction();
$a->sendEmail();