<?php
include "include.php";

if(isset($_POST)) {
    wa_log('return_url', 'secure_frame');
    wa_log($_POST, 'secure_frame');
}