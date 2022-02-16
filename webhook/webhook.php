<?php
$webhook_response = @file_get_contents("php://input");
$webhook_data = json_decode($webhook_response, true); //If json data