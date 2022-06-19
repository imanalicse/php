<?php
namespace App\SendgridQuickstart;

use App\DotEnv;
use App\MySQL\QueryBuilder;
use App\SendgridQuickstart\Enum\EmailTransport;
use SendGrid\Mail\Attachment;


class EmailAction
{
    public function __construct()
    {
        (new DotEnv(__DIR__ . '/.env'))->load();
    }


    public function sendEmail($email_to, $subject, $content, $attachments = [])
    {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom(getenv('SENDGRID_FROM_EMAIL_ADDRESS'), getenv('SENDGRID_FROM_NAME'));
        $email->setSubject($subject);
        $email->addTo($email_to);
        $email->addContent("text/html", $content);

        if (!empty($attachments)) {
            $file_patch = $attachments['file_patch'] ?? '';
            $file_name = $attachments['file_name'] ?? basename($file_patch);
            if (!empty($file_patch) && !empty($file_name)) {
                $file_encoded = base64_encode(file_get_contents($file_patch));
                $attachment = new Attachment();
                $attachment->setContent($file_encoded);
                $attachment->setFilename($file_name);
                $email->addAttachment($attachment);
            }
        }
        $sendgrid_api_key = getenv('SENDGRID_API_KEY');
        $sendgrid = new \SendGrid($sendgrid_api_key);
        try {
            return $sendgrid->send($email);
        } catch (Exception $e) {
            return 'Send message exception: '. $e->getMessage() ."<br/>";
        }
    }


    public function saveEmailTracker($send_email_response, $receive_data) : ?int
    {
        try {
            $data = [];
            $data['email_transport'] = $send_email_response['email_transport'];
            $data['status_code'] = $send_email_response['status_code'];
            $data['tracker_id'] = $send_email_response['tracker_id'];
            $data['is_success'] = $send_email_response['is_success'];
            $data['model_id'] = $receive_data['model_id'];
            $data['model_name'] = $receive_data['model_name'];
            $data['email_type'] = $receive_data['email_type'];
            $data['to_email'] = $receive_data['to_email'];
            $data['is_debug'] = $receive_data['is_debug'] ?? 0;

            $query = new QueryBuilder();
            return $query->insert("sendgrid_email_trackers", $data);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
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