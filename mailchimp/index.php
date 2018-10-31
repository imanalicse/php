<!DOCTYPE html>
<html>
<head>
    <title>Mailchimp</title>
    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
</head>
<body>

<?php
include_once 'mailchimp-form.php';

//add_action('wp_ajax_upload_mailchimp_subscriber', 'upload_mailchimp_subscriber');
//add_action('wp_ajax_nopriv_upload_mailchimp_subscriber', 'upload_mailchimp_subscriber');

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



?>

<script>

    /**
     * mailchip
     */
    $('form.mailchimp').on('submit', function (e) {
        var self = $(this);
        var data = self.serialize();

        $.ajax({
            type: "POST",
            //dataType: 'json',
            url: 'ajax.php',
            data: data,
            success: function (response) {

                console.log('response ', response);

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