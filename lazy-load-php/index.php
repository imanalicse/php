<!DOCTYPE html>
<html>
<head>
    <title>  </title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<style>
    .flex-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }
    .flex-container > div {
        background-color: #f1f1f1;
        margin: 10px;
        padding: 20px;
        width: 500px;
        height: 500px;
    }
</style>
<body>


    <div class="flex-container load-appender">

    </div>
    <div class="loading" style="display: none; text-align: center"><img src="loading.gif"></div>

</body>
</html>

<script>
    var total_item = 110;
    var item_per_page = 9;
    var offset_value = 0;
    var load_data = true;
    LoadMore();
    jQuery(window).scroll(function($) {
        if(offset_value < total_item && jQuery(window).scrollTop() + jQuery(window).height() == jQuery(document).height() && load_data==true) {
            LoadMore();
        }
    });

    function LoadMore() {
        jQuery.ajax({
            type: 'POST',
            url: 'load-more.php',
            data: {
                item_per_page: item_per_page,
                offset_value: offset_value
            },
            beforeSend: function () {
                load_data = false;
                $(".loading").show();
            },
            success: function(resp){
                if(resp) {
                    $(".load-appender").append(resp);
                    offset_value = offset_value + item_per_page;
                    load_data = true;
                }
            },
            complete: function () {
                $(".loading").hide();
            }
        });
    }


</script>