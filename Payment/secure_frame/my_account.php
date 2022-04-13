<?php
include '../../functions.php';

if(isset($_REQUEST)) {
    wa_log('my_account', 'secure_frame');
    wa_log($_REQUEST, 'secure_frame');
}