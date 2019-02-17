<?php
include '../../functions.php';

if(isset($_REQUEST)) {
    wa_log('callback_url', 'secure_frame');
    wa_log($_REQUEST, 'secure_frame');
}