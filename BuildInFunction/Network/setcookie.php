<?php
/**
 * setcookie — Send a cookie
 * PHP cookie is a small piece of information which is stored at client browser. It is used to recognize the user.
 * Cookie is created at server side and saved to client browser. Each time when client sends request to the server, cookie is embedded with request. Such way, cookie can be received at the server side.
 * In short, cookie can be created, sent and received at server end.
 * Note: PHP Cookie must be used before <html> tag.
 * value - This value is stored on the clients computer; do not store sensitive information.
 * expires_or_options - If set to 0, or omitted, the cookie will expire at the end of the session (when the browser closes).
 */

$value = 'something from somewhere 2';
$cookie_key = 'TestCookie';
setcookie('session_cookie', 'Session Cookie value'); // will expire at the end of the session (when the browser closes).
setcookie($cookie_key, $value, time() + 3600); /* expire in 1 hour */

// Delete cookie - When deleting a cookie you should assure that the expiration date is in the past,
// to trigger the removal mechanism in your browser.
// set the expiration date to one hour ago
// setcookie($cookie_key, "", time() - 3600);