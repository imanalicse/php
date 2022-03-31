<!DOCTYPE html>
<html>
<head>
    <title>Mailchimp</title>
    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
</head>
<body>

<?php
include_once 'mailchimp-form.php';

?>

<script>
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
                    self.siblings('.message').removeClass('success');
                    self.siblings('.message').addClass('error');
                    self.siblings('.message').show().html(response.error);
                } else if (response) {
                    self.find("#email").val("");
                    self.siblings('.message').removeClass('error');
                    self.siblings('.message').addClass('success');
                    self.siblings('.message').show().html('Subscribed successfully');
                } else {
                    self.siblings('.message').removeClass('success');
                    self.siblings('.message').addClass('error');
                    self.siblings('.message').show().html('Not subscribed. Please try again.');
                }
                self.find('.loader').hide();
            }
        });
        e.preventDefault();
    });
</script>


</body>
</html>