<?php

defined('_JEXEC') or die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');

/**
 *
 * a special type of 'commbank ':
 * @author Max Milbers
 * @author Valérie Isaksen
 * @version $Id: commbank.php 5148 2011-12-19 16:14:12Z alatak $
 * @package VirtueMart
 * @subpackage payment
 * @copyright Copyright (C) 2004-2008 soeren - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.org
 */
if (!class_exists('vmPSPlugin'))
    require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');

class plgVMPaymentCommbank extends vmPSPlugin {

    // instance of class
    public static $_this = false;

    function __construct(& $subject, $config) {
		parent::__construct($subject, $config);

	$this->_loggable = true;
	$this->tableFields = array_keys($this->getTableSQLFields());

	$varsToPush = array('commbank_merchant_email' => array('', 'char'),
	    'commbank_verified_only' => array('', 'int'),
	    'payment_currency' => array(0, 'int'),
	    'sandbox' => array(0, 'int'),
	    'sandbox_merchant_email' => array('', 'char'),
	    'payment_logos' => array('', 'char'),
	    'debug' => array(0, 'int'),
	    'status_pending' => array('', 'char'),
	    'status_success' => array('', 'char'),
	    'status_canceled' => array('', 'char'),
	    'countries' => array(0, 'char'),
	    'min_amount' => array(0, 'int'),
	    'max_amount' => array(0, 'int'),
	    'cost_per_transaction' => array(0, 'int'),
	    'cost_percent_total' => array(0, 'int'),
	    'tax_id' => array(0, 'int'),
        'virtualPaymentClientURL' => array('','char'),
        'vpc_Version' => array('','int'),
        'vpc_Command' => array('','char'),
        'vpc_AccessCode' => array('','char'),
        'vpc_MerchTxnRef' => array('','char'),
        'vpc_Merchant' => array('','char'),
        'vpc_SecurityHash' => array('','char'),
        'vpc_OrderInfo' => array('','char'),

	);

	$this->setConfigParameterable($this->_configTableFieldName, $varsToPush);

    }

    protected function getVmPluginCreateTableSQL() {
	    return $this->createTableSQL('Payment Commbank Table');
    }

    public function getVpcConfiguration(){
        $db = JFactory::getDBO();
        $sql = 'SELECT * FROM `' . '#__virtuemart_paymentmethods' . '` '
            . 'WHERE `payment_element` = ' .'"' .'commbank'.'"'; //die;
        $db->setQuery($sql);
        $res=$db->loadObject();
        $params=explode('|',$res->payment_params);
        $configuration=array();
        foreach($params as $param):
            $paramVal=explode('=',$param);
            $paramIndex=$paramVal[0];
            $pramValue=$paramVal[1];
            $configuration[$paramIndex]=$pramValue;
        endforeach;

        return $configuration;
    }
    //for order payment
    function getPaymentDone(){
        die('Found');
        $vpcConfiguration = $this->getVpcConfiguration();
        $vpcURL=str_replace('"','',stripcslashes($vpcConfiguration['virtualPaymentClientURL']));
        $vpcVersion=$vpcConfiguration['vpc_Version'];
        $vpcCommand=str_replace('"','',$vpcConfiguration['vpc_Command']);
        $vpcAccessCode=str_replace('"','',$vpcConfiguration['vpc_AccessCode']);
        $vpcMerchTxnRef=$vpcConfiguration['vpc_MerchTxnRef'];
        $vpcMerchant=str_replace('"','',$vpcConfiguration['vpc_Merchant']);
        $vpcOrderInfo=$vpcConfiguration['vpc_OrderInfo'];
        $vpcAmount='100';
        $vpcCardNum='4005550000000001';
        $vpcCardExp='1305';
        $vpcCardSecurityCode='123';
        $vpcTicketNo='123';
        $postData="vpc_Version=$vpcVersion&vpc_Command=$vpcCommand&vpc_AccessCode=$vpcAccessCode&vpc_MerchTxnRef=$vpcMerchTxnRef&vpc_Merchant=$vpcMerchant&vpc_OrderInfo=$vpcOrderInfo&vpc_Amount=$vpcAmount&vpc_CardNum=$vpcCardNum&vpc_CardExp=$vpcCardExp&vpc_CardSecurityCode=$vpcCardSecurityCode&vpc_TicketNo=$vpcTicketNo";
        //die;
        ob_start();

    // initialise Client URL object
        $ch = curl_init();

    // set the URL of the VPC
        curl_setopt ($ch, CURLOPT_URL, $vpcURL);
        curl_setopt ($ch, CURLOPT_POST, 1);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, $postData);


    //curl_setopt($ch, CURLOPT_CAINFO, "c:/temp/ca-bundle.crt");

