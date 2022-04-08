<?php
namespace App\SendgridQuickstart;

use App\Logger\Log;
use App\MySQL\QueryBuilder;
use App\SendgridQuickstart\Enum\EmailStatusNumeric;
use App\SendgridQuickstart\Enum\EmailType;
use App\SendgridQuickstart\Enum\SendgridEvent;

class SendGridEventHandler
{
    public function updateMailTrackerByEvent($event) {
        try {
            Log::write("===Event====", 'email_tracker');
            Log::write($event, 'email_tracker');
            $sg_message_id = $event['sg_message_id'];
            list($tracker_id) = explode('.', $sg_message_id);
            $conditions = [
                'tracker_id' => $tracker_id,
            ];
            if (isset($event['rgs_model_name']) && !empty($event['rgs_model_name'])) {
                $conditions['model_name'] = $event['rgs_model_name'];
            }
            if (isset($event['rgs_model_id']) && !empty($event['rgs_model_id'])) {
                $rgs_model_id = $event['rgs_model_id'];
                $rgs_model_id = str_replace(['\'', '"'], '', $rgs_model_id);
                $conditions['model_id'] = $rgs_model_id;
            }

           Log::write("Email Tracker Condition for tracker_id=$tracker_id", 'email_tracker');
           Log::write($conditions, 'email_tracker');

            $query_builder = new QueryBuilder();
            $email_tracker = $query_builder->get("sendgrid_email_trackers")->where($conditions)->find();

            Log::write($email_tracker, 'email_tracker');
            if (!empty($email_tracker)) {
                $this->saveEmailTrackerEvent($event, $email_tracker["id"]);

                $tracker_email = strtolower($email_tracker['to_email']);
                $event_email = strtolower($event['email']);
                Log::write("=tracker_email=$tracker_email, event_email=$event_email", 'email_tracker');
                if ($tracker_email == $event_email) {
                    Log::write('$email_tracker->id='.$email_tracker['id'],'email_tracker');
                    $updated_data = [];
                    switch ($event['event']) {
                        case SendgridEvent::DELIVERED:
                            $updated_data['is_delivered'] = 1;
                            break;
                        case SendgridEvent::PROCESSED:
                            $updated_data['is_process'] = 1;
                            break;
                        case SendgridEvent::OPEN:
                            $updated_data['is_opened'] = 1;
                            break;
                        case SendgridEvent::CLICK:
                            $updated_data['is_clicked'] = 1;
                            break;
                        case SendgridEvent::DEFERRED:
                            $updated_data['is_deferred'] = 1;
                            break;
                        case SendgridEvent::BOUNCE:
                            $updated_data['is_bounced'] = 1;
                            break;
                        case SendgridEvent::DROPPED:
                            $updated_data['is_dropped'] = 1;
                            break;
                    }
                    $query_builder = new QueryBuilder();
                    $query_builder->update("sendgrid_email_trackers", $updated_data, ["id" => $email_tracker["id"]]);

                    $this->updateEmailStatusForEvent($email_tracker, $event);
                }
            }
        }
        catch (\Exception $exception) {
            Log::write('Error: Unable to update EmailTrackers:' . $exception->getMessage(), 'email_tracker_error');
            Log::write($event, 'email_tracker_error');
        }
    }

    public function saveEmailTrackerEvent($event, $email_tracker_id) {
        try {
            $data = [];
            $data['email_tracker_id'] = $email_tracker_id;
            $data['event_name'] = $event['event'];
            $data['event_responses'] = json_encode($event);
            $data['created'] = date('Y-m-d H:i:s');
            $data['modified'] = date('Y-m-d H:i:s');
            (new QueryBuilder())->insert("sendgrid_email_tracker_events", $data);
        } catch (\Exception $exception) {
           Log::write('Error: Unable to save saveEmailTrackerEvent:' . $exception->getMessage(), 'email_tracker_error');
        }
    }

    public function updateEmailStatusForEvent($email_tracker, $event){
        if ($email_tracker['is_debug'] != 1) {
            switch ($email_tracker['email_type']) {
                case EmailType::ORDER:
                    $this->updateOrderEmailStatusForEvent($email_tracker, $event);
                    break;
                case EmailType::SLIDE:
                    $this->updateSlideEmailStatusForEvent($email_tracker, $event);
                    break;
            }
        }
    }

   public function updateOrderEmailStatusForEvent($email_tracker, $event) {
        $email_functions = new EmailFunctions();
        $is_send_order_email = $email_functions->isSentOrderEmail($email_tracker['model_id']);
        if (!$is_send_order_email) {
            try {
                switch ($event['event']) {
                    case SendgridEvent::DELIVERED:
                        $email_functions->updateOrderEmailStatus($email_tracker['model_id'], EmailStatusNumeric::IS_SENT);
                        break;

                    case SendgridEvent::PROCESSED:
                        $email_functions->updateOrderEmailStatus($email_tracker['model_id'], EmailStatusNumeric::IS_PROCESSED);
                        break;

                    case SendgridEvent::BOUNCE:
                        $email_functions->updateOrderEmailStatus($email_tracker['model_id'], EmailStatusNumeric::IS_BOUNCED);
                        //$this->undeliveredEmailNotificationToAdmin($email_tracker);
                        break;

                    case SendgridEvent::DROPPED:
                        $email_functions->updateOrderEmailStatus($email_tracker['model_id'], EmailStatusNumeric::IS_DROPPED);
                        //$this->undeliveredEmailNotificationToAdmin($email_tracker);
                        break;
                }
            } catch (\Exception $exception) {
               Log::write('Error: updateOrderEmailStatusForEvent for order:' . $exception->getMessage(), 'email_tracker_error');
            }
        }

        //$this->updateSendOrderEmailsStatusByEmailTrackerEvent($email_tracker, $event);
   }

