<!DOCTYPE html>
<html>
<head>
    <title>Page Title</title>
    <style>
        .header{
            height: 200px;
            background: green;
            width: 100%;
        }
    </style>
</head>
<body>
<div class="header">

</div>
<style>
    html, body{
        width: 100%;
        height: 100%;
        overflow: hidden;
        margin: 0;
        padding: 0;
    }
    #iframe{
        height: calc(100% - 200px);
    }
</style>
<iframe id="iframe" src="http://www.bitmascot.com" frameborder="0"  width="100%" height="100%" allowfullscreen></iframe>

</body>
</html>