<?php
$db_host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'classicmodels';
$mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);



$mysqli->query('START TRANSACTION');
$mysqli->query("SELECT @orderNumber:=MAX(orderNUmber)+2 FROM orders");

//$orderNumber = $result->fetch_row()[0];
//echo '<pre>';
//print_r($orderNumber);
//echo '</pre>';


$query = "INSERT INTO orders(orderNumber,
                   orderDate,
                   requiredDate,
                   shippedDate,
                   status,
                   customerNumber)
VALUES(@orderNumber,
       '2005-05-31',
       '2005-06-10',
       '2005-06-11',
       'In Process',
        145)";

$mysqli->query($query);

$query = "INSERT INTO orderdetails(orderNumber,
                         productCode,
                         quantityOrdered,
                         priceEach,
                         orderLineNumber)
VALUES(@orderNumber,'S18_1749', 32, '136', 1),
      (@orderNumber,'S18_2248', 50, '55.09', 2)";

$mysqli->query($query);

$query = "SELECT 
            a.orderNumber,
            orderDate,
            requiredDate,
            shippedDate,
            status,
            comments,
            customerNumber,
            orderLineNumber,
            productCode,
            quantityOrdered,
            priceEach
        FROM
            orders a
                INNER JOIN
            orderdetails b USING (orderNumber)
        WHERE
            a.ordernumber = @orderNumber";

$result = $mysqli->query($query);

$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
if(!empty($rows)){
    $mysqli->query('COMMIT');
    echo '<pre>';
    print_r("COMMIT");
    echo '</pre>';
}else{
    $mysqli->query('ROLLBACK');
    echo '<pre>';
    print_r("ROLLBACK");
    echo '</pre>';
}
$mysqli->close();




