<?php
class A
{

    function securepayCallbackUrl()
    {
        $this->viewBuilder()->layout('ajax');
        $this->SecurepayOrderSession = TableRegistry::get('SecurepayOrderSessions');

        $mode = Configure::read("Transection_Mode");
        $path = "secureframe";
        $file = "callbackurl";
        if (isset($_REQUEST['refid'])/* && !empty($_REQUEST)*/) {

            $this->saveLog($path, $file, json_encode($_REQUEST));
            $cartdata = $this->SecurepayOrderSession->find()->where(['order_code_id' => $_REQUEST['refid']])->first();
            $cart_dt = json_decode($cartdata->session_order, true);
            $student_dt = json_decode($cartdata->session_student_info, true);
            $paymentInfo = $this->OrderCalculation->getPaymentInfo('securepay');

            if ($mode == "test") {
                $paymentInfo['transaction_mode'] = 0;
            } else if ($mode == "live") {
                $paymentInfo['transaction_mode'] = 1;
            }
            $securepay_data['title'] = $paymentInfo['title'];
            $securepay_data['payment_method_name'] = $paymentInfo['payment_method_name'];
            $securepay_data['vendor_name'] = $paymentInfo['vendor_name'];
            $securepay_data['vendor_password'] = $paymentInfo['vendor_password'];
            $securepay_data['transaction_mode'] = $paymentInfo['transaction_mode'];
            $securepay_data['test_url'] = $paymentInfo['test_url'];
            $securepay_data['production_url'] = $paymentInfo['production_url'];
            $cart_dt['payment'][$paymentInfo['payment_method_code']] = $securepay_data;

            $cart_dt['payment']['payment_method_code'] = $paymentInfo['payment_method_code'];
            $cart_dt['payment']['payment_reference_number'] = $_REQUEST['txnid'];
            $cart_dt['payment']['payment_reference'] = "";
            $cart_dt['payment']['payment_response_code'] = $_REQUEST['rescode'];
            $cart_dt['payment']['payment_response_text'] = $_REQUEST['restext'];
            $cart_dt['payment']['securepay_order_id'] = $_REQUEST['refid'];
            $cart_dt['payment']['securepay_order_code'] = $_REQUEST['refid'];
            $cart_dt['payment']['Transection_Mode'] = $mode;
            $cart_dt['payment']['base_order_total'] = $_REQUEST['amount'] / 100;
            $cart_dt['payment']['base_currency_code'] = "AUD";
            if ($_REQUEST['rescode'] == '00' || $_REQUEST['rescode'] == '08') {
                if ($this->checkMaxTicketPerExceed($cart_dt['event'], $cart_dt['ticket_info']) /*&& $this->checkTotalTicket($cart_dt['event'],$cart_dt['ticket_info'])*/) {
                    //if(1==1){


                    $data['session_order'] = json_encode($cart_dt);

                    $securepayOrderSession = $this->SecurepayOrderSession->patchEntity($cartdata, $data);

                    $this->SecurepayOrderSession->save($securepayOrderSession);


                } else {
                    $cart_dt['error']['status'] = 'Error';
                    $cart_dt['error']['msg'] = 'Ticket is not available now';
                    $data['session_order'] = json_encode($cart_dt);

                    $securepayOrderSession = $this->SecurepayOrderSession->patchEntity($cartdata, $data);

                    $this->SecurepayOrderSession->save($securepayOrderSession);
                    $this->sendSecurepayErrorEmailAdmin($cart_dt, $_REQUEST);
                }
            } else {
                $data['session_order'] = json_encode($cart_dt);

                $securepayOrderSession = $this->SecurepayOrderSession->patchEntity($cartdata, $data);

                $this->SecurepayOrderSession->save($securepayOrderSession);
            }
            /**/

        } elseif ($mode == 'test') {
            $cart_dt['payment']['payment_method_code'] = 'securepay';
            $cart_dt['payment']['payment_reference_number'] = 'asdfasdf';
            $cart_dt['payment']['payment_reference'] = "";
            $cart_dt['payment']['payment_response_code'] = 'asdfasd';
            $cart_dt['payment']['payment_response_text'] = 'asdfasd';
            $cart_dt['payment']['securepay_order_id'] = 'asdfasdf';
            $cart_dt['payment']['securepay_order_code'] = 'asdfasdf';
            $cart_dt['payment']['Transection_Mode'] = $mode;
            $cart_dt['payment']['base_order_total'] = $this->OrderCalculation->calculate_order_total_amount();
            $cart_dt['payment']['base_currency_code'] = "AUD";
            $this->request->session()->write('Order.payment', $cart_dt['payment']);
        }
        die();
    }

