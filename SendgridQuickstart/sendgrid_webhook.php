<?php

namespace App\SendgridQuickstart;

require '../vendor/autoload.php';

use App\Logger\Log;
use App\SendgridQuickstart\SendGridEventHandler;

Log::write('Sendgrid webhook called', 'email_tracker');

$webhook_response = @file_get_contents("php://input");
$webhook_data = json_decode($webhook_response, true); //If json data

$data = file_get_contents("php://input");
//$data = '[{"email":"bmimanali@gmail.com","event":"processed","send_at":0,"sg_event_id":"cHJvY2Vzc2VkLTIwOTAwNjQ1LWsyeW5NZzNYUzFxRFBtSElxc0ZKRkEtMA","sg_message_id":"k2ynMg3XS1qDPmHIqsFJFA.filterdrecv-74cbf9986b-cbq2r-1-624F0FC3-4E.0","smtp-id":"<k2ynMg3XS1qDPmHIqsFJFA@geopod-ismtpd-1-1>","timestamp":1649348547}, {"email":"bmimanali@gmail.com","event":"delivered","ip":"149.72.91.245","response":"250 2.0.0 OK  1649348549 13-20020a0562140dcd00b0044414ed9899si971187qvt.321 - gsmtp","sg_event_id":"ZGVsaXZlcmVkLTAtMjA5MDA2NDUtazJ5bk1nM1hTMXFEUG1ISXFzRkpGQS0w","sg_message_id":"k2ynMg3XS1qDPmHIqsFJFA.filterdrecv-74cbf9986b-cbq2r-1-624F0FC3-4E.0","smtp-id":"<k2ynMg3XS1qDPmHIqsFJFA@geopod-ismtpd-1-1>","timestamp":1649348549,"tls":1}]';
Log::write('sendgrid webhook events:', 'email_tracker');
Log::write($data, 'email_tracker');
$events = json_decode($data, true);
$event_handler = new SendGridEventHandler();
if (!empty($events)) {
    foreach ($events as $event) {
        $event_handler->updateMailTrackerByEvent($event);
    }
}