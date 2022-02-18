<?php
require_once "functions.php";

waLog("sendgrid_webhook", "Init");
$webhook_response = @file_get_contents("php://input");
$webhook_data = json_decode($webhook_response, true); //If json data