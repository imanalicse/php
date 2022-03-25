<?php

class SendGridFunction
{
    public function sendEmail()
    {
        $sendgrid_api_key = Configure::Read('test_sendgrid_api_key');
        //$email = new \SendGrid\Mail\Mail();
        $to = "bmimanali@gmail.com";
        $email = new Mailer('default');
        $email->setTransport('sendgrid');
        $email->setFrom("iman@bitmascot.com", "Iman RGS Test");
        $email->setSubject("Sending with SendGrid is Fun RGS");
        $email->addTo($to, "Bm Iman");
        $email->setEmailFormat('html');

        $email_body = 'and easy to do anywhere, even with PHP';
        $sendemail = $email->deliver($email_body);
        $this->sessionWrite('sendgrid_response', $sendemail);
        $sendgrid_response = $this->sessionRead('sendgrid_response');
        $sendgrid_response = $this->parseSendGridSendEmailResponse($sendgrid_response);

        echo "<pre>";
        print_r($sendgrid_response);
        echo "</pre>";

        $tracker_data = [];
        $tracker_data['model_id'] = 100;
        $tracker_data['model_name'] = EmailTrackerModel::ORDER;
        $tracker_data['email_type'] = EmailType::ORDER;
        $tracker_data['multiple_email_model_name'] = 'SendOrderMails';
        $tracker_data['multiple_email_model_id'] = 120;
        $tracker_data['to_email'] = strtolower($to);
        $email_tracker = $this->getComponent('EmailFunctions')->saveEmailTracker($sendgrid_response, $tracker_data);
        echo "<pre>";
        print_r($email_tracker);
        echo "</pre>";

        die('===End=====');
    }


    public function saveEmailTracker($send_email_response, $receive_data)
    {
        try {
            $data = [];
            $data['university_id'] = $this->getComponent('CommonFunction')->getUniversityId();
            $data['email_transport'] = $send_email_response['email_transport'];
            $data['status_code'] = $send_email_response['status_code'];
            $data['tracker_id'] = $send_email_response['tracker_id'];
            $data['is_success'] = $send_email_response['is_success'];
            $this->controller->saveLog('email_tracker', $send_email_response['tracker_id'], $send_email_response['response']);

            $data['model_id'] = $receive_data['model_id'];
            $data['model_name'] = $receive_data['model_name'];
            $data['email_type'] = $receive_data['email_type'];
            $data['multiple_email_model_name'] = $receive_data['multiple_email_model_name'] ?? '';
            $data['multiple_email_model_id'] = $receive_data['multiple_email_model_id'] ?? null;
            $data['to_email'] = $receive_data['to_email'];
            $data['is_debug'] = $receive_data['is_debug'] ?? 0;

            $entity = $this->EmailTrackers->newEmptyEntity();
            $entity = $this->EmailTrackers->patchEntity($entity, $data);
            $email_tracker = $this->EmailTrackers->save($entity);
            if ($email_tracker) {
                return $email_tracker->id;
            }
        } catch (\Exception $exception) {
            $this->controller->saveLog('', 'email_tracker_error', 'Error: Unable to save EmailTrackers:' . $exception->getMessage());
        }
        return null;
    }

    public function parseSendGridSendEmailResponse($sendgrid_response) {
        try {
            $response = [];
            $apiResponse = $sendgrid_response['apiResponse'];
            $status_code = $apiResponse->getStatusCode();
            $message_id = $apiResponse->getHeaderLine('X-Message-Id');
            if ($status_code != 202) {
                $message = '<h2>Seng Grid error</h2>';
                $message .= '<div>ULR: ' . Router::fullBaseUrl() . '</div>';
                $message .= '<div>Method name: parseSendGridSendEmailResponse</div>';
                $message .= '<div>Error Reason: '.$apiResponse->getReasonPhrase() .'<div>';
                $message .= 'Response data: '.$this->json_encode($apiResponse);
                $this->getComponent('EmailHandler')->sendViaPhpMail(ErrorEmailTo::EMAIL_ERROR, 'Error in sendGrid response', $message);
                $this->controller->saveLog('', 'email_tracker_error', 'Error: parseSendGridSendEmailResponse not 202:'. $this->json_encode($apiResponse));
            }
            $response['is_success'] = $apiResponse->isSuccess();
            $response['status_code'] = $status_code;
            $response['tracker_id'] = $message_id;
            $response['email_transport'] = EmailTransport::SENDGRID;
            $response['response'] = $sendgrid_response;
            return $response;
        } catch (\Exception $exception) {
            $this->controller->saveLog('', 'email_tracker_error', 'Error: parseSendGridSendEmailResponse:' . $exception->getMessage());
        }
    }
}