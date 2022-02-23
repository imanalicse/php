<?php
include "include.php";
use Stripe\Event as WebhookEvent;

waLog("init_stripe_webhook", "stripe_webhook2");
$webhook_response = @file_get_contents("php://input");
$webhook_data = json_decode($webhook_response, true); //If json data

waLog("stripe_webhook_data", "stripe_webhook");
waLog($webhook_data, "stripe_webhook");

if ($webhook_data['type'] == WebhookEvent::ACCOUNT_UPDATED) {
}

