<?php
include_once "CurlExecutor.php";

define("BASE_URL", "https://api.payway.com.au/rest/v1");
define("MERCHANT_ID", "TEST");
define("PUBLIC_KEY", "T11915_PUB_gjjg685bbrqkvwzb2jzw8ye4wu8xvfigkzfqmqrshzy28y67fp765pqjangi");
define("PRIVATE_KEY", "T11915_SEC_7vgpwqp89ggw5xvveuanmyz3hmyim4ixm9689x984etnaxbarhybyu2yccaz");


$token = getSingsleUseTokenID();              //get single use token
createPayment($token);

function createPayment($token) {
    $headers[] = "Authorization: Basic " . base64_encode(PRIVATE_KEY . ":");
    $headers[] = "Content-Type: application/x-www-form-urlencoded";

    $post_data = array(
        "singleUseTokenId" => $token,
        "customerNumber" => "120",
        "transactionType" => "payment",
        "principalAmount" => "20.00",
        "currency" => "aud",
        "orderNumber" => "Order-13",
        "merchantId" => MERCHANT_ID
    );

    $result = CurlExecutor::execute(BASE_URL . "/transactions", "POST", $post_data, null, $headers);
    $result["response"] = json_decode($result["response"]);

    CurlExecutor::prettyPrint($result);
}

function getSingsleUseTokenID() {
    $headers[] = "Authorization: Basic " . base64_encode(PUBLIC_KEY . ":");
    $headers[] = "Content-Type: application/x-www-form-urlencoded";

    $post_data = array(
        "paymentMethod" => "creditCard",
        "cardNumber" => "5163200000000008",
        "cardholderName" => "My Mastercard",
        "cvn" => "070",
        "expiryDateMonth" => "08",
        "expiryDateYear" => "20",
    );

    $result = CurlExecutor::execute(BASE_URL . "/single-use-tokens", "POST", $post_data, null, $headers);
    $result["response"] = json_decode($result["response"]);
    if ($result["code"] == 200) {
        return $result["response"]->singleUseTokenId;
    }
    CurlExecutor::prettyPrint($result);
    die("Error");
}
?>