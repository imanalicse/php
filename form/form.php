<?php
if(isset($_POST) && !empty($_POST)){
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';
}
?>
<form action="" method="post">
    <input type="checkbox" name="vehicle[]" value="Bike"> I have a bike<br>
    <input type="checkbox" name="vehicle[]" value="Car"> I have a car<br>
    <input type="checkbox" name="vehicle[]" value="Boat" checked> I have a boat<br><br>
    <input type="submit" value="Submit">
</form>

