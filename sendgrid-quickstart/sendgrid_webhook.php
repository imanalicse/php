<?php

use Cake\Core\Configure;

require_once "functions.php";

waLog("sendgrid_webhook", "Init");
$webhook_response = @file_get_contents("php://input");
$webhook_data = json_decode($webhook_response, true); //If json data

$data = file_get_contents("php://input");
        $events = json_decode($data, true);

        $this->saveLog("", "email_tracker", 'webhookSendgrid->events='.$data);
        $this->saveLog("", "email_tracker", $events);

        if (!empty($events)) {
            foreach ($events as $event) {
                $this->getComponent('EmailTrackerEvent')->updateMailTrackerByEvent($event);
            }
        }