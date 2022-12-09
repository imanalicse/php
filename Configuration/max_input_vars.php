<?php
/**
 * ini_get("max_input_vars")
 * How many input variables may be accepted (limit is applied to $_GET, $_POST and $_COOKIE superglobal separately).
 * Use of this directive mitigates the possibility of denial of service attacks which use hash collisions.
 * If there are more input variables than specified by this directive, an E_WARNING is issued,
 * and further input variables are truncated from the request.
 */
echo 'max_input_vars: '. ini_get("max_input_vars") . '<br>';