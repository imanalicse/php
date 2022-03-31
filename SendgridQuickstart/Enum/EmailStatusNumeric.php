<?php
namespace App\SendgridQuickstart\Enum;

abstract class EmailStatusNumeric {
    const IS_NO_ACTION = 0;
    const IS_SENT = 1;
    const IS_REQUESTED = 2;
    const IS_PROCESSED = 3;
    const IS_DEFERRED = 4;
    const IS_OPENED = 5;
    const IS_CLICKED = 6;
    const IS_BOUNCED = 7;
    const IS_DROPPED = 8;
}