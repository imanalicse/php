<?php
namespace App\SendgridQuickstart;

use App\DotEnv;
use App\MySQL\QueryBuilder;
use App\SendgridQuickstart\Enum\EmailTrackerModel;
use App\SendgridQuickstart\Enum\EmailTransport;
use App\SendgridQuickstart\Enum\EmailType;


class EmailAction
{
    public function __construct()
    {
        (new DotEnv(__DIR__ . '/.env'))->load();
    }

    public function sendEmail()
    {
        $email_to = getenv('TO_EMAIL_ADDRESS');

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom(getenv('SENDGRID_FROM_EMAIL_ADDRESS'), getenv('SENDGRID_FROM_NAME'));
        $email->setSubject("Sending with sendgrid");
        $email->addTo($email_to, getenv('TO_USER_NAME'));
        $email->addContent(
            "text/html", "Hello <strong>". getenv('TO_USER_NAME') ."</strong>,
                        <p> Thank you for using sendgrid service</p>
                        "
        );
        $sendgrid_api_key = getenv('SENDGRID_API_KEY');
        $sendgrid = new \SendGrid($sendgrid_api_key);
        try {
            $sendgrid_response = $sendgrid->send($email);
            if ($sendgrid_response->statusCode() == 202) {
                $sendgrid_response = $this->parseSendGridSendEmailResponse($sendgrid_response);
                $tracker_data = [];
                $tracker_data['model_id'] = 100;
                $tracker_data['model_name'] = EmailTrackerModel::ORDER;
                $tracker_data['email_type'] = EmailType::ORDER;
                $tracker_data['to_email'] = strtolower($email_to);
                $email_tracker = $this->saveEmailTracker($sendgrid_response, $tracker_data);
                echo "<pre>";
                print_r($email_tracker);
                echo "</pre>";
            }
            else {
                $body = json_decode($sendgrid_response->body(), true);
                $errors = $body["errors"];
                foreach ($errors as $error) {
                    echo "<pre>";
                    print_r($error);
                    echo "</pre>";
                }
            }
        } catch (Exception $e) {
            echo 'Send message exception: '. $e->getMessage() ."<br/>";
        }

        die('===End=====');
    }


    public function saveEmailTracker($send_email_response, $receive_data)
    {
        try {
            $data = [];
            $data['email_transport'] = $send_email_response['email_transport'];
            $data['status_code'] = $send_email_response['status_code'];
            $data['tracker_id'] = $send_email_response['tracker_id'];
            $data['is_success'] = $send_email_response['is_success'];
            //$this->controller->saveLog('email_tracker', $send_email_response['tracker_id'], $send_email_response['response']);

            $data['model_id'] = $receive_data['model_id'];
            $data['model_name'] = $receive_data['model_name'];
            $data['email_type'] = $receive_data['email_type'];
            $data['multiple_email_model_name'] = $receive_data['multiple_email_model_name'] ?? '';
            $data['multiple_email_model_id'] = $receive_data['multiple_email_model_id'] ?? 0;
            $data['to_email'] = $receive_data['to_email'];
            $data['is_debug'] = $receive_data['is_debug'] ?? 0;

            $mysqli = new mysqli("localhost","root","","imanalicse");
            if ($mysqli -> connect_errno) {
              echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
              exit();
            }

            $sql = "SELECT Lastname, Age FROM Persons ORDER BY Lastname";
            $result = $mysqli -> query($sql);
            // Fetch all
            $result -> fetch_all(MYSQLI_ASSOC);
            // Free result set
            $result -> free_result();
            $mysqli -> close();


//            $entity = $this->EmailTrackers->newEmptyEntity();
//            $entity = $this->EmailTrackers->patchEntity($entity, $data);
//            $email_tracker = $this->EmailTrackers->save($entity);
//            if ($email_tracker) {
//                return $email_tracker->id;
//            }
        } catch (\Exception $exception) {
            echo $exception->getMessage();
            //$this->controller->saveLog('', 'email_tracker_error', 'Error: Unable to save EmailTrackers:' . $exception->getMessage());
        }
        return null;
    }

    public function parseSendGridSendEmailResponse($sendgrid_response) {
        try {
            $response = [];
            $headers = $sendgrid_response->headers(true);
            $status_code = $sendgrid_response->statusCode();
            $message_id = $headers['X-Message-Id'];
            if ($status_code != 202) {
                $message = '<h2>Seng Grid error</h2>';
                $message .= '<div>Error Reason: '.$sendgrid_response->getReasonPhrase() .'<div>';
                $message .= 'Response data: '.json_encode($sendgrid_response);
                //TODO notify system admin
            }
            $response['is_success'] = $status_code == 202;
            $response['status_code'] = $status_code;
            $response['tracker_id'] = $message_id;
            $response['email_transport'] = EmailTransport::SENDGRID;
            $response['response'] = $sendgrid_response;
            return $response;
        } catch (\Exception $exception) {
            //$this->controller->saveLog('', 'email_tracker_error', 'Error: parseSendGridSendEmailResponse:' . $exception->getMessage());
        }
    }
}