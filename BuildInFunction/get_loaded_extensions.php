<?php
/**
 * get_loaded_extensions — Returns an array with the names of all modules compiled and loaded
 *  get_loaded_extensions(bool $zend_extensions = false): array
 */

 $extensions = get_loaded_extensions();
 echo "<pre>";
 print_r($extensions);
 echo "</pre>";