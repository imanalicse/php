<?php
namespace App\SendgridQuickstart\Enum;

abstract class SendgridEvent {
    const PROCESSED = 'processed';
    const DEFERRED = 'deferred';
    const DELIVERED = 'delivered';
    const OPEN = 'open';
    const CLICK = 'click';
    const BOUNCE = 'bounce';
    const DROPPED = 'dropped';
}