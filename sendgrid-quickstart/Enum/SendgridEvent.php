<?php
namespace App\Enum\Email;

/**
 * Class SendgridEvent
 * @package App\Enum\Email
 */
abstract class SendgridEvent {
    const PROCESSED = 'processed';
    const DEFERRED = 'deferred';
    const DELIVERED = 'delivered';
    const OPEN = 'open';
    const CLICK = 'click';
    const BOUNCE = 'bounce';
    const DROPPED = 'dropped';
}