    public function processOrder()
    {
        $this->ValidationCheck->is_valid_cart();
        $this->getAndSetSecurepayCartArray();//$this->request->session()->read('Order');
        $cart = $this->request->session()->read('Order');
        if (isset($cart['error']['status']) && $cart['error']['status'] == 'Error') {
            $this->Flash->adminError($cart['error']['msg'], ['key' => 'admin_error']);
            $this->redirect(array('action' => 'securepayerror'));
        }


        if (empty($cart['products'])) {
            return $this->redirect("/");
        }

        $this->checkCartIsEmpty($cart);
        $customer = $cart['customer'];

        $check_pre_book = $this->OrderCalculation->checkPrebooking($customer, $cart);

        if ($check_pre_book) {

            $user = $this->request->session()->read('Auth.User');

            if ($user['role_id'] != 2) {
                $this->request->session()->delete('customer');
                $this->request->session()->delete('Order.customer');
                $msg = "This " . $this->student_type . " has already prebooked. Please try another " . $this->student_type . ".";
            } else {
                $msg = "You've already prebooked studio session.";
            }
            $this->Flash->adminError($msg, ['key' => 'admin_error']);


        }


        $userId = $cart['customer']['id'];
        $event_reference_code = $this->request->session()->read('Order.reference_code.reference_code');

        $studentReferenceCode = $this->createReferenceCodeForTicket($cart);

        /* END: Student Reference Code */
        $student = $this->get_logged_in_student_info($userId);
        $this->OrderReferenceCodes = TableRegistry::get('OrderReferenceCodes');
        $orderrefcode = $this->OrderReferenceCodes->find()->select(['id', 'reference_code'])->where(['user_id' => $userId, 'event_reference_code' => $event_reference_code])->order(['id' => 'DESC'])->first();


        if (empty($orderrefcode)) {

            if (!empty($studentReferenceCode)) {
                $student_qr_code_image = $this->QrCodeHandler->write_qr_code($studentReferenceCode, 'uploads/' . $cart['University']['subdomain'] . '/files/qrcode/student/');
                $this->log($student_qr_code_image);
                $this->request->session()->write('Order.order_qr_code_image', $student_qr_code_image);
                if (!empty($student)) {
                    $students = TableRegistry::get('Students');
                    $student = $students->get($student->id); // Return student with id = $id (primary_key of row which need to get updated)
                    // $student->student_qr_code_image = $student_qr_code_image;
                    $student->southam_id = $studentReferenceCode;

                    $students->save($student);
                } else {
                    $msg = "Invalid " . $this->student_type . " Id.";
                    $this->Flash->adminError($msg, ['key' => 'admin_error']);
                }
            }
        }


        /* START: Payment */
        $is_regalia_product_exists = $this->check_regalia_product();
        $is_valid_student_id_exists = $this->check_valid_student();

        if ($is_regalia_product_exists && !$is_valid_student_id_exists) {
            $this->Session->setFlash('Invalid ' . $this->student_type . ' Id.');
            $this->redirect('/regalia');
        }
        //end of checking valid student


        $cart = $this->request->session()->read('Order');
        if (empty($cart['checkout_uuid'])) {
            $checkout_uuid = uniqid();
            $path = "orders/" . date('Ymd');
            $file = date('YmdHis');
            $cart['checkout_uuid'] = $checkout_uuid;
            $cart['log_path'] = $path;
            $cart['log_file'] = $file;
            $this->request->session()->write('Order', $cart);
        } else {
            $checkout_uuid = @$cart['checkout_uuid'];
            $path = @$cart['log_path'];
            $file = @$cart['log_file'];

            $logData = "\n\n\n------------------------------Try Again-----------------------------------------\n\n\n";
            $this->saveLog($path, $file, json_encode($logData));
        }

        $logData = [
            'checkout_uuid' => $checkout_uuid,
            'log_type' => 'cart_data_before_payment',
            'cart' => $cart,
            'studentReferenceCode' => $studentReferenceCode
        ];
        $this->saveLog($path, $file, json_encode($logData));


        if (!empty($cart['ticket_info'])) {
            $status = $this->EventHandler->validateEventData($cart['event_id'], $cart, true);
            if (!empty($status['error'])) {
                $error_message = __($status['msg']);
                $this->Flash->adminError($error_message, ['key' => 'admin_error']);
                return $this->redirect(array('action' => 'payments'));
            }
        }


        if ($cart['payment']['payment_method_code'] == 'cash') {
            $cart = $this->request->session()->read('Order');
            $orderId = $this->createNewOrder($cart, $studentReferenceCode);
        } else if ($cart['payment']['payment_method_code'] == 'eway') {

            if ($this->request->session()->check('Order.info.order_status'))
                $this->request->session()->delete('Order.info.order_status');
            $pay = array();
            $pay['orderTotal'] = $this->request->session()->read('Order.info.total');
            $pay["card_holder"] = $this->request->session()->read('Order.payment.eway.ccname');
            $pay["card_number"] = $this->request->session()->read('Order.payment.eway.ccnumber');
            $pay["card_expiry_month"] = $this->request->session()->read('Order.payment.eway.month');
            $pay["card_expiry_year"] = $this->request->session()->read('Order.payment.eway.year');
            $pay["cvv"] = $this->request->session()->read('Order.payment.eway.cvnumber');
            $pay["vendor_name"] = $this->request->session()->read('Order.payment.eway.vendor_name');
            $pay["vendor_password"] = $this->request->session()->read('Order.payment.eway.vendor_password');
            $pay["transaction_mode"] = $this->request->session()->read('Order.payment.eway.transaction_mode');
            $pay["test_url"] = $this->request->session()->read('Order.payment.eway.test_url');
            $pay["production_url"] = $this->request->session()->read('Order.payment.eway.production_url');


            if ($pay['transaction_mode'] != 1) {
                $pay['orderTotal'] = (int)$pay['orderTotal'];
            }


            //$this->log($pay,'payment_test');

            $result = $this->PaymentHandler->checkPayment($pay);
//debug($pay);die;
            if ($result && isset($result["ewayTrxnStatus"]) && $result["ewayTrxnStatus"] == true) {


                $response_details = explode(',', $result['ewayTrxnError']);

                $this->request->session()->write('Order.payment.payment_reference_number', $result['ewayTrxnNumber']);
                $this->request->session()->write('Order.payment.payment_reference', $result['ewayAuthCode']);
                $this->request->session()->write('Order.payment.payment_response_code', $response_details[0]);
                $this->request->session()->write('Order.payment.payment_response_text', $response_details[1]);

                $cart = $this->request->session()->read('Order');
                $orderId = $this->createNewOrder($cart, $studentReferenceCode);

            } else {

                //$this->log($result, 'payment_error');

                $error_message = __('Error in payment processing. Please try again.');
                $this->Flash->adminError($error_message, ['key' => 'admin_error']);
                return $this->redirect(array('action' => 'payments'));

            }

        } else if ($cart['payment']['payment_method_code'] == 'stripe') {

            if ($this->request->session()->check('Order.info.order_status'))
                $this->request->session()->delete('Order.info.order_status');
            $pay = array();
            $pay['amount'] = $this->request->session()->read('Order.info.total');
            $pay["card_holder"] = $this->request->session()->read('Order.payment.stripe.ccname');
            $pay["card_number"] = $this->request->session()->read('Order.payment.stripe.ccnumber');
            $pay["card_expiry_month"] = $this->request->session()->read('Order.payment.stripe.month');
            $pay["card_expiry_year"] = $this->request->session()->read('Order.payment.stripe.year');
            $pay["cvv"] = $this->request->session()->read('Order.payment.stripe.cvnumber');
            $pay["vendor_name"] = $this->request->session()->read('Order.payment.stripe.vendor_name');
            $pay["vendor_password"] = $this->request->session()->read('Order.payment.stripe.vendor_password');
            $pay["transaction_mode"] = $this->request->session()->read('Order.payment.stripe.transaction_mode');
            $pay["test_url"] = $this->request->session()->read('Order.payment.stripe.test_url');
            $pay["production_url"] = $this->request->session()->read('Order.payment.stripe.production_url');
            $pay["site_currency"] = $this->CurrencyHandler->currency_parems['site_currency']['site_currency_info']['currency_code'];
            $pay["base_currency"] = $this->CurrencyHandler->currency_parems['site_currency']['site_currency_info']['to_currency_code'];
            $pay["currency_ratio"] = $this->CurrencyHandler->currency_parems['site_currency']['site_currency_info']['to_currency_price'];


            $result = $this->StripePayment->pay($pay);


            if (!empty($result['status'])) {

                $this->request->session()->write('Order.payment.stripe_charge_history_id', $result['data']->charge_history_id);
                $this->request->session()->write('Order.payment.stripe_connect_id', $result['data']->stripe_connect_id);
                $this->request->session()->write('Order.payment.stripe_connect_email', $result['data']->stripe_connect_email);
                $this->request->session()->write('Order.payment.Transection_Mode', $result['data']->Transection_Mode);
                $this->request->session()->write('Order.payment.stripe_customer_id', $result['data']->stripe_customer_id);

                $this->request->session()->write('Order.payment.stripe_charge_id', $result['data']->id);
                $this->request->session()->write('Order.payment.payment_reference_number', $result['data']->balance_transaction);
                $this->request->session()->write('Order.payment.payment_reference', "");
                $this->request->session()->write('Order.payment.payment_response_code', 200);
                $this->request->session()->write('Order.payment.payment_response_text', $result['data']->status);

                $this->request->session()->write('Order.payment.base_order_total', ($result['data']->base_price->amount / 100));
                $this->request->session()->write('Order.payment.base_currency_code', strtoupper($result['data']->base_price->currency));

                $cart = $this->request->session()->read('Order');
                $orderId = $this->createNewOrder($cart, $studentReferenceCode);

            } else {

                //$this->log($result, 'payment_error');

                $error_message = __('Error in payment processing. Please try again.');
                $this->Flash->adminError($error_message, ['key' => 'admin_error']);
                return $this->redirect(array('action' => 'payments'));

            }

        } else if ($cart['payment']['payment_method_code'] == 'securepay') {
///

            if ($cart['payment']['payment_response_code'] == '08' || $cart['payment']['payment_response_code'] == '00') {

                $orderId = $this->createNewOrder($cart, $studentReferenceCode);
                $this->deleteSecurepaySession($cart['payment']['securepay_order_code']);
            } else {

                $error_message = __($cart['payment']['payment_response_text'] . '. Please try again.');
                $this->Flash->adminError($error_message, ['key' => 'admin_error']);
                return $this->redirect(array('action' => 'payments'));
            }


        }


        return $this->redirect(['action' => 'success']);


    }
}