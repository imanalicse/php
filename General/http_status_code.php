<?php
/**
 * https://en.wikipedia.org/wiki/List_of_HTTP_status_codes
 * All HTTP response status codes are separated into five classes or categories
1xx informational response – the request was received, continuing process
2xx successful – the request was successfully received, understood, and accepted
3xx redirection – further action needs to be taken in order to complete the request
4xx client error – the request contains bad syntax or cannot be fulfilled
5xx server error – the server failed to fulfil an apparently valid request
 *
 */

abstract class HttpStatusCode {

    // successful response code
    const GET_SUCCESS_CODE = 200; // OK -
    const POST_SUCCESS_CODE = 201; // Created - The request has been fulfilled, resulting in the creation of a new resource.
    const PATCH_SUCCESS_CODE = 202; // Accepted - The request has been accepted for processing, but the processing has not been completed.
    const PUT_SUCCESS_CODE = 202;
    const DELETE_SUCCESS_CODE = 204; // No Content - The server successfully processed the request, and is not returning any content.

    //Client error code
    const BAD_REQUEST_HTTP_CODE = 400; // Bad Request - The server cannot or will not process the request due to an apparent client error (e.g., malformed request syntax, size too large, invalid request message framing, or deceptive request routing).
    const UNAUTHORIZED_HTTP_CODE = 401; // Unauthorized
    const FORBIDDEN_HTTP_CODE = 403;
    const NOT_FOUND_HTTP_CODE = 404;
    const METHOD_NOT_ALLOWED_HTTP_CODE = 405;
    const CONFLICT_HTTP_CODE = 409;
    const UNPROCESSABLE_HTTP_CODE = 422;
    const FILE_TOO_LARGE_CODE = 413; // Payload Too Large - The request is larger than the server is willing or able to process.

    //Server error code
    const INTERNAL_SERVER_ERROR_HTTP_CODE = 500;
}