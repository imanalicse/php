<?php

enum SendgridEvent: string {
    case PROCESSED = 'processed';
    case DEFERRED = 'deferred';
    case DELIVERED = 'delivered';
    case OPEN = 'open';
    case CLICK = 'click';
    case BOUNCE = 'bounce';
    case DROPPED = 'dropped';
}