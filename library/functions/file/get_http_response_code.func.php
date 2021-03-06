<?php

/**
 * returns the HTTP response code (number) of the full path URL
 *
 * @param $url
 *
 * @return int
 */
function get_http_response_code($url)
{
    $headers = get_headers($url);

    return (int) substr($headers[0], 9, 3);
}
