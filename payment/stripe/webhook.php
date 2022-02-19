<?php
include "include.php";

waLog("init_stripe_webhook", "stripe_webhook");
$webhook_response = @file_get_contents("php://input");
$webhook_data = json_decode($webhook_response, true); //If json data

waLog("stripe_webhook_data", "stripe_webhook");
waLog($webhook_data, "stripe_webhook");