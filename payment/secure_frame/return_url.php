<?php
include '../../functions.php';

if(isset($_REQUEST)) {
    wa_log('return_url', 'secure_frame');
    wa_log($_REQUEST, 'secure_frame');
}