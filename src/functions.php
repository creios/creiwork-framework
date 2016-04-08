<?php

use GuzzleHttp\Psr7\Response;

/**
 * @param Response $response
 */
function out(Response $response)
{
    header(sprintf('%s %s %s', $response->getProtocolVersion(), $response->getStatusCode(), $response->getReasonPhrase()));

    foreach ($response->getHeaders() as $name => $values) {
        foreach ($values as $value) {
            header(sprintf('%s: %s', $name, $value), false);
        }
    }

    stream_copy_to_stream(\GuzzleHttp\Psr7\StreamWrapper::getResource($response->getBody()), fopen('php://output', 'w'));
}