    //turn on/off cert validation
    // 0 = don't verify peer, 1 = do verify
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);

    // 0 = don't verify hostname, 1 = check for existence of hostame, 2 = verify
    //curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 2);

    // connect
        curl_exec ($ch);

    // get response
        $response = ob_get_contents();

    // turn output buffering off.
        ob_end_clean();

    // set up message paramter for error outputs
        $message = "";

    // serach if $response contains html error code
        if(strchr($response,"<html>") || strchr($response,"<html>")) {;
            $message = $response;
        } else {
            // check for errors from curl
            if (curl_error($ch))
                $message = "curl_errno=". curl_errno($ch) . "<br/>" . curl_error($ch);
        }

    // close client URL
        //print_r($response);die;
        curl_close ($ch);
        // Extract the available receipt fields from the VPC Response
    // If not present then let the value be equal to 'No Value Returned'
        $map = array();

    // process response if no errors
        if (strlen($message) == 0) {
            $pairArray = split("&", $response);
            foreach ($pairArray as $pair) {
                $param = split("=", $pair);
                $map[urldecode($param[0])] = urldecode($param[1]);
            }
            $message         = $this->null2unknown($map, "vpc_Message");
        }        //die;


        $url = $this->_getUrlHttps($method);
        if($message=='Approved'){
            $new_status='C';
            $returnValue=1;
        }else {
            $returnValue=0;
        }
    }
    //end of order payment
    //
    function getTableSQLFields() {

        $SQLfields = array(
            'id' => ' tinyint(1) unsigned NOT NULL AUTO_INCREMENT ',
            'virtuemart_order_id' => ' int(11) UNSIGNED DEFAULT NULL',
            'order_number' => ' char(32) DEFAULT NULL',
            'virtuemart_paymentmethod_id' => ' mediumint(1) UNSIGNED DEFAULT NULL',
            'payment_name' => ' char(255) NOT NULL DEFAULT \'\' ',
            'payment_order_total' => 'decimal(15,5) NOT NULL DEFAULT \'0.00000\' ',
            'payment_currency' => 'char(3) ',
            'cost_per_transaction' => ' decimal(10,2) DEFAULT NULL ',
            'cost_percent_total' => ' decimal(10,2) DEFAULT NULL ',
            'tax_id' => ' smallint(1) DEFAULT NULL',
            'commbank_custom' => ' varchar(255)  ',
            'commbank_response_amount' =>'decimal(10,2)',
            'commbank_response_locale' =>'char(100)',
            'commbank_response_batch_no' =>'char(32)',
            'commbank_response_command'=> 'char(10)',
            'commbank_response_version'=> 'char(13)',
            'commbank_response_card_type'=> 'char(10)',
            'commbank_response_order_info'=>'char(50)',
            'commbank_response_receipnt_no'=>'char(50)',
            'commbank_response_merchant_id'=>'char(50)',
            'commbank_response_authorize_id'=>'char(50)',
            'commbank_response_transaction_no'=>'char(64)',
            'commbank_response_acq_code'=>'char(64)',
            'commbank_response_txn_code'=>'char(64)',
            'commbank_response_csc_result_code'=>'char(64)',
            'commbank_response_csc_acq_code'=>'char(128)',
            'commbank_response_residence_country' => ' char(2) DEFAULT NULL',
            'commbankresponse_raw' => ' char DEFAULT NULL'
        );
        return $SQLfields;
    }

    function plgVmConfirmedOrder($cart, $order) {

        if (!($method = $this->getVmPluginMethod($order['details']['BT']->virtuemart_paymentmethod_id))) {
            return null; // Another method was selected, do nothing
        }
        if (!$this->selectedThisElement($method->payment_element)) {
            return false;
        }
        $session = JFactory::getSession();
        $return_context = $session->getId();
        $this->_debug = $method->debug;
        $this->logInfo('plgVmConfirmedOrder order number: ' . $order['details']['BT']->order_number, 'message');

        if (!class_exists('VirtueMartModelOrders'))
            require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php' );
        if (!class_exists('VirtueMartModelCurrency'))
            require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'currency.php');

        //$usr = & JFactory::getUser();
        $new_status = '';

        $usrBT = $order['details']['BT'];
        $address = ((isset($order['details']['ST'])) ? $order['details']['ST'] : $order['details']['BT']);

        $vendorModel = new VirtueMartModelVendor();
        $vendorModel->setId(1);
        $vendor = $vendorModel->getVendor();
        $this->getPaymentCurrency($method);
        $q = 'SELECT `currency_code_3` FROM `#__virtuemart_currencies` WHERE `virtuemart_currency_id`="' . $method->payment_currency . '" ';
        $db = &JFactory::getDBO();
        $db->setQuery($q);
        $currency_code_3 = $db->loadResult();

        $paymentCurrency = CurrencyDisplay::getInstance($method->payment_currency);
        $totalInPaymentCurrency = round($paymentCurrency->convertCurrencyTo($method->payment_currency, $order['details']['BT']->order_total,false), 2);
        $cd = CurrencyDisplay::getInstance($cart->pricesCurrency);

        $merchant_email = $this->_getMerchantEmail($method);
        if (empty($merchant_email)) {
            vmInfo(JText::_('VMPAYMENT_COMMBANK_MERCHANT_EMAIL_NOT_SET'));
            return false;
        }

        $testReq = $method->debug == 1 ? 'YES' : 'NO';
        $post_variables = Array(
            'cmd' => '_ext-enter',
            'redirect_cmd' => '_xclick',
            'upload' => '1',
            'business' => $merchant_email, //Email address or account ID of the payment recipient (i.e., the merchant).
            'receiver_email' => $merchant_email, //Primary email address of the payment recipient (i.e., the merchant
            'order_number' => $order['details']['BT']->order_number,
            "invoice" => $order['details']['BT']->order_number,
            'custom' => $return_context,
            'item_name' => JText::_('VMPAYMENT_COMMBANK_ORDER_NUMBER') . ': ' . $order['details']['BT']->order_number,
            "amount" => $totalInPaymentCurrency,
            "currency_code" => $currency_code_3,
            "first_name" => $address->first_name,
            "last_name" => $address->last_name,
            "address1" => $address->address_1,
            "address2" => isset($address->address_2) ? $address->address_2 : '',
            "zip" => $address->zip,
            "city" => $address->city,
            "state" => isset($address->virtuemart_state_id) ? ShopFunctions::getStateByID($address->virtuemart_state_id) : '',
            "country" => ShopFunctions::getCountryByID($address->virtuemart_country_id, 'country_3_code'),
            "email" => $address->email,
            "night_phone_b" => $address->phone_1,
            "return" => JROUTE::_(JURI::root() . 'index.php?option=com_virtuemart&view=pluginresponse&task=pluginresponsereceived&pm=' . $order['details']['BT']->virtuemart_paymentmethod_id),
            "notify_url" => JROUTE::_(JURI::root() . 'index.php?option=com_virtuemart&view=pluginresponse&task=pluginnotification&tmpl=component'),
            "cancel_return" => JROUTE::_(JURI::root() . 'index.php?option=com_virtuemart&view=pluginresponse&task=pluginUserPaymentCancel&on=' . $order['details']['BT']->order_number . '&pm=' . $order['details']['BT']->virtuemart_paymentmethod_id),
            "ipn_test" => $method->debug,
            "no_note" => "1");

            // Prepare data that should be stored in the database
            $dbValues['order_number'] = $order['details']['BT']->order_number;
            $dbValues['payment_name'] = $this->renderPluginName($method, $order);
            $dbValues['virtuemart_paymentmethod_id'] = $cart->virtuemart_paymentmethod_id;
            $dbValues['commbank_custom'] = $return_context;
            $dbValues['cost_per_transaction'] = $method->cost_per_transaction;
            $dbValues['cost_percent_total'] = $method->cost_percent_total;
            $dbValues['payment_currency'] = $method->payment_currency;
            $dbValues['payment_order_total'] = $totalInPaymentCurrency;
            $dbValues['tax_id'] = $method->tax_id;
            $this->storePSPluginInternalData($dbValues);
        // add spin image
        $url= 'index.php?option=com_virtuemart&view=cart&task=complete_payment';
        $html = '<form action="' . $url . '" method="post" name="vm_commbank_form" >';
        foreach ($post_variables as $name => $value) {
            $html.= '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value) . '" />';
        }
            $html.= '<input type="hidden" name="order_no" value="' . $dbValues['order_number'] . '" />';
            $html.= '<fieldset>
                <table class="adminform user-details" width="100%" >
                    <thead><tr><td colspan="2"><div class="card-head">Card Information (For Secure SSL Commonwealth Bank Transaction)</div></td></tr></thead>
                    <tbody>
                        <tr><td width="20%">Card No</td><td><input name="vpc_card_no" class="inputbox required" maxlength="50" size="77" /></td></tr>
                        <tr><td colspan="2">&nbsp;</td></tr>
                        <tr><td>Card Type</td><td>
                            <select name="card_type" class="required">
                                     <option value="visa">VISA</option>
                                     <option value="master">Master Card</option>
                            </select>

                            <!--<input name="vpc_card_expiry_date" maxlength="50" size="50" />-->
                        </td></tr>
                        <tr><td colspan="2">&nbsp;</td></tr>
                        <tr>
                            <td>Card Exipry</td>
                            <td>

                            <select name="vpc_card_expiry_year" class="required"><option value="">Year</option>'?>
                            <?php
                                $current_year=date("Y");
                                for($i=0;$i<10;$i++){
                                    $year=$current_year+$i;
                                    $html.='<option value="'.substr($year,-2).'" >'.$year.'</option>';
                                }?>
                            <?php $html.='</select>
                                                    <select name="vpc_card_expiry_month" class="required">
                                                         <option value="">Month</option>
                                                         <option value="01">January</option>
                                                         <option value="02">February</option>
                                                         <option value="03">March</option>
                                                         <option value="04">April</option>
                                                         <option value="05">May</option>
                                                         <option value="06">June</option>
                                                         <option value="07">July</option>
                                                         <option value="08">August</option>
                                                         <option value="09">September</option>
                                                         <option value="10">October</option>
                                                         <option value="11">November</option>
                                                         <option value="12">December</option>

                                                    </select> (YYMM)
                            </td>
                        </tr>
                        <tr><td colspan="2">&nbsp;</td></tr>
                        <tr><td>Card Security Code</td><td> <input name="vpc_csc" maxlength="50" size="77" /></td></tr>
                        <tr><td colspan="2">&nbsp;</td></tr>
                        <tr><td>Name on Card</td><td> <input name="card_name" maxlength="50" size="77" /></td></tr>
                        <tr><td colspan="2"><input type="submit" name="submit" value="submit" class="button" /></td></tr>
                    <tbody>
                </table>
            </fieldset>';
        $html.= '</form>';


        return $this->processConfirmedOrderPaymentResponse(2, $cart, $order, $html, $new_status);
    }

    function plgVmgetPaymentCurrency($virtuemart_paymentmethod_id, &$paymentCurrencyId) {
        if (!($method = $this->getVmPluginMethod($virtuemart_paymentmethod_id))) {
            return null; // Another method was selected, do nothing
        }
        if (!$this->selectedThisElement($method->payment_element)) {
            return false;
        }
        $this->getPaymentCurrency($method);
        $paymentCurrencyId = $method->payment_currency;
    }

    function plgVmOnPaymentResponseReceived(  &$html) {

        // the payment itself should send the parameter needed.
        $virtuemart_paymentmethod_id = JRequest::getInt('pm', 0);

        $vendorId = 0;
        if (!($method = $this->getVmPluginMethod($virtuemart_paymentmethod_id))) {
            return null; // Another method was selected, do nothing
        }
        if (!$this->selectedThisElement($method->payment_element)) {
            return false;
        }

        $payment_data = JRequest::get('post');
        vmdebug('plgVmOnPaymentResponseReceived', $payment_data);
        $order_number = $payment_data['invoice'];
        $return_context = $payment_data['custom'];
        if (!class_exists('VirtueMartModelOrders'))
            require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php' );

        $virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber($order_number);
        $payment_name = $this->renderPluginName($method);
        $html = $this->_getPaymentResponseHtml($payment_data, $payment_name);

            if ($virtuemart_order_id) {
                if (!class_exists('VirtueMartCart'))
                    require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
                // get the correct cart / session
                $cart = VirtueMartCart::getCart();

                // send the email ONLY if payment has been accepted
                if (!class_exists('VirtueMartModelOrders'))
                    require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php' );
                $order = new VirtueMartModelOrders();
                $orderitems = $order->getOrder($virtuemart_order_id);
                $cart->sentOrderConfirmedEmail($orderitems);
                //We delete the old stuff
                $cart->emptyCart();
            }

        return true;
    }

    function plgVmOnUserPaymentCancel() {

	if (!class_exists('VirtueMartModelOrders'))
	    require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php' );

	$order_number = JRequest::getVar('on');
	if (!$order_number)
	    return false;
	$db = JFactory::getDBO();
	$query = 'SELECT ' . $this->_tablename . '.`virtuemart_order_id` FROM ' . $this->_tablename. " WHERE  `order_number`= '" . $order_number . "'";

	$db->setQuery($query);
	$virtuemart_order_id = $db->loadResult();

	if (!$virtuemart_order_id) {
	    return null;
	}
	$this->handlePaymentUserCancel($virtuemart_order_id);
	//JRequest::setVar('paymentResponse', $returnValue);
	return true;
    }

    /*
     *   plgVmOnPaymentNotification() - This event is fired by Offline Payment. It can be used to validate the payment data as entered by the user.
     * Return:
     * Parameters:
     *  None
     *  @author Valerie Isaksen
     */

    function plgVmOnPaymentNotification() {

	if (!class_exists('VirtueMartModelOrders'))
	    require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php' );
	$commbank_data = JRequest::get('post');
	//$this->_debug = true;
	$order_number = $commbank_data['invoice'];
	$virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber($commbank_data['invoice']);
	$this->logInfo('plgVmOnPaymentNotification: virtuemart_order_id  found ' . $virtuemart_order_id, 'message');

	if (!$virtuemart_order_id) {
	    $this->_debug = true; // force debug here
	    $this->logInfo('plgVmOnPaymentNotification: virtuemart_order_id not found ', 'ERROR');
	    // send an email to admin, and ofc not update the order status: exit  is fine
	    $this->sendEmailToVendorAndAdmins(JText::_('VMPAYMENT_COMMBANK_ERROR_EMAIL_SUBJECT'), JText::_('VMPAYMENT_COMMBANK_UNKNOW_ORDER_ID'));
	    exit;
	}
	$vendorId = 0;
	$payment = $this->getDataByOrderId($virtuemart_order_id);

	$method = $this->getVmPluginMethod($payment->virtuemart_paymentmethod_id);
	if (!$this->selectedThisElement($method->payment_element)) {
	    return false;
	}

	$this->_debug = $method->debug;
	if (!$payment) {
	    $this->logInfo('getDataByOrderId payment not found: exit ', 'ERROR');
	    return null;
	}
	$this->logInfo('commbank_data ' . implode('   ', $commbank_data), 'message');

	// get all know columns of the table
	$db = JFactory::getDBO();
	$query = 'SHOW COLUMNS FROM `' . $this->_tablename . '` ';
	$db->setQuery($query);
	$columns = $db->loadResultArray(0);
	$post_msg = '';
	foreach ($commbank_data as $key => $value) {
	    $post_msg .= $key . "=" . $value . "<br />";
	    $table_key = 'commbank_response_' . $key;
	    if (in_array($table_key, $columns)) {
		$response_fields[$table_key] = $value;
	    }
	}
	$response_fields['payment_name'] = $this->renderPluginName($method);
	$response_fields['commbankresponse_raw'] = $post_msg;
	$return_context = $commbank_data['custom'];
	$response_fields['order_number'] = $order_number;
	$response_fields['virtuemart_order_id'] = $virtuemart_order_id;

	$this->storePSPluginInternalData($response_fields);

	$error_msg = $this->_processIPN($commbank_data, $method);
	$this->logInfo('process IPN ' . $error_msg, 'message');


    if (!(empty($error_msg) )) {
	    $new_status = $method->status_canceled;
	    $this->logInfo('process IPN ' . $error_msg . ' ' . $new_status, 'ERROR');
	} else {
	    $this->logInfo('process IPN OK, status', 'message');

	    if (empty($commbank_data['payment_status']) || ($commbank_data['payment_status'] != 'Completed' && $commbank_data['payment_status'] != 'Pending')) {
		//return false;
	    }
	    $commbank_status = $commbank_data['payment_status'];
	    if (strcmp($commbank_status, 'Completed') == 0) {
		$new_status = $method->status_success;
	    }
	}

	$this->logInfo('plgVmOnPaymentNotification return new_status:' . $new_status, 'message');


	if ($virtuemart_order_id) {
	    // send the email only if payment has been accepted
	    if (!class_exists('VirtueMartModelOrders'))
		require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php' );
	    $modelOrder = new VirtueMartModelOrders();
	    $order['order_status'] = $new_status;
	    $order['virtuemart_order_id'] = $virtuemart_order_id;
	    $order['customer_notified'] = 1;
	    $order['comments'] = JTExt::sprintf('VMPAYMENT_COMMBANK_PAYMENT_CONFIRMED', $order_number);
	    $modelOrder->updateStatusForOneOrder($virtuemart_order_id, $order, true);
	    // remove vmcart
	}
	$this->emptyCart($return_context);
	return true;
    }

    /**
     * Display stored payment data for an order
     * @see components/com_virtuemart/helpers/vmPSPlugin::plgVmOnShowOrderBEPayment()
     */
    function plgVmOnShowOrderBEPayment($virtuemart_order_id, $payment_method_id) {

	if (!$this->selectedThisByMethodId($payment_method_id)) {
	    return null; // Another method was selected, do nothing
	}

	$db = JFactory::getDBO();
	$q = 'SELECT * FROM `' . $this->_tablename . '` '
		. 'WHERE `virtuemart_order_id` = ' . $virtuemart_order_id;
	$db->setQuery($q);
	if (!($paymentTable = $db->loadObject())) {
	   // JError::raiseWarning(500, $db->getErrorMsg());
	    return '';
	}
	$this->getPaymentCurrency($paymentTable);
	$q = 'SELECT `currency_code_3` FROM `#__virtuemart_currencies` WHERE `virtuemart_currency_id`="' . $paymentTable->payment_currency . '" ';
	$db = &JFactory::getDBO();
	$db->setQuery($q);
	$currency_code_3 = $db->loadResult();
	$html = '<table class="adminlist">' . "\n";
	$html .=$this->getHtmlHeaderBE();
	$html .= $this->getHtmlRowBE('COMMBANK_PAYMENT_NAME', $paymentTable->payment_name);
	//$html .= $this->getHtmlRowBE('COMMBANK_PAYMENT_TOTAL_CURRENCY', $paymentTable->payment_order_total.' '.$currency_code_3);
	$code = "commbank_response_";
	foreach ($paymentTable as $key => $value) {
	    if (substr($key, 0, strlen($code)) == $code) {
		$html .= $this->getHtmlRowBE($key, $value);
	    }
	}
	$html .= '</table>' . "\n";
	return $html;
    }

    /**
     * Get ipn data, send verification to PayPal, run corresponding handler
     *
     * @param array $data
     * @return string Empty string if data is valid and an error message otherwise
     * @access protected
     */
    function _processIPN($commbank_data, $method) {
        $secure_post = $method->secure_post;
        $commbank_url = $this->_getURL($method);
        // read the post from PayPal system and add 'cmd'
        $post_msg = 'cmd=_notify-validate';
        foreach ($commbank_data as $key => $value) {
            if ($key != 'view' && $key != 'layout') {
            $value = urlencode($value);
            $post_msg .= "&$key=$value";
            }
        }

        $this->checkPaypalIps($commbank_data['ipn_test']);

        // post back to PayPal system to validate
        $header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($post_msg) . "\r\n\r\n";

        if ($secure_post) {
            // If possible, securely post back to commbank using HTTPS
            // Your PHP server will need to be SSL enabled
            $fps = fsockopen('ssl://' . $commbank_url, 443, $errno, $errstr, 30);
        } else {
            $fps = fsockopen($commbank_url, 80, $errno, $errstr, 30);
        }

        if (!$fps) {
            $this->sendEmailToVendorAndAdmins("error with commbank", JText::sprintf('VMPAYMENT_COMMBANK_ERROR_POSTING_IPN', $errstr, $errno));
            return JText::sprintf('VMPAYMENT_COMMBANK_ERROR_POSTING_IPN', $errstr, $errno); // send email
        } else {
            fputs($fps, $header . $post_msg);
            while (!feof($fps)) {
            $res = fgets($fps, 1024);

            if (strcmp($res, 'VERIFIED') == 0) {
                return '';
            } elseif (strcmp($res, 'INVALID') == 0) {
                $this->sendEmailToVendorAndAdmins("error with commbank IPN NOTIFICATION", JText::_('VMPAYMENT_COMMBANK_ERROR_IPN_VALIDATION') . $res);
                return JText::_('VMPAYMENT_COMMBANK_ERROR_IPN_VALIDATION') . $res;
            }
            }
        }

        fclose($fps);
        return '';
    }

    function _getMerchantEmail($method) {
	    return $method->sandbox ? $method->sandbox_merchant_email : $method->commbank_merchant_email;
    }

    function _getUrl($method) {
        $url = JURI::base();
        //$url = $method->sandbox ? 'www.sandbox.commbank.com' : 'www.commbank.com';
        return $url;
    }

    function _getUrlHttps($method) {
        $url = $this->_getUrl($method);
        //$url = $url . '/cgi-bin/webscr';

        return $url;
    }

    /*
     * CheckPaypalIPs
     * Cannot be checked with Sandbox
     * From VM1.1
     */

    function checkPaypalIps($test_ipn) {
        return;
        // Get the list of IP addresses for www.commbank.com and notify.commbank.com
        $commbank_iplist = array();
        $commbank_iplist = gethostbynamel('www.commbank.com');
        $commbank_iplist2 = array();
        $commbank_iplist2 = gethostbynamel('notify.commbank.com');
        $commbank_iplist3 = array();
        $commbank_iplist3 = array('216.113.188.202', '216.113.188.203', '216.113.188.204', '66.211.170.66');
        $commbank_iplist = array_merge($commbank_iplist, $commbank_iplist2, $commbank_iplist3);

        $commbank_sandbox_hostname = 'ipn.sandbox.commbank.com';
        $remote_hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);

        $valid_ip = false;

        if ($commbank_sandbox_hostname == $remote_hostname) {
            $valid_ip = true;
            $hostname = 'www.sandbox.commbank.com';
        } else {
            $ips = "";
            // Loop through all allowed IPs and test if the remote IP connected here
            // is a valid IP address
            if (in_array($_SERVER['REMOTE_ADDR'], $commbank_iplist)) {
            $valid_ip = true;
            }
            $hostname = 'www.commbank.com';
        }

        if (!$valid_ip) {


            $mailsubject = "PayPal IPN Transaction on your site: Possible fraud";
            $mailbody = "Error code 506. Possible fraud. Error with REMOTE IP ADDRESS = " . $_SERVER['REMOTE_ADDR'] . ".
                            The remote address of the script posting to this notify script does not match a valid PayPal ip address\n
                These are the valid IP Addresses: $ips

                The Order ID received was: $invoice";
            $this->sendEmailToVendorAndAdmins($mailsubject, $mailbody);


            exit();
        }

        if (!($hostname == "www.sandbox.commbank.com" && $test_ipn == 1 )) {
            $res = "FAILED";
            $mailsubject = "PayPal Sandbox Transaction";
            $mailbody = "Hello,
            A fatal error occured while processing a commbank transaction.
            ----------------------------------
            Hostname: $hostname
            URI: $uri
            A Paypal transaction was made using the sandbox without your site in Paypal-Debug-Mode";
            //vmMail($mosConfig_mailfrom, $mosConfig_fromname, $debug_email_address, $mailsubject, $mailbody );
            $this->sendEmailToVendorAndAdmins($mailsubject, $mailbody);
        }
    }

    function _getPaymentResponseHtml($commbank_data, $payment_name) {
        vmdebug('commbank response', $commbank_data);

        $html = '<table>' . "\n";
        $html .= $this->getHtmlRow('COMMBANK_PAYMENT_NAME', $payment_name);
        $html .= $this->getHtmlRow('COMMBANK_ORDER_NUMBER', $commbank_data['invoice']);
        $html .= $this->getHtmlRow('COMMBANK_AMOUNT', $commbank_data['mc_gross'] . " " . $commbank_data['mc_currency']);

        $html .= '</table>' . "\n";

        return $html;
    }

    function getCosts(VirtueMartCart $cart, $method, $cart_prices) {
        if (preg_match('/%$/', $method->cost_percent_total)) {
            $cost_percent_total = substr($method->cost_percent_total, 0, -1);
        } else {
            $cost_percent_total = $method->cost_percent_total;
        }
        return ($method->cost_per_transaction + ($cart_prices['salesPrice'] * $cost_percent_total * 0.01));
    }

    /**
     * Check if the payment conditions are fulfilled for this payment method
     * @author: Valerie Isaksen
     *
     * @param $cart_prices: cart prices
     * @param $payment
     * @return true: if the conditions are fulfilled, false otherwise
     *
     */
    protected function checkConditions($cart, $method, $cart_prices) {


        $address = (($cart->ST == 0) ? $cart->BT : $cart->ST);

        $amount = $cart_prices['salesPrice'];
        $amount_cond = ($amount >= $method->min_amount AND $amount <= $method->max_amount
            OR
            ($method->min_amount <= $amount AND ($method->max_amount == 0) ));

        $countries = array();
        if (!empty($method->countries)) {
            if (!is_array($method->countries)) {
            $countries[0] = $method->countries;
            } else {
            $countries = $method->countries;
            }
        }
        // probably did not gave his BT:ST address
        if (!is_array($address)) {
            $address = array();
            $address['virtuemart_country_id'] = 0;
        }

        if (!isset($address['virtuemart_country_id']))
            $address['virtuemart_country_id'] = 0;
        if (in_array($address['virtuemart_country_id'], $countries) || count($countries) == 0) {
            if ($amount_cond) {
            return true;
            }
        }

        return false;
    }

    /**
     * We must reimplement this triggers for joomla 1.7
     */

    /**
     * Create the table for this plugin if it does not yet exist.
     * This functions checks if the called plugin is active one.
     * When yes it is calling the standard method to create the tables
     * @author Valérie Isaksen
     *
     */
    function plgVmOnStoreInstallPaymentPluginTable($jplugin_id) {

	    return $this->onStoreInstallPluginTable($jplugin_id);
    }

    /**
     * This event is fired after the payment method has been selected. It can be used to store
     * additional payment info in the cart.
     *
     * @author Max Milbers
     * @author Valérie isaksen
     *
     * @param VirtueMartCart $cart: the actual cart
     * @return null if the payment was not selected, true if the data is valid, error message if the data is not vlaid
     *
     */
    public function plgVmOnSelectCheckPayment(VirtueMartCart $cart) {
	return $this->OnSelectCheck($cart);
    }

    /**
     * plgVmDisplayListFEPayment
     * This event is fired to display the pluginmethods in the cart (edit shipment/payment) for exampel
     *
     * @param object $cart Cart object
     * @param integer $selected ID of the method selected
     * @return boolean True on succes, false on failures, null when this plugin was not selected.
     * On errors, JError::raiseWarning (or JError::raiseError) must be used to set a message.
     *
     * @author Valerie Isaksen
     * @author Max Milbers
     */
    public function plgVmDisplayListFEPayment(VirtueMartCart $cart, $selected = 0, &$htmlIn) {
	return $this->displayListFE($cart, $selected, $htmlIn);
    }

    /*
     * plgVmonSelectedCalculatePricePayment
     * Calculate the price (value, tax_id) of the selected method
     * It is called by the calculator
     * This function does NOT to be reimplemented. If not reimplemented, then the default values from this function are taken.
     * @author Valerie Isaksen
     * @cart: VirtueMartCart the current cart
     * @cart_prices: array the new cart prices
     * @return null if the method was not selected, false if the shiiping rate is not valid any more, true otherwise
     *
     *
     */

    public function plgVmonSelectedCalculatePricePayment(VirtueMartCart $cart, array &$cart_prices, &$cart_prices_name) {
	return $this->onSelectedCalculatePrice($cart, $cart_prices, $cart_prices_name);
    }

    /**
     * plgVmOnCheckAutomaticSelectedPayment
     * Checks how many plugins are available. If only one, the user will not have the choice. Enter edit_xxx page
     * The plugin must check first if it is the correct type
     * @author Valerie Isaksen
     * @param VirtueMartCart cart: the cart object
     * @return null if no plugin was found, 0 if more then one plugin was found,  virtuemart_xxx_id if only one plugin is found
     *
     */
    function plgVmOnCheckAutomaticSelectedPayment(VirtueMartCart $cart, array $cart_prices = array()) {
	return $this->onCheckAutomaticSelected($cart, $cart_prices);
    }

    /**
     * This method is fired when showing the order details in the frontend.
     * It displays the method-specific data.
     *
     * @param integer $order_id The order ID
     * @return mixed Null for methods that aren't active, text (HTML) otherwise
     * @author Max Milbers
     * @author Valerie Isaksen
     */
    public function plgVmOnShowOrderFEPayment($virtuemart_order_id, $virtuemart_paymentmethod_id, &$payment_name) {
	  $this->onShowOrderFE($virtuemart_order_id, $virtuemart_paymentmethod_id, $payment_name);
    }

    /**
     * This event is fired during the checkout process. It can be used to validate the
     * method data as entered by the user.
     *
     * @return boolean True when the data was valid, false otherwise. If the plugin is not activated, it should return null.
     * @author Max Milbers

      public function plgVmOnCheckoutCheckDataPayment($psType, VirtueMartCart $cart) {
      return null;
      }
     */

    /**
     * This method is fired when showing when priting an Order
     * It displays the the payment method-specific data.
     *
     * @param integer $_virtuemart_order_id The order ID
     * @param integer $method_id  method used for this order
     * @return mixed Null when for payment methods that were not selected, text (HTML) otherwise
     * @author Valerie Isaksen
     */
    function plgVmonShowOrderPrintPayment($order_number, $method_id) {
	return $this->onShowOrderPrint($order_number, $method_id);
    }


    function plgVmDeclarePluginParamsPayment($name, $id, &$data) {
	return $this->declarePluginParams('payment', $name, $id, $data);
    }

    function plgVmSetOnTablePluginParamsPayment($name, $id, &$table) {
	return $this->setOnTablePluginParams($name, $id, $table);
    }
