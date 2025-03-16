<?php
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
namespace Facebook\Tests\HttpClients;

use Facebook\HttpClients\FacebookCurlHttpClient;
use Facebook\HttpClients\FacebookGuzzleHttpClient;
use Facebook\HttpClients\FacebookHttpClientInterface;
use Facebook\HttpClients\FacebookStreamHttpClient;
use Facebook\HttpClients\HttpClientsFactory;
use Facebook\Tests\BaseTestCase;
use GuzzleHttp\Client;

class HttpClientsFactoryTest extends BaseTestCase
{
    /**
     * @dataProvider httpClientsProvider
     */
    public function testCreateHttpClient(mixed $handler, string $expected)
    {
        $httpClient = HttpClientsFactory::createHttpClient($handler);

        $this->assertInstanceOf(FacebookHttpClientInterface::class, $httpClient);
        $this->assertInstanceOf($expected, $httpClient);
    }

    /**
     * @return array
     */
    public function httpClientsProvider(): array
    {
        $clients = [
          ['guzzle', FacebookGuzzleHttpClient::class],
          ['stream', FacebookStreamHttpClient::class],
          [new Client(), FacebookGuzzleHttpClient::class],
          [new FacebookGuzzleHttpClient(), FacebookGuzzleHttpClient::class],
          [new FacebookStreamHttpClient(), FacebookStreamHttpClient::class],
          [null, FacebookHttpClientInterface::class],
        ];
        if (extension_loaded('curl')) {
            $clients[] = ['curl', FacebookCurlHttpClient::class];
            $clients[] = [new FacebookCurlHttpClient(), FacebookCurlHttpClient::class];
        }

        return $clients;
    }
}
