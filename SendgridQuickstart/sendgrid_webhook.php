<?php

use App\Logger\Log;
use App\SendgridQuickstart\SendGridEventHandler;

Log::write('Sendgrid webhook called', 'email_tracker');

$webhook_response = @file_get_contents("php://input");
$webhook_data = json_decode($webhook_response, true); //If json data

$data = file_get_contents("php://input");
$events = json_decode($data, true);
Log::write('sendgrid webhook events:', 'email_tracker');
Log::write($events, 'email_tracker');

$event_handler = new SendGridEventHandler();
if (!empty($events)) {
    foreach ($events as $event) {
        $event_handler->updateMailTrackerByEvent($event);
    }
}