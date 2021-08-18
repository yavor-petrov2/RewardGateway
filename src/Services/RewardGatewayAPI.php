<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class RewardGatewayAPI
{
    const ENDPOINT_URL = 'https://hiring.rewardgateway.net/list';
    const REQUEST_METHOD = 'GET';
    const USER = 'hard';
    const PASS = 'hard';

    protected $client;
    protected $responseStatus;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Make a call to RewardGateway API to extract data
     *
     * @return ResponseInterface
     * @throws TransportExceptionInterface
     */
    public function call(): ResponseInterface
    {
        return $this->client->request(
            self::REQUEST_METHOD,
            self::ENDPOINT_URL,
            $this->buildHeaders()
        );
    }

    /**
     * Generate request headers
     *
     * @return array
     */
    protected function buildHeaders(): array
    {
        $headers = [];
        $headers['auth_basic'] = [
            self::USER,
            self::PASS
        ];

        return $headers;
    }

    /**
     * Handle API failure and return proper array
     *
     * @return array
     */
    public function getData(): array
    {
        try {
            $response = $this->call();
            $content = $response->getContent();
        } catch (ClientExceptionInterface |
        RedirectionExceptionInterface |
        ServerExceptionInterface |
        TransportExceptionInterface |
        \Exception $e) {
            return [
                'error' => 'Error extracting data. Please try again.'
            ];
        }

        $content = $this->hydrateContent($content);

        return [
            'people' => $content
        ];
    }

    /**
     * Handle API failure and return proper JsonResponse
     * Used for AJAX requests
     *
     * @return JsonResponse
     */
    /*public function getJsonData(): JsonResponse
    {
        $this->responseStatus = 500;
        try {
            $response = $this->call();
            $content = $response->getContent();
            $this->responseStatus = $response->getStatusCode();
        } catch (ClientExceptionInterface |
            RedirectionExceptionInterface |
            ServerExceptionInterface |
            TransportExceptionInterface |
            \Exception $e) {

            return new JsonResponse(
                'Error extracting data',
                $this->responseStatus
            );
        }

        return new JsonResponse($content, 200);
    }*/

    protected function hydrateContent($content)
    {
        $content = json_decode($content);
        foreach ($content as $key => $person) {
            // Fix missing images
            // This is causing additional requests, so we cover this fix in the twig
            // Also the lorempixel.com is very slow, so it takes too much time to validate
            /*if (curl_init($person->avatar) !== false) {
                $person->avatar = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAADsklEQVRYR63XdwincxwH8NeZGWXGnRWZiSJ/kGRlJMqI0+FynSI7skpmnGzJzChd54pkZkUJ2WVE6cqR7BHK3r2v76Pnfvc83+e5zqd+f/z6fcb795nv7xTjZXUciL2xAzbHGvgHP+ADvIln8QR+GuN6ygilzXAOZuIPPIc38CG+L/ZrYgvshD2xHO7BVfi4FqMGYGWcj3PxMm7A4/h9AHTsDsKZBdAcXFHAL2HaB2ATPIB1cHJJ6YhkLaYS3wFyEz7D4fh00kkXgG3wDF7HrFLfLrv0wMblh/fwVQ/CtTAX22NfLGjrTQLIP38JT+IE/DXhNPqn4qxW8Kj8jWtxIX7tALIC7ir9sWs7E20Aqd2L+KSkazJ4/N6Mkyq1+BkX4+oeEA9hbezR9EQbwKU4Bjv2pD0dnu4fIweULE7qJvhbuB2X58cGQEbtfRxSabh08nljouNeHN2jmxjzsWWy3QC4FduW1PTFuL+UZgyG17Bzj2Jips/S5KflSzbcFyX9qVGfPFgyNAbAq9ilojgdd2BqAByJ27D+wJK5E8eNiY78kUMruquUsZ0ZAGmI9QYM4uvsslrHYLikTENNN1t1YQAkXY80XVmx2A9PjYmO/fH0gG6mbq8A+CbNUDq3ZrM0JUhJTxwAcGxuRADkwh1cDk3NJqU6fmQGsv+zMWuSmPMDINcts5ma1CSLKCXIgarJ19gH7wzoJea8AIjB6SNKEH/Ry1muSa7nLSMylUM3JwBeKf8+TTEkOVYLsXyP4p/YtOvsduhfht0DIFtwg9IHQwDy+3U4o0fx+kJExvjJxV0QACEKOZXZBb+NsEwPZHK6JMusjxe09VctpZ8RAKuVVZyahAUNSS7atz1KU/HlkAPMKH0yrTlGGZuc4d0Ky635SI1DSLtk60nG06GUmDlWL6RcDYA0V6jSEXi0Ej3MJmz3qB6d+8pRy27pk5Q8PsKiP28TktCpHJtw/u8mrBM4cxudcLuavIvcglzPSVa1Lt7GjbgyTtoAVsTzpb4JlpHKdISCzca0EbVtq4QB311qnXMf/48hlzCPm/hfDEC+b1jIQuoTo1MQrrgsEpKa5ZXeCUkJKY3vRdJFy7cqr590+7IGb+JkvDO6IaN5wv0nfQ+TZOJhbPc/gPilENHD2v+8loHmt9QsJPSCMporLWUdfix1v6i8GRbVfFLGPE43KkDSiAEVm9otSIzUPZzvmvIs68U+BkBjnO4N08knhDNNlS0ayYPko/KwCRPKp+uFtASQfwFMNb0ytvWaBAAAAABJRU5ErkJggg==';
            }*/

            $person->name = $this->clearHTMLTags($person->name);
            $person->bio = $this->clearHTMLTags($person->bio);
            $person->title = $this->clearHTMLTags($person->title);
            $person->company = $this->clearHTMLTags($person->company);
        }

        return $content;
    }

    /**
     * Clear javascript and HTML tags
     *
     * @param $str
     * @return string
     */
    protected function clearHTMLTags($str): string
    {
        // remove all script tags from bio
        $str = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $str);
        // strip all additional tags
        $str = strip_tags($str);
        // fix 0 strings
        if (empty($str)) {
            $str = '';
        }
        return $str;
    }

}