   public function updateSlideEmailStatusForEvent($email_tracker, $event) {
        $is_send_slide_email = $this->controller->getComponent('EmailFunctions')->isSentSlideEmail($email_tracker['model_id'], $email_tracker['university_id']);
        if ($is_send_slide_email != EmailStatusNumeric::IS_SENT) {
            try {
                switch ($event['event']) {
                    case SendgridEvent::DELIVERED:
                        $this->controller->getComponent('EmailFunctions')->updateSlideEmailStatus($email_tracker['model_id'], EmailStatusNumeric::IS_SENT, $email_tracker['university_id']);
                        break;

                    case SendgridEvent::PROCESSED:
                        $this->controller->getComponent('EmailFunctions')->updateSlideEmailStatus($email_tracker['model_id'], EmailStatusNumeric::IS_PROCESSED, $email_tracker['university_id']);
                        break;

                    case SendgridEvent::BOUNCE:
                        $this->controller->getComponent('EmailFunctions')->updateSlideEmailStatus($email_tracker['model_id'], EmailStatusNumeric::IS_BOUNCED, $email_tracker['university_id']);
                        $this->undeliveredEmailNotificationToAdmin($email_tracker);
                        break;

                    case SendgridEvent::DROPPED:
                        $this->controller->getComponent('EmailFunctions')->updateSlideEmailStatus($email_tracker['model_id'], EmailStatusNumeric::IS_DROPPED, $email_tracker['university_id']);
                        $this->undeliveredEmailNotificationToAdmin($email_tracker);
                        break;
                }
            } catch (\Exception $exception) {
                $this->controller->saveLog('', 'email_tracker_error', 'Error: updateSlideEmailStatusForEvent for slide email event:' . $exception->getMessage());
            }
        }

        $this->updateSendEmailsStatusByEmailTrackerEvent($email_tracker, $event);
   }

    public function updateSendEmailsStatusByEmailTrackerEvent($email_tracker, $event)
    {
        try {
            $email_tracker_id = $email_tracker['id'];
            $this->SendEmails = $this->getDbTable('SendEmails');
            $send_email = $this->SendEmails->find()->where(['email_tracker_id' => $email_tracker_id])->first();
            if ($send_email->is_send_mail != EmailStatusNumeric::IS_SENT) {
                $is_send_mail = '';
                switch ($event['event']) {
                    case SendgridEvent::DELIVERED:
                        $is_send_mail = EmailStatusNumeric::IS_SENT;
                    break;
                    case SendgridEvent::PROCESSED:
                        $is_send_mail = EmailStatusNumeric::IS_PROCESSED;
                    break;
                    case SendgridEvent::BOUNCE:
                        $is_send_mail = EmailStatusNumeric::IS_BOUNCED;
                    break;
                    case SendgridEvent::DROPPED:
                        $is_send_mail = EmailStatusNumeric::IS_DROPPED;
                    break;
                }
                if ($is_send_mail) {
                    $data = [];
                    $data['is_send_mail'] = $is_send_mail;
                    $data['updated'] = date('Y-m-d H:i:s');
                    $patch_entity_data = $this->SendEmails->patchEntity($send_email, $data);
                    $this->SendEmails->save($patch_entity_data);
                }
            }
        } catch (\Exception $exception) {
            $this->controller->saveLog('', 'database_action_error', 'Error: Unable to update method updateSendEmailsStatusByEmailTrackerEvent:' . $exception->getMessage());
        }
    }

    public function updateSendOrderEmailsStatusByEmailTrackerEvent($email_tracker, $event)
    {
        try {
            $email_tracker_id = $email_tracker['id'];
            $this->SendOrderMails = $this->getDbTable('SendOrderMails');
            $send_order_email = $this->SendOrderMails->find()->where(['email_tracker_id' => $email_tracker_id])->first();
            if ($send_order_email->is_send_mail != EmailStatusNumeric::IS_SENT) {
                $is_send_mail = '';
                switch ($event['event']) {
                    case SendgridEvent::DELIVERED:
                        $is_send_mail = EmailStatusNumeric::IS_SENT;
                    break;
                    case SendgridEvent::PROCESSED:
                        $is_send_mail = EmailStatusNumeric::IS_PROCESSED;
                    break;
                    case SendgridEvent::BOUNCE:
                        $is_send_mail = EmailStatusNumeric::IS_BOUNCED;
                    break;
                    case SendgridEvent::DROPPED:
                        $is_send_mail = EmailStatusNumeric::IS_DROPPED;
                    break;
                }
                if ($is_send_mail) {
                    $data = [];
                    $data['is_send_mail'] = $is_send_mail;
                    $data['updated'] = date('Y-m-d H:i:s');
                    $patch_entity_data = $this->SendOrderMails->patchEntity($send_order_email, $data);
                    $this->SendOrderMails->save($patch_entity_data);
                }
            }
        } catch (\Exception $exception) {
            $this->controller->saveLog('', 'database_action_error', 'Error: Unable to update method updateSendOrderEmailsStatusByEmailTrackerEvent:' . $exception->getMessage());
        }
    }