//commonwealth bank integration starts
    /*---------------- Disclaimer --------------------------------------------------

    Copyright 2004 Dialect Solutions Holdings.  All rights reserved.

    This document is provided by Dialect Holdings on the basis that you will treat
    it as confidential.

    No part of this document may be reproduced or copied in any form by any means
    without the written permission of Dialect Holdings.  Unless otherwise expressly
    agreed in writing, the information contained in this document is subject to
    change without notice and Dialect Holdings assumes no responsibility for any
    alteration to, or any error or other deficiency, in this document.

    All intellectual property rights in the Document and in all extracts and things
    derived from any part of the Document are owned by Dialect and will be assigned
    to Dialect on their creation. You will protect all the intellectual property
    rights relating to the Document in a manner that is equal to the protection
    you provide your own intellectual property.  You will notify Dialect
    immediately, and in writing where you become aware of a breach of Dialect's
    intellectual property rights in relation to the Document.

    The names "Dialect", "QSI Payments" and all similar words are trademarks of
    Dialect Holdings and you must not use that name or any similar name.

    Dialect may at its sole discretion terminate the rights granted in this
    document with immediate effect by notifying you in writing and you will
    thereupon return (or destroy and certify that destruction to Dialect) all
    copies and extracts of the Document in its possession or control.

    Dialect does not warrant the accuracy or completeness of the Document or its
    content or its usefulness to you or your merchant customers.   To the extent
    permitted by law, all conditions and warranties implied by law (whether as to
    fitness for any particular purpose or otherwise) are excluded.  Where the
    exclusion is not effective, Dialect limits its liability to $100 or the
    resupply of the Document (at Dialect's option).

    Data used in examples and sample data files are intended to be fictional and
    any resemblance to real persons or companies is entirely coincidental.

    Dialect does not indemnify you or any third party in relation to the content or
    any use of the content as contemplated in these terms and conditions.

    Mention of any product not owned by Dialect does not constitute an endorsement
    of that product.

    This document is governed by the laws of New South Wales, Australia and is
    intended to be legally binding.

    -------------------------------------------------------------------------------

    This example assumes that a form has been sent to this example with the
    required fields. The example then processes the command and displays the
    receipt or error to a HTML page in the users web browser.

    NOTE:
    =====
    You will have to install the libeay32.dll and ssleay32.dll libraries
    into your x:\WINNT\system32 directory to run this example.


    @author Dialect Payment Solutions Pty Ltd Group


    Version 3.1

    ------------------------------------------------------------------------------*/
    function getResponseDescription($responseCode) {

        switch ($responseCode) {
            case "0" : $result = "Transaction Successful"; break;
            case "?" : $result = "Transaction status is unknown"; break;
            case "1" : $result = "Unknown Error"; break;
            case "2" : $result = "Bank Declined Transaction"; break;
            case "3" : $result = "No Reply from Bank"; break;
            case "4" : $result = "Expired Card"; break;
            case "5" : $result = "Insufficient funds"; break;
            case "6" : $result = "Error Communicating with Bank"; break;
            case "7" : $result = "Payment Server System Error"; break;
            case "8" : $result = "Transaction Type Not Supported"; break;
            case "9" : $result = "Bank declined transaction (Do not contact Bank)"; break;
            case "A" : $result = "Transaction Aborted"; break;
            case "C" : $result = "Transaction Cancelled"; break;
            case "D" : $result = "Deferred transaction has been received and is awaiting processing"; break;
            case "F" : $result = "3D Secure Authentication failed"; break;
            case "I" : $result = "Card Security Code verification failed"; break;
            case "L" : $result = "Shopping Transaction Locked (Please try the transaction again later)"; break;
            case "N" : $result = "Cardholder is not enrolled in Authentication scheme"; break;
            case "P" : $result = "Transaction has been received by the Payment Adaptor and is being processed"; break;
            case "R" : $result = "Transaction was not processed - Reached limit of retry attempts allowed"; break;
            case "S" : $result = "Duplicate SessionID (OrderInfo)"; break;
            case "T" : $result = "Address Verification Failed"; break;
            case "U" : $result = "Card Security Code Failed"; break;
            case "V" : $result = "Address Verification and Card Security Code Failed"; break;
            default  : $result = "Unable to be determined";
        }
        return $result;
    }

