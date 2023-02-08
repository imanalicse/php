<?php

namespace App\Payment\Paypal\StandardCheckout;

require '../../../global_config.php';

class PayPalComponent
{
    public function getPayPalTransactionMode(): string {
        return 'TEST';
    }

    public function getPayPalBaseUrl() : string {
        $transaction_mode = $this->getPayPalTransactionMode();
        $paypal_base_url = 'https://api-m.paypal.com';
        if ($transaction_mode === 'TEST') {
            $paypal_base_url = 'https://api-m.sandbox.paypal.com';
        }
        return $paypal_base_url;
    }

    public static function getPayPalClientId() {
        $transaction_mode = self::getPayPalTransactionMode();
        return getenv('PAYPAL_CLIENT_ID_'. $transaction_mode);
    }

    public function getPayPalAuthorizationCode() : string {
        $transaction_mode = $this->getPayPalTransactionMode();
        $paypal_secret_key =  getenv('PAYPAL_SECRET_KEY_'. $transaction_mode);
        $auth_code = $this->getPayPalClientId() . ':' . $paypal_secret_key;
        return base64_encode($auth_code);
    }
    public function getPayPalBasicHeader($authorizationCode): array {
        return [
            'Content-Type' => 'application/json',
            'authorization' => 'Basic '. $authorizationCode,
        ];
    }
    public function getPayPalHeader($access_token): array {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'authorization' => 'Bearer '. $access_token,
        ];
    }

    public function generatePapPalAccessToken() {
        $access_token = '';
        try {
            $request_data = [];
            $request_data['grant_type'] = 'client_credentials';
            $url = $this->getPayPalBaseUrl() . '/v1/oauth2/token';
            $headers = $this->getPayPalBasicHeader($this->getPayPalAuthorizationCode());
            echo "<pre>";
            print_r($headers);
            echo "</pre>";
            die();

            $client = new \GuzzleHttp\Client();
            $options = [
                'headers'=> $headers,
                'body' => $request_data
            ];
            $response = $client->post($url, $options);
            $response_data = $response->getJson();
            echo "<pre>";
            print_r($response_data);
            echo "</pre>";
            if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
                return $response_data['access_token'] ?? '';
            }
            $exception = $response_data['error_description'] ?? 'Unable to creat access token';
            throw new \Exception($exception);
        }
        catch (\Exception $exception) {
            // $this->controller->saveLog(PaymentMethod::PAY_PAL, 'pay_pal_error', 'Error in payPal token request: ' . $exception->getMessage());
        }
        return $access_token;
    }

    /**
     * @throws \Exception
     */
    public function executePaypalOrder() {
        echo "hello";
        die('xx');
        try {
            $payment_amount = "2.00";
            $order_data = [];
            $order_data['intent'] = 'CAPTURE';
            $order_data['purchase_units'] = [
                [
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => $payment_amount,
                    ]
                ]
            ];
            echo "<pre>";
            print_r($order_data);
            echo "</pre>";
            die('xxx');
            $order_data = json_encode($order_data);

            $access_token = self::generatePapPalAccessToken();
            if (empty($access_token)) {
                throw new \Exception('Unable to create access token');
            }

            $url = self::getPayPalBaseUrl() . '/v2/checkout/orders';
            // $http = new Client();
            $client = new \GuzzleHttp\Client();
            $options = [
                'headers'=> self::getPayPalHeader($access_token),
                'body' => $order_data
            ];

            $response = $client->post($url, $options);
//            $response = $http->post($url, $order_data, [
//                'headers' => self::getPayPalHeader($access_token),
//            ]);
            $response_data = $response->getJson();
            echo "<pre>";
            print_r($response_data);
            echo "</pre>";
            if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
                return $response_data;
            }
            // $this->controller->saveLog(PaymentMethod::PAY_PAL, 'paypal_error', 'order_response: '. $response->getStringBody());
            $exception = $response_data['error_description'] ?? 'Unable to create PayPal Order';
            throw new \Exception($exception);
        }
        catch (\Exception $exception) {
            echo "<pre>";
            print_r($exception->getMessage());
            echo "</pre>";
            // $this->controller->saveLog(PaymentMethod::PAY_PAL, 'pay_pal_error', 'Error in createPaypalOrder: '. $exception->getMessage());
        }
    }

    public function executePayPalCapture($paypalOrderId) {
        try {
            $access_token = $this->generatePapPalAccessToken();
            if (empty($access_token)) {
                throw new \Exception('Unable to create access token');
            }

            $url = $this->getPayPalBaseUrl() . '/v2/checkout/orders/' . $paypalOrderId . '/capture';
            $this->controller->saveLog(PaymentMethod::PAY_PAL, 'pay_pal', 'captured_payment_response: '. $url);
            $http = new Client();
            $response = $http->post($url, '', [
                'headers' => $this->getPayPalHeader($access_token),
            ]);
            $response_data = $response->getJson();
            $this->controller->saveLog(PaymentMethod::PAY_PAL, 'pay_pal_error', 'captured_payment_response: '. json_encode($response_data));
            if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
                return $response_data;
            }
            $exception = $response_data['error_description'] ?? 'Unable to capture payment';
            throw new \Exception($exception);
        }
        catch (\Exception $exception) {
            $this->controller->saveLog(PaymentMethod::PAY_PAL, 'pay_pal_error', 'Error in executePayPalCapture: '. $exception->getMessage());
        }
    }
}