   public function undeliveredEmailNotificationToAdmin($email_tracker)
    {
        $email_sent = false;
        try {
            $notification_model_id = '';
            $model_id = $email_tracker['model_id'];
            $model_name = $email_tracker['model_name'];
            $email_type = $email_tracker['email_type'];
            $university_id = $email_tracker['university_id'];
            $sub_domain = $this->getComponent('CommonFunction')->getSubDomainName($university_id);
            $this->controller->loadModel('University.Universities');
            $university = $this->controller->Universities->find()->where( array('subdomain' => $sub_domain))->first()->toArray();

            $to = $university['admin_email'] ? $university['admin_email'] : 'bakar@webalive.com.au';
            $from_email = 'hello@reedgraduations.com.au';
            $bcc = [];
            $subject = 'Email processing failed in '.$university['name'];
            $mail_body = '<div>Dear Admin,<br><br>The system was unable to process ';
            $this->log('====undeliveredEmailNotificationToAdmin====');
            $this->log('$email_type = '.$email_type);
            switch ($email_type) {
                case EmailType::ORDER:
                    $notification_model_id = $email_tracker['model_id'];
                    $mail_body .= 'email from <strong>'.$university['name'].'</strong>, for order id #'.$model_id.'.<br>';
                    break;
                case EmailType::SLIDE:
                    $student_id = $this->controller->getComponent('CommonFunction')->getStudentIDByModelId($model_id, $university_id);
                    $notification_model_id = $student_id;
                    $this->log('Student Id = '.$student_id);
                    $mail_body .= 'slide email from <strong>'.$university['name'].'</strong>, for Student ID #'.$student_id.'.<br>';
                    break;
            }

            $site_url = 'https://'.$university['url'];
            $mail_body .= "<br/>URL: <a href='$site_url'> $site_url </a><br/>";

            $mail_body .= '<p>Regards,<br>ReedGraduations</p></div>';

            $variables['logName'] = 'email_tracker';
            $email_sent = $this->getComponent('EmailHandler')->sendMail($from_email, $to, $subject, null, $variables, null, 'html', null, $bcc, null, $mail_body);
            $this->controller->saveLog('', 'email_tracker_admin', "Admin notification for University id = $university_id, model_name = $model_name, model_id = $model_id, is_sent = $email_sent, to=$to");
            $notification_data['university_id'] = $university_id;
            $notification_data['order_id'] = $notification_model_id;
            $notification_data['model_id'] = $model_id;
            $notification_data['model_name'] = $model_name;
            $notification_data['email_type'] = $email_type;
            $table_prefix = $university['table_prefix'];

            $notificationSave = $this->saveFailedEmailNotification($table_prefix, $notification_data);
            $this->controller->saveLog('', 'email_notification_log', "Save Notification = ".$notificationSave );
        } catch (\Exception $exception) {
            $this->controller->saveLog('', 'email_tracker_admin', "Admin notification error for University id = $university_id, model_name = $model_name, model_id = $model_id, is_sent = $email_sent : ". $exception->getMessage());
        }
    }

    public function saveFailedEmailNotification($table_prefix, $data){
        $data['created'] = date('Y-m-d H:i:s');
        $data['modified'] = date('Y-m-d H:i:s');

        $this->controller->saveLog('', 'email_notification_log', '====saveFailedEmailNotification====');
        $this->controller->saveLog('', 'email_notification_log', 'Table Prefix - '.$table_prefix.', Data - '.json_encode($data));
        try{
            $connection = ConnectionManager::get('organizations');
            $sql = "INSERT INTO ".$table_prefix."failed_email_notifications (`id`, `university_id`, `order_id`, `model_id`, `model_name`, `email_type`, `created`, `modified`) VALUES
                (NULL, '".$data['university_id']."', '".$data['order_id']."', '".$data['model_id']."', '".$data['model_name']."', '".$data['email_type']."', '".$data['created']."', '".$data['modified']."')";
            $this->controller->saveLog('', 'email_notification_log', '$sql - '.$sql);
            $connection->execute($sql);

            $this->controller->saveLog('', 'email_notification_log', 'Notification Save Successfully!');
            return true;
        } catch(Exception $e){
            //echo '<pre>===getOrderInfoById():Exception==='.__LINE__;pr($e->getMessage());echo '</pre>';
            $this->controller->saveLog('', 'email_notification_log', 'Failed to Save notification - ' . json_encode($e));
            return false;
        }
    }
}
