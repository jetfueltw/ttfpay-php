<?php

namespace Jetfuel\Ttfpay\HttpClient;

use GuzzleHttp\Client;

class GuzzleHttpClient implements HttpClientInterface
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * GuzzleHttpClient constructor.
     *
     * @param string $baseUrl
     */
    public function __construct($baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/').'/';
        $this->client = new Client();
    }

    /**
     * GET request.
     *
     * @param string $uri
     * @param array $data
     * @return string
     */
    public function get($uri, array $data)
    {
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
           
        ];
        $response = $this->client->get($this->baseUrl.$uri, [
            'headers' => $headers,
            'query'   => $data,
        ]);
        return $response->getBody()->getContents();
    }

    /**
     * POST request.
     *
     * @param string $uri
     * @param array $data
     * @return string
     */
    public function post($uri, $data)
    {
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
            'Content-Type' => 'application/json; charset=utf-8'
        ];
        $response = $this->client->post($this->baseUrl.$uri, [
            'headers'     => $headers,
            'json' => $data,
        ]);

        return $response->getBody()->getContents();
    }

    /**
     * Form POST request.
     *
     * @param string $uri
     * @param array $data
     * @return string
     */
    public function formpost($uri, array $data)
    {
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
            'Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8'
        ];
        $response = $this->client->post($this->baseUrl.$uri, [
            'headers'     => $headers,
            'form_params' => $data,
        ]);

        return $response->getBody()->getContents();
    }
}
