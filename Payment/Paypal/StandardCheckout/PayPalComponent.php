<?php

namespace App\Payment\Paypal\StandardCheckout;

use App\Logger\Log;
use GuzzleHttp\Client;

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

    public function getPayPalClientId() : string {
        $transaction_mode = self::getPayPalTransactionMode();
        return getenv('PAYPAL_CLIENT_ID_'. $transaction_mode);
    }

    public function getPayPalAuthorizationCode() : string {
        $transaction_mode = $this->getPayPalTransactionMode();
        $paypal_secret_key =  getenv('PAYPAL_SECRET_KEY_'. $transaction_mode);
        $auth_code = $this->getPayPalClientId() . ':' . $paypal_secret_key;
        return base64_encode($auth_code);
    }

    public function sellerMerchantId() {
        $transaction_mode = self::getPayPalTransactionMode();
        return getenv('PAYPAL_SELLER_PAYER_ID_'. $transaction_mode);
    }

    public function partnerBNCode() {
        $transaction_mode = self::getPayPalTransactionMode();
        return getenv('PAYPAL_PARTNER_BN_CODE_'. $transaction_mode);
    }

    public function getPayPalBasicHeader($authorizationCode): array {
        return [
            'Content-Type' => 'application/json',
            'authorization' => 'Basic '. $authorizationCode,
        ];
    }

    public function getPayPalHeader($access_token): array {
        $header_options = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'authorization' => 'Bearer '. $access_token
        ];
        $bn_code = $this->partnerBNCode();
        if (!empty($bn_code)) {
            $header_options['PayPal-Partner-Attribution-Id'] = $bn_code;
        }
        return $header_options;
    }

    public function generatePapPalAccessToken() {
        $access_token = '';
        try {
            $request_data = [];
            $request_data['grant_type'] = 'client_credentials';
            $url = $this->getPayPalBaseUrl() . '/v1/oauth2/token';
            $headers = $this->getPayPalBasicHeader($this->getPayPalAuthorizationCode());

            $client = new \GuzzleHttp\Client();
            $options = [
                'headers'=> $headers,
                // 'body' => $request_data
                'body' => 'grant_type=client_credentials'
            ];
            $response = $client->post($url, $options);
            $response_data = $response->getBody()->getContents();
            $response_data = json_decode($response_data, true);
            if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
                return $response_data['access_token'] ?? '';
            }
            $exception = $response_data['error_description'] ?? 'Unable to creat access token';
            throw new \Exception($exception);
        }
        catch (\Exception $exception) {
            $error_message = $exception->getMessage();
            Log::write('Error in payPal token request:: '. $error_message, 'paypal');
        }
        return $access_token;
    }


    public function getSellerOnboardStatus($partner_merchant_id, $seller_merchant_id): array {
        $return_response = [
            'status' => 0,
            'message' => '',
            'data' => ''
        ];
        $access_token = $this->generatePapPalAccessToken();
        try {
            $request_data = [];
            $url = $this->getPayPalBaseUrl() . '/v1/customer/partners/'. $partner_merchant_id . '/merchant-integrations/' . $seller_merchant_id;
            $headers = $this->getPayPalHeader($access_token);
            $options = [
                'headers'=> $headers
            ];

            $client = new \GuzzleHttp\Client();
            $response = $client->get($url, $options);
            $response_data = $response->getBody()->getContents();
            $response_data = json_decode($response_data, true);

            Log::write('Onboard status response: '. json_encode($response_data, JSON_UNESCAPED_SLASHES), 'pay_pal_connect', 'pay_pal');

            if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
                $return_response = [
                    'status' => 1,
                    'message' => '',
                    'data' => $response_data
                ];
            }
        }
        catch (\Exception $exception) {
            Log::write('Error in pay pal onboard status: ' . $exception->getMessage(),  'pay_pal_connect_error', 'pay_pal');
        }
        return $return_response;
    }


    /**
     * @throws \Exception
     */
    public function executePaypalOrder() {
        try {
            $payment_amount = "5.00";
            $currency = 'AUD';

            $items = [
                [
                    "name"=> "AAA",
                    "quantity"=> "1",
                    "unit_amount"=> [
                        "currency_code"=> $currency,
                        "value"=> $payment_amount
                    ]
                ]
            ];

            $shipping_address =  [
                "address_line_1" => "9 Yarra",
                "address_line_2" => "Address line 2",
                "admin_area_2" => "Melbourne",
                "admin_area_1" => "Victoria",
                "postal_code" => "3100",
                "country_code" => "AU"
            ];

            $shipping = [
                'type' => 'SHIPPING', //SHIPPING, PICKUP_IN_PERSON
                'name' => [
                    'full_name' => 'Iman Ali'
                ],
                'address' => $shipping_address
            ];
            $shipping_amount = 0;
            $service_charge_amount = 0;
            $discount = 0;

            $order_data = [];
            $order_data['intent'] = 'CAPTURE';
            $order_data['purchase_units'] = [
                [
                    "reference_id" => "merchant-ref-".uniqid(),
                    'items' => $items,
                    'amount' => [
                        'currency_code' => 'AUD',
                        'value' => $payment_amount,
                        'breakdown' => [
                            'item_total' => [
                                'currency_code' => 'AUD',
                                'value' => $payment_amount,
                            ],
                            'shipping' => [
                                'currency_code' => $currency,
                                'value' => $shipping_amount,
                            ],
                            'handling' => [
                                'currency_code' => $currency,
                                'value' => $service_charge_amount,
                            ],
                            "discount" => [
                                "currency_code" => $currency,
                                "value"=> $discount
                            ]
                        ]
                    ],
                    'shipping' => $shipping,
                ]
            ];

            Log::write($order_data, 'paypal');

            $order_data = json_encode($order_data);

            $access_token = self::generatePapPalAccessToken();
            if (empty($access_token)) {
                throw new \Exception('Unable to create access token');
            }

            $url = self::getPayPalBaseUrl() . '/v2/checkout/orders';
            Log::write('executePaypalOrder request data: '. $order_data, 'paypal');
            $client = new \GuzzleHttp\Client();
            $options = [
                'headers'=> self::getPayPalHeader($access_token),
                'body' => $order_data
            ];

            $response = $client->post($url, $options);

            if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
                $response_data = $response->getBody()->getContents();
                Log::write('executePaypalOrder response data: '. $order_data, 'paypal');
                $response_data = json_decode($response_data, true);
                return $response_data;
            }
            // $this->controller->saveLog(PaymentMethod::PAY_PAL, 'paypal_error', 'order_response: '. $response->getStringBody());
            $exception = $response_data['error_description'] ?? 'Unable to create PayPal Order';
            throw new \Exception($exception);
        }
        catch (\Exception $exception) {
            $exception_message = $exception->getMessage();
            Log::write('Error in createPaypalOrder: '. $exception_message, 'paypal');
        }
    }

    public function executePayPalCapture($paypalOrderId) {
        try {
            $access_token = $this->generatePapPalAccessToken();
            if (empty($access_token)) {
                throw new \Exception('Unable to create access token');
            }

            $url = $this->getPayPalBaseUrl() . '/v2/checkout/orders/' . $paypalOrderId . '/capture';
            Log::write('captured_payment_response: '. $url, 'paypal');
            $client = new \GuzzleHttp\Client();

            $options = [
                'headers'=> $this->getPayPalHeader($access_token),
                'body' => ''
            ];

            $response = $client->post($url, $options);

            $response_data = $response->getBody()->getContents();
            $response_data = json_decode($response_data, true);
            Log::write('captured_payment_response: '. json_encode($response_data), 'paypal');
            if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
                return $response_data;
            }
            $exception = $response_data['error_description'] ?? 'Unable to capture payment';
            throw new \Exception($exception);
        }
        catch (\Exception $exception) {
            Log::write('Error in executePayPalCapture: '. $exception->getMessage(), 'paypal');
        }
    }
}
