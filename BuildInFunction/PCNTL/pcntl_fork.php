<?php
/**
 * pcntl_fork — Forks the currently running process
 *  pcntl_fork(): int
 */

if (! function_exists('pcntl_fork')) die('PCNTL functions not available on this PHP installation');

$pid = pcntl_fork();
if ($pid == -1) {
     die('could not fork');
}
else if ($pid) {
     // we are the parent
     pcntl_wait($status); //Protect against Zombie children
}
else {
     // we are the child
}