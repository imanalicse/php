<?php
/**
 * time — Return current Unix timestamp
 * time(): int
 * Returns the current time measured in the number of seconds since the Unix Epoch (January 1 1970 00:00:00 GMT).
 * NOTE:
 * Unix timestamps do not contain any information with regards to any local timezone.
 * It is recommended to use the DateTimeImmutable class for handling date and time information in order to
 * avoid the pitfalls that come with just Unix timestamps.
 */

echo 'Now: '. time();