//  ----------------------------------------------------------------------------

// This function uses the QSI AVS Result Code retrieved from the Digital
// Receipt and returns an appropriate description for this code.

// @param vAVSResultCode String containing the QSI AVS Result Code
// @return description String containing the appropriate description

    function displayAVSResponse($avsResultCode) {

        if ($avsResultCode != "") {
            switch ($avsResultCode) {
                Case "Unsupported" : $result = "AVS not supported or there was no AVS data provided"; break;
                Case "X"  : $result = "Exact match - address and 9 digit ZIP/postal code"; break;
                Case "Y"  : $result = "Exact match - address and 5 digit ZIP/postal code"; break;
                Case "S"  : $result = "Service not supported or address not verified (international transaction)"; break;
                Case "G"  : $result = "Issuer does not participate in AVS (international transaction)"; break;
                Case "A"  : $result = "Address match only"; break;
                Case "W"  : $result = "9 digit ZIP/postal code matched, Address not Matched"; break;
                Case "Z"  : $result = "5 digit ZIP/postal code matched, Address not Matched"; break;
                Case "R"  : $result = "Issuer system is unavailable"; break;
                Case "U"  : $result = "Address unavailable or not verified"; break;
                Case "E"  : $result = "Address and ZIP/postal code not provided"; break;
                Case "N"  : $result = "Address and ZIP/postal code not matched"; break;
                Case "0"  : $result = "AVS not requested"; break;
                default   : $result = "Unable to be determined";
            }
        } else {
            $result = "null response";
        }
        return $result;
    }

