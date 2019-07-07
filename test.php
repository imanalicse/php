<?php
$arr = array(
    array(
        'id' =>'SBOX110093041',
        'refund_amount' =>'1039.5',
    ),
    array(
        'id' =>'SBOX110096404',
        'refund_amount' =>'14.6',
    )
);

echo '<pre>';
print_r(array_search('SBOX110093041', array_column($arr, 'id')));
echo '</pre>';