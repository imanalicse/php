<!DOCTYPE html>
<html>
<head>
    <title>Mailchimp</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>

<?php


add_action('wp_ajax_upload_mailchimp_subscriber', 'upload_mailchimp_subscriber');
add_action('wp_ajax_nopriv_upload_mailchimp_subscriber', 'upload_mailchimp_subscriber');
function upload_mailchimp_subscriber()
{
    $email = $_POST['email'];

    //CakeLog::write('debug', $user);
    $result = call('lists/subscribe', array(
        'id' => '3f6098aeb3',
        'email' => array('email' => $email),
        'double_optin' => false,
        'update_existing' => true,
        'replace_interests' => false,
        'send_welcome' => false,
    ));

    if (!empty($result["leid"]))
        wp_send_json($result);
    else if ($result["error"])
        wp_send_json($result);
    exit();

}

function call($method, $args = array())
{
    $api_endpoint = 'https://<dc>.api.mailchimp.com/2.0';
    $api_key = 'bcc534a46d190974ae4d8310b47a763d-us9'; //live

    list(, $datacentre) = explode('-', $api_key);
    $api_endpoint = str_replace('<dc>', $datacentre, $api_endpoint);
    $args['apikey'] = $api_key;
    $url = $api_endpoint . '/' . $method . '.json';

    if (function_exists('curl_init') && function_exists('curl_setopt')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($args));
        $result = curl_exec($ch);
        curl_close($ch);
    } else {
        $json_data = json_encode($args);
        $result = file_get_contents($url, null, stream_context_create(array(
            'http' => array(
                'protocol_version' => 1.1,
                'user_agent' => 'PHP-MCAPI/2.0',
                'method' => 'POST',
                'header' => "Content-type: application/json\r\n" .
                    "Connection: close\r\n" .
                    "Content-length: " . strlen($json_data) . "\r\n",
                'content' => $json_data,
            ),
        )));
    }
    //CakeLog::write('debug', $result);
    return $result ? json_decode($result, true) : false;
}
?>

<form action="upload_mailchimp_subscriber"
      method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form"
      class="validate mailchimp">
    <div id="mc_embed_signup_scroll">
        <div class="mc-field-group newsletter-input">
            <div>
                <input type="email" value="" name="email" class="email" id="email"
                       placeholder="Enter Address *" required="" aria-required="true">
                <span class="newsletter_mail_error"></span>
            </div>
            <div>
                <div style="position: absolute; left: -5000px;" aria-hidden="true"><input
                        type="text" name="b_3eedf62523fb63e16a809814c_78cdb59ddb" tabindex="-1"
                        value=""></div>
                <input type="submit" value="Sign Up" name="subscribe" id="mc-embedded-subscribe"
                       class="button submit-newsletter btn  btn-block">
            </div>
        </div>
    </div>
    <div class="loader" style="display: none;"><img
            src="https://www.eventbookings.com/wp-content/themes/webalive/images/ajax-loader.gif"/>
    </div>
</form>

<script>

    /**
     * mailchip
     */
    $('form.mailchimp').on('submit', function (e) {
        var $this = $(this), dataString = "action=upload_mailchimp_subscriber&" + $(this).serialize(); // Store form data
        $this.find('.loader').show();
        $this.siblings('.message').hide();
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: ajax_url,
            data: dataString,
            success: function (response) {
                // If we have response
                if (response.error) {
                    $this.siblings('.message').removeClass('success');
                    $this.siblings('.message').addClass('error');
                    $this.siblings('.message').show().html(response.error);
                } else if (response) {
                    $this.find("#email").val("");
                    $this.siblings('.message').removeClass('error');
                    $this.siblings('.message').addClass('success');
                    $this.siblings('.message').show().html('Subscribed successfully');
                } else {
                    $this.siblings('.message').removeClass('success');
                    $this.siblings('.message').addClass('error');
                    $this.siblings('.message').show().html('Not subscribed. Please try again.');
                }
                $this.find('.loader').hide();
            }
        });
        e.preventDefault();

    });
</script>


</body>
</html>