//  ----------------------------------------------------------------------------

// This function uses the QSI CSC Result Code retrieved from the Digital
// Receipt and returns an appropriate description for this code.

// @param vCSCResultCode String containing the QSI CSC Result Code
// @return description String containing the appropriate description

    function displayCSCResponse($cscResultCode) {

        if ($cscResultCode != "") {
            switch ($cscResultCode) {
                Case "Unsupported" : $result = "CSC not supported or there was no CSC data provided"; break;
                Case "M"  : $result = "Exact code match"; break;
                Case "S"  : $result = "Merchant has indicated that CSC is not present on the card (MOTO situation)"; break;
                Case "P"  : $result = "Code not processed"; break;
                Case "U"  : $result = "Card issuer is not registered and/or certified"; break;
                Case "N"  : $result = "Code invalid or not matched"; break;
                default   : $result = "Unable to be determined"; break;
            }
        } else {
            $result = "null response";
        }
        return $result;
    }

//  -----------------------------------------------------------------------------

// This method uses the verRes status code retrieved from the Digital
// Receipt and returns an appropriate description for the QSI Response Code

// @param statusResponse String containing the 3DS Authentication Status Code
// @return String containing the appropriate description

    function getStatusDescription($statusResponse) {
        if ($statusResponse == "" || $statusResponse == "No Value Returned") {
            $result = "3DS not supported or there was no 3DS data provided";
        } else {
            switch ($statusResponse) {
                Case "Y"  : $result = "The cardholder was successfully authenticated."; break;
                Case "E"  : $result = "The cardholder is not enrolled."; break;
                Case "N"  : $result = "The cardholder was not verified."; break;
                Case "U"  : $result = "The cardholder's Issuer was unable to authenticate due to some system error at the Issuer."; break;
                Case "F"  : $result = "There was an error in the format of the request from the merchant."; break;
                Case "A"  : $result = "Authentication of your Merchant ID and Password to the ACS Directory Failed."; break;
                Case "D"  : $result = "Error communicating with the Directory Server."; break;
                Case "C"  : $result = "The card type is not supported for authentication."; break;
                Case "S"  : $result = "The signature on the response received from the Issuer could not be validated."; break;
                Case "P"  : $result = "Error parsing input from Issuer."; break;
                Case "I"  : $result = "Internal Payment Server system error."; break;
                default   : $result = "Unable to be determined"; break;
            }
        }
        return $result;
    }

