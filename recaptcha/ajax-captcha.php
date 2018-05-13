<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Re Captcha</title>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
</head>

<body>

<script src='https://www.google.com/recaptcha/api.js'></script>
<div class="container justify-content-center login-container">

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" id="login-form" method="post">
        <div class="form-group row col-md-12 captcha_wrapper">
            <div class="g-recaptcha" data-sitekey="6LdPOlUUAAAAAHhcpOLCUIO1lS7W_Xeic8FhDjoO"></div>
        </div>
        <div class="form-group row justify-content-center">
            <button type="submit" class="btn btn-primary button-padding">Login</button>
        </div>
    </form>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
         $("form").submit(function (event) {
             event.preventDefault();
             var data =  $( this ).serialize();
             $.ajax({
                 type:"POST",
                 url: "validation",
                 data: data,
                 success: function(response){
                     console.log("response ", response);
                 }
             });

             return false
         });
    });
</script>

</body>
</html>