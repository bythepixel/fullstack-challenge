<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;

class LoggableHttpWrapper
{
    public Client $guzzle;

    public function __construct(Client $client)
    {
        $this->guzzle = $client;
    }

    /**
     * @param string $type
     * @param string $uri
     * @param array $options
     * @param bool $log
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request(string $type, string $uri, array $options = [], bool $log = false): Response
    {
        if (config('app.httpLogging', false)) {
            Log::debug(print_r([
                'Logging Request',
                'type: ' . $type,
                'URI:' . print_r($uri, true),
                'Options' . print_r($options, true)
            ], true));
        }

        try {
            $response = $this->guzzle->request($type, $uri, $options);
        } catch (\Exception $exception) {
            Log::error(
                print_r(
                    [
                        'Error: ' . $exception->getMessage(),
                        'Line: ' . $exception->getLine(),
                        'File: ' . $exception->getFile()
                    ],
                    true
                )
            );
            throw $exception;
        }

        if (config('app.httpLogging', false)) {
            Log::debug(print_r([
                'response body' => $response->getBody()->getContents(),
                'headers' => $response->getHeaders(),
                'status_code' => $response->getStatusCode()
            ], true));
        }

        return $response;
    }

}