//  -----------------------------------------------------------------------------

// This subroutine takes a data String and returns a predefined value if empty
// If data Sting is null, returns string "No Value Returned", else returns input

// @param $in String containing the data String

// @return String containing the output String


//  ----------------------------------------------------------------------------
    function null2unknown($map, $key) {
        if (array_key_exists($key, $map)) {
            if (!is_null($map[$key])) {
                return $map[$key];
            }
        }
        return "No Value Returned";
    }

// *********************
// START OF MAIN PROGRAM
// *********************

// add the start of the vpcURL querystring parameters
function initializeParams(){
    $vpcURL = $_POST["virtualPaymentClientURL"];
    // This is the title for display
    $title  = $_POST["Title"];

    // Remove the Virtual Payment Client URL from the parameter hash as we
    // do not want to send these fields to the Virtual Payment Client.
    unset($_POST["virtualPaymentClientURL"]);
    unset($_POST["SubButL"]);
    unset($_POST["Title"]);

    // create a variable to hold the POST data information and capture it
    $postData = "";

    $ampersand = "";
    foreach($_POST as $key => $value) {
        // create the POST data input leaving out any fields that have no value
    if (strlen($value) > 0) {
    $postData .= $ampersand . urlencode($key) . '=' . urlencode($value);
    $ampersand = "&";
    }
    }

    // Get a HTTPS connection to VPC Gateway and do transaction
    // turn on output buffering to stop response going to browser
    ob_start();

    // initialise Client URL object
    $ch = curl_init();

    // set the URL of the VPC
    curl_setopt ($ch, CURLOPT_URL, $vpcURL);
    curl_setopt ($ch, CURLOPT_POST, 1);
    curl_setopt ($ch, CURLOPT_POSTFIELDS, $postData);

    // (optional) set the proxy IP address and port
    // curl_setopt ($ch, CURLOPT_PROXY, "YOUR_PROXY:PORT");

    // (optional) certificate validation
    // trusted certificate file
    //curl_setopt($ch, CURLOPT_CAINFO, "c:/temp/ca-bundle.crt");

    //turn on/off cert validation
    // 0 = don't verify peer, 1 = do verify
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);

    // 0 = don't verify hostname, 1 = check for existence of hostame, 2 = verify
    //curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 2);

    // connect
    curl_exec ($ch);

    // get response
    $response = ob_get_contents();

    // turn output buffering off.
    ob_end_clean();

    // set up message paramter for error outputs
    $message = "";

    // serach if $response contains html error code
    if(strchr($response,"<html>") || strchr($response,"<html>")) {;
        $message = $response;
    } else {
        // check for errors from curl
        if (curl_error($ch))
            $message = "curl_errno=". curl_errno($ch) . "<br/>" . curl_error($ch);
    }

    // close client URL
    curl_close ($ch);

    // Extract the available receipt fields from the VPC Response
    // If not present then let the value be equal to 'No Value Returned'
    $map = array();

    // process response if no errors
    if (strlen($message) == 0) {
        $pairArray = split("&", $response);
        foreach ($pairArray as $pair) {
            $param = split("=", $pair);
            $map[urldecode($param[0])] = urldecode($param[1]);
        }
        $message         = null2unknown($map, "vpc_Message");
    }

    // Standard Receipt Data
    # merchTxnRef not always returned in response if no receipt so get input
    $merchTxnRef     = $vpc_MerchTxnRef;

    $amount          = null2unknown($map, "vpc_Amount");
    $locale          = null2unknown($map, "vpc_Locale");
    $batchNo         = null2unknown($map, "vpc_BatchNo");
    $command         = null2unknown($map, "vpc_Command");
    $version         = null2unknown($map, "vpc_Version");
    $cardType        = null2unknown($map, "vpc_Card");
    $orderInfo       = null2unknown($map, "vpc_OrderInfo");
    $receiptNo       = null2unknown($map, "vpc_ReceiptNo");
    $merchantID      = null2unknown($map, "vpc_Merchant");
    $authorizeID     = null2unknown($map, "vpc_AuthorizeId");
    $transactionNr   = null2unknown($map, "vpc_TransactionNo");
    $acqResponseCode = null2unknown($map, "vpc_AcqResponseCode");
    $txnResponseCode = null2unknown($map, "vpc_TxnResponseCode");

    // CSC Receipt Data
    $cscResultCode   = null2unknown($map, "vpc_CSCResultCode");
    $cscACQRespCode  = null2unknown($map, "vpc_AcqCSCRespCode");

    // AVS Receipt Data
    $avsResultCode   = null2unknown($map, "vpc_AVSResultCode");
    $vACQAVSRespCode = null2unknown($map, "vpc_AcqAVSRespCode");
    $avs_City        = null2unknown($map, "vpc_AVS_City");
    $avs_Country     = null2unknown($map, "vpc_AVS_Country");
    $avs_Street01    = null2unknown($map, "vpc_AVS_Street01");
    $avs_PostCode    = null2unknown($map, "vpc_AVS_PostCode");
    $avs_StateProv   = null2unknown($map, "vpc_AVS_StateProv");
    $avsRequestCode  = null2unknown($map, "vpc_AVSRequestCode");

    /*********************
     * END OF MAIN PROGRAM
     *********************/

    // FINISH TRANSACTION - Process the VPC Response Data
    // =====================================================
    // For the purposes of demonstration, we simply display the Result fields on a
    // web page.

    // Show 'Error' in title if an error condition
    $errorTxt = "";
    // Show the display page as an error page
    if ($txnResponseCode == "7" || $txnResponseCode != "No Value Returned") {
        $errorTxt = "Error ";
    }

    // *******************
    // END OF MAIN PROGRAM
    // *******************

    // FINISH TRANSACTION - Process the VPC Response Data
    // =====================================================
    // For the purposes of demonstration, we simply display the Result fields on a
    // web page.

    // Show 'Error' in title if an error condition
    $errorTxt = "";

    // Show this page as an error page if vpc_TxnResponseCode equals '7'
    if ($txnResponseCode == "7" || $txnResponseCode == "No Value Returned") {
        $errorTxt = "Error ";
    }
}
//commonwealth bank integration ends


}

// No closing tag
