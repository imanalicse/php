<?php

require '../vendor/autoload.php';

use App\MySQL\QueryBuilder;
use App\SendgridQuickstart\EmailAction;
use App\SendgridQuickstart\Enum\EmailTrackerModel;
use App\SendgridQuickstart\Enum\EmailType;

$email_action = new EmailAction();

$to = "bmimanali@gmail.com";
$subject = "Sending with sendgrid";
$content =  "Hello <strong>User</strong>,
            <p>Thank you for using sendgrid service</p>
            ";

$query = new QueryBuilder();
$order_last_id =  $query->insert("orders", ["product_name" => "Product 1", "price" => 10.00]);

$attachments = [
    'file_name' => 'Successful-Game-Team.jpg',
    'file_patch' => 'https://www.bitmascot.com/wp-content/uploads/2016/09/Successful-Game-Team.jpg'
];
// $attachments = [];

$sendgrid_response = $email_action->sendEmail($to, $subject, $content, $attachments);
if ($sendgrid_response->statusCode() == 202) {
    $sendgrid_response = $email_action->parseSendGridSendEmailResponse($sendgrid_response);
    $tracker_data = [];
    $tracker_data['model_id'] = $order_last_id;
    $tracker_data['model_name'] = EmailTrackerModel::ORDER;
    $tracker_data['email_type'] = EmailType::ORDER;
    $tracker_data['to_email'] = strtolower($to);
    $email_tracker_row_id = $email_action->saveEmailTracker($sendgrid_response, $tracker_data);
    echo "<pre>";
    print_r('Last insert Id: '. $email_tracker_row_id);
    echo "</pre>";
}
else {
    echo "Errors:";
    $body = json_decode($sendgrid_response->body(), true);
    $errors = $body["errors"];
    foreach ($errors as $error) {
        echo "<pre>";
        print_r($error);
        echo "</pre>";
    }
}