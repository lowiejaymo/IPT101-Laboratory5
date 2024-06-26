<?php
declare(strict_types=1);

/**
 * Copyright 2017 Facebook, Inc.
 *
 * You are hereby granted a non-exclusive, worldwide, royalty-free license to
 * use, copy, modify, and distribute this software in source code or binary
 * form for use in connection with the web services and APIs provided by
 * Facebook.
 *
 * As with any software that integrates with the Facebook platform, your use
 * of this software is subject to the Facebook Developer Principles and
 * Policies [http://developers.facebook.com/policy/]. This copyright notice
 * shall be included in all copies or substantial portions of the software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 */

namespace Facebook\HttpClients;

use GuzzleHttp\Client;
use InvalidArgumentException;
use RuntimeException;

class HttpClientsFactory
{
    private function __construct()
    {
        // a factory constructor should never be invoked
    }

    /**
     * HTTP client generation.
     *
     * @throws InvalidArgumentException If the http client handler isn't "curl", "stream", "guzzle", or an instance of Facebook\HttpClients\FacebookHttpClientInterface.
     * @throws RuntimeException If the cURL extension or the Guzzle client aren't available (if required).
     */
    public static function createHttpClient(string|Client|FacebookHttpClientInterface|null $handler): FacebookHttpClientInterface
    {
        if (!$handler) {
            return self::detectDefaultClient();
        }

        if ($handler instanceof FacebookHttpClientInterface) {
            return $handler;
        }

        if ('stream' === $handler) {
            return new FacebookStreamHttpClient();
        }
        if ('curl' === $handler) {
            if (!extension_loaded('curl')) {
                throw new RuntimeException('The cURL extension must be loaded in order to use the "curl" handler.');
            }

            return new FacebookCurlHttpClient();
        }

        if ('guzzle' === $handler && !class_exists(Client::class)) {
            throw new RuntimeException('The Guzzle HTTP client must be included in order to use the "guzzle" handler.');
        }

        if ($handler instanceof Client) {
            return new FacebookGuzzleHttpClient($handler);
        }
        if ('guzzle' === $handler) {
            return new FacebookGuzzleHttpClient();
        }

        throw new InvalidArgumentException('The http client handler must be set to "curl", "stream", "guzzle", be an instance of GuzzleHttp\Client or an instance of Facebook\HttpClients\FacebookHttpClientInterface');
    }

    /**
     * Detect default HTTP client.
     */
    private static function detectDefaultClient(): FacebookCurlHttpClient|FacebookGuzzleHttpClient|FacebookStreamHttpClient|FacebookHttpClientInterface
    {
        if (extension_loaded('curl')) {
            return new FacebookCurlHttpClient();
        }

        if (class_exists(Client::class)) {
            return new FacebookGuzzleHttpClient();
        }

        return new FacebookStreamHttpClient();
    }
}
