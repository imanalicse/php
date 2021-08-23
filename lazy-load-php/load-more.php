<?php
$db_host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'classicmodels';
$mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);

$item_per_page = $_POST['item_per_page'];
$offset_value = $_POST['offset_value'];
sleep(2);
$query = "SELECT * FROM products LIMIT ".$offset_value.", ".$item_per_page;

$result = $mysqli->query($query);
$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
if(!empty($rows)){
    foreach ($rows as $row){
        echo '<div>'.$row['productName'].'</div>';
    }

}