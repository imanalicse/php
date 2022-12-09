<?php
/**
 * max_file_uploads int 20 	PHP_INI_PERDIR
 * The maximum number of files allowed to be uploaded simultaneously.
 * Upload fields left blank on submission do not count towards this limit.
 */
echo ini_get("max_file_uploads") . '